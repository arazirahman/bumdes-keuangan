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
        $user = $request->user();

        $q = Transaction::with(['unitUsaha', 'category', 'creator', 'village'])
            ->orderBy('trx_date', 'desc')
            ->orderBy('id', 'desc');

        // SCOPE DESA: operator hanya lihat data desanya
        if ($user->role === 'operator') {
            $q->where('village_id', $user->village_id);
        } else {
            // superadmin boleh filter desa (optional dari request)
            if ($request->filled('village_id')) {
                $q->where('village_id', (int)$request->village_id);
            }
        }

        // filter lain
        if ($request->filled('type')) {
            $q->where('type', $request->string('type'));
        }

        if ($request->filled('unit_usaha_id')) {
            $q->where('unit_usaha_id', $request->integer('unit_usaha_id'));
        }

        $rows = $q->paginate(15)->withQueryString();

        // saldo: ikut scope desa
        $saldoQ = Transaction::query();
        if ($user->role === 'operator') {
            $saldoQ->where('village_id', $user->village_id);
        } elseif ($request->filled('village_id')) {
            $saldoQ->where('village_id', (int)$request->village_id);
        }

        $saldo = (clone $saldoQ)->where('type', 'income')->sum('amount')
            - (clone $saldoQ)->where('type', 'expense')->sum('amount');

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
            'proof' => ['nullable', 'image', 'max:3072'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('proofs', 'public');
        }

        Transaction::create([
            'trx_date' => $data['trx_date'],
            'type' => $data['type'],
            'unit_usaha_id' => $data['unit_usaha_id'],
            'village_id' => $request->user()->role === 'operator'
                ? $request->user()->village_id
                : ($request->input('village_id') ?: null), // superadmin bisa null/atau nanti ditambahkan pilihan
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'proof_path' => $proofPath,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('transactions.index')->with('ok', 'Transaksi berhasil disimpan.');
    }

    public function edit(Transaction $transaction)
    {
        // operator hanya boleh edit data desanya
        if (auth()->user()->role === 'operator' && $transaction->village_id !== auth()->user()->village_id) {
            abort(403, 'Akses ditolak.');
        }

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
        // operator hanya boleh update data desanya
        if ($request->user()->role === 'operator' && $transaction->village_id !== $request->user()->village_id) {
            abort(403, 'Akses ditolak.');
        }

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

        if ($request->boolean('remove_proof') && $transaction->proof_path) {
            Storage::disk('public')->delete($transaction->proof_path);
            $transaction->proof_path = null;
        }

        if ($request->hasFile('proof')) {
            if ($transaction->proof_path) {
                Storage::disk('public')->delete($transaction->proof_path);
            }
            $transaction->proof_path = $request->file('proof')->store('proofs', 'public');
        }

        $transaction->update([
            'trx_date' => $data['trx_date'],
            'type' => $data['type'],
            'unit_usaha_id' => $data['unit_usaha_id'],
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'proof_path' => $transaction->proof_path,
        ]);

        return redirect()->route('transactions.index')->with('ok', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->proof_path) {
            Storage::disk('public')->delete($transaction->proof_path);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('ok', 'Transaksi berhasil dihapus.');
    }
}
