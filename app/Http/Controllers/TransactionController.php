<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\UnitUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $q = Transaction::with(['unitUsaha', 'category', 'creator'])
            ->orderBy('trx_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('type')) {
            $q->where('type', $request->string('type'));
        }
        if ($request->filled('unit_usaha_id')) {
            $q->where('unit_usaha_id', $request->integer('unit_usaha_id'));
        }

        $rows = $q->paginate(15)->withQueryString();

        // saldo berjalan (sederhana: hitung total global)
        $saldo = Transaction::where('type', 'income')->sum('amount') - Transaction::where('type', 'expense')->sum('amount');

        return view('transactions.index', [
            'title' => 'Transaksi',
            'rows' => $rows,
            'saldo' => $saldo,
            'units' => UnitUsaha::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('transactions.create', [
            'title' => 'Tambah Transaksi',
            'units' => UnitUsaha::where('is_active', true)->orderBy('name')->get(),
            'incomeCats' => Category::where('type', 'income')->orderBy('name')->get(),
            'expenseCats' => Category::where('type', 'expense')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trx_date' => ['required', 'date'],
            'type' => ['required', 'in:income,expense'],
            'unit_usaha_id' => ['required', 'exists:unit_usahas,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'proof' => ['nullable', 'image', 'max:3072'], // max 3MB
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('proofs', 'public'); // storage/app/public/proofs
        }

        Transaction::create([
            'trx_date' => $data['trx_date'],
            'type' => $data['type'],
            'unit_usaha_id' => $data['unit_usaha_id'],
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'proof_path' => $proofPath,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('transactions.index')->with('ok', 'Transaksi berhasil disimpan.');
    }

    public function destroy(Transaction $transaction)
    {
        // hapus file bukti jika ada
        if ($transaction->proof_path) {
            Storage::disk('public')->delete($transaction->proof_path);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('ok', 'Transaksi berhasil dihapus.');
    }

    public function edit(Transaction $transaction)
    {
        return view('transactions.edit', [
            'title' => 'Edit Transaksi',
            'row' => $transaction->load(['unitUsaha', 'category']),
            'units' => UnitUsaha::where('is_active', true)->orderBy('name')->get(),
            'incomeCats' => Category::where('type', 'income')->orderBy('name')->get(),
            'expenseCats' => Category::where('type', 'expense')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'trx_date' => ['required', 'date'],
            'type' => ['required', 'in:income,expense'],
            'unit_usaha_id' => ['required', 'exists:unit_usahas,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'proof' => ['nullable', 'image', 'max:3072'],
            'remove_proof' => ['nullable', 'boolean'],
        ]);

        // hapus bukti lama kalau dicentang
        if ($request->boolean('remove_proof') && $transaction->proof_path) {
            Storage::disk('public')->delete($transaction->proof_path);
            $transaction->proof_path = null;
        }

        // upload bukti baru (replace)
        if ($request->hasFile('proof')) {
            if ($transaction->proof_path) {
                Storage::disk('public')->delete($transaction->proof_path);
            }
            $transaction->proof_path = $request->file('proof')->store('proofs', 'public');
        }

        $transaction->fill([
            'trx_date' => $data['trx_date'],
            'type' => $data['type'],
            'unit_usaha_id' => $data['unit_usaha_id'],
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
        ])->save();

        return redirect()->route('transactions.index')->with('ok', 'Transaksi berhasil diperbarui.');
    }
}
