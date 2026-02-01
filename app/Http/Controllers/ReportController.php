<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Asset;
use App\Models\UnitUsaha;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private function buildReport(Request $request): array
    {
        // filter bulan: YYYY-MM
        $month = $request->get('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
        $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();

        // filter unit usaha (optional)
        $unitId = $request->get('unit_usaha_id');

        $baseQuery = Transaction::query()
            ->whereBetween('trx_date', [$start, $end]);

        if ($unitId) {
            $baseQuery->where('unit_usaha_id', (int)$unitId);
        }

        // =========================
        // a) LABA RUGI
        // =========================
        $pendapatan = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $biaya      = (clone $baseQuery)->where('type', 'expense')->sum('amount');
        $labaBersih = $pendapatan - $biaya;

        // =========================
        // b) ARUS KAS
        // =========================
        $uangMasuk  = $pendapatan;
        $uangKeluar = $biaya;

        // saldo akhir = total income - total expense (global)
        // kalau unit dipilih → saldo akhir berdasarkan unit itu
        $saldoQuery = Transaction::query();
        if ($unitId) {
            $saldoQuery->where('unit_usaha_id', (int)$unitId);
        }

        $saldoAkhir = (clone $saldoQuery)->where('type', 'income')->sum('amount')
            - (clone $saldoQuery)->where('type', 'expense')->sum('amount');

        // =========================
        // c) NERACA SEDERHANA
        // =========================
        // Modal = total pemasukan kategori yang mengandung kata "Modal"
        $modalQuery = Transaction::query()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.type', 'income')
            ->where('categories.name', 'like', '%Modal%');

        if ($unitId) {
            $modalQuery->where('transactions.unit_usaha_id', (int)$unitId);
        }

        $modal = (int) $modalQuery->sum('transactions.amount');

        // Kas = saldo akhir
        $kas = (int) $saldoAkhir;

        // Aset lain = total aset inventaris (unit_cost * qty)
        $asetLain = (int) (Asset::query()
            ->when($unitId, fn($q) => $q->where('unit_usaha_id', (int)$unitId))
            ->selectRaw('COALESCE(SUM(unit_cost * qty), 0) as total')
            ->value('total') ?? 0);

        // Total aset = kas + aset lain
        $totalAset = $kas + $asetLain;

        // Ekuitas (versi sederhana)
        // Catatan: agar benar-benar seimbang butuh konsep saldo laba ditahan.
        $ekuitas = $modal;

        return [
            'month' => $month,
            'start' => $start,
            'end' => $end,
            'unit_usaha_id' => $unitId,

            // Laba Rugi
            'pendapatan' => (int)$pendapatan,
            'biaya' => (int)$biaya,
            'laba_bersih' => (int)$labaBersih,

            // Arus Kas
            'uang_masuk' => (int)$uangMasuk,
            'uang_keluar' => (int)$uangKeluar,
            'saldo_akhir' => (int)$saldoAkhir,

            // Neraca
            'kas' => (int)$kas,
            'aset_lain' => (int)$asetLain,
            'total_aset' => (int)$totalAset,
            'modal' => (int)$modal,
            'ekuitas' => (int)$ekuitas,
        ];
    }

    public function index(Request $request)
    {
        $report = $this->buildReport($request);

        $units = UnitUsaha::orderBy('name')->get();

        return view('reports.index', [
            'title' => 'Laporan Keuangan',
            'report' => $report,
            'units' => $units,
        ]);
    }

    public function pdf(Request $request)
    {
        $report = $this->buildReport($request);

        $pdf = Pdf::loadView('reports.pdf', [
            'title' => 'Laporan Keuangan',
            'report' => $report,
        ])->setPaper('a4', 'portrait');

        $filename = 'Laporan_Keuangan_' . $report['month'] . '.pdf';
        return $pdf->download($filename);
    }

    public function excel(Request $request)
    {
        $report = $this->buildReport($request);
        $filename = 'Laporan_Keuangan_' . $report['month'] . '.xlsx';

        return Excel::download(new ReportsExport($report), $filename);
    }
}
