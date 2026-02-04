<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Asset;
use App\Models\UnitUsaha;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private function buildReport(Request $request): array
    {
        $user = $request->user();

        // bulan
        $month = $request->get('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
        $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();

        // scope desa:
        // operator -> paksa desa miliknya
        // superadmin -> boleh pilih desa (optional), kalau kosong -> semua desa
        $villageId = null;
        if ($user->role === 'operator') {
            $villageId = $user->village_id;
        } elseif ($request->filled('village_id')) {
            $villageId = (int)$request->village_id;
        }

        // unit usaha optional
        $unitId = $request->get('unit_usaha_id');

        $baseQuery = Transaction::query()
            ->whereBetween('trx_date', [$start, $end])
            ->when($unitId, fn($q) => $q->where('unit_usaha_id', (int)$unitId))
            ->when($villageId, fn($q) => $q->where('village_id', (int)$villageId));

        $pendapatan = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $biaya      = (clone $baseQuery)->where('type', 'expense')->sum('amount');
        $labaBersih = $pendapatan - $biaya;

        $uangMasuk  = $pendapatan;
        $uangKeluar = $biaya;

        // saldo akhir kumulatif (ikut scope desa)
        $saldoQuery = Transaction::query()
            ->when($unitId, fn($q) => $q->where('unit_usaha_id', (int)$unitId))
            ->when($villageId, fn($q) => $q->where('village_id', (int)$villageId));

        $saldoAkhir = (clone $saldoQuery)->where('type', 'income')->sum('amount')
            - (clone $saldoQuery)->where('type', 'expense')->sum('amount');

        // modal (kategori mengandung "Modal") ikut scope desa
        $modalQuery = Transaction::query()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.type', 'income')
            ->where('categories.name', 'like', '%Modal%')
            ->when($unitId, fn($q) => $q->where('transactions.unit_usaha_id', (int)$unitId))
            ->when($villageId, fn($q) => $q->where('transactions.village_id', (int)$villageId));

        $modal = (int) $modalQuery->sum('transactions.amount');

        $kas = (int) $saldoAkhir;

        $asetLain = (int) (Asset::query()
            ->when($unitId, fn($q) => $q->where('unit_usaha_id', (int)$unitId))
            ->when($villageId, fn($q) => $q->where('village_id', (int)$villageId))
            ->selectRaw('COALESCE(SUM(unit_cost * qty), 0) as total')
            ->value('total') ?? 0);

        $totalAset = $kas + $asetLain;

        return [
            'month' => $month,
            'start' => $start,
            'end' => $end,
            'unit_usaha_id' => $unitId,
            'village_id' => $villageId,

            'pendapatan' => (int)$pendapatan,
            'biaya' => (int)$biaya,
            'laba_bersih' => (int)$labaBersih,

            'uang_masuk' => (int)$uangMasuk,
            'uang_keluar' => (int)$uangKeluar,
            'saldo_akhir' => (int)$saldoAkhir,

            'kas' => (int)$kas,
            'aset_lain' => (int)$asetLain,
            'total_aset' => (int)$totalAset,
            'modal' => (int)$modal,
        ];
    }

    public function index(Request $request)
    {
        $report = $this->buildReport($request);

        return view('reports.index', [
            'title' => 'Laporan Keuangan',
            'report' => $report,
            'units' => UnitUsaha::orderBy('name')->get(),
            'villages' => Village::orderBy('name')->get(),
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
