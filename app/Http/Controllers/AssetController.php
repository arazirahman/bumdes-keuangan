<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\UnitUsaha;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $q = Asset::with(['unitUsaha', 'creator'])->orderBy('id', 'desc');

        if ($request->filled('unit_usaha_id')) {
            $q->where('unit_usaha_id', (int)$request->unit_usaha_id);
        }

        $rows = $q->paginate(15)->withQueryString();

        $totalValue = (int) Asset::query()
            ->when($request->filled('unit_usaha_id'), fn($qq) => $qq->where('unit_usaha_id', (int)$request->unit_usaha_id))
            ->selectRaw('SUM(unit_cost * qty) as total')
            ->value('total') ?? 0;

        return view('assets.index', [
            'title' => 'Aset/Inventaris',
            'rows' => $rows,
            'units' => UnitUsaha::orderBy('name')->get(),
            'totalValue' => $totalValue,
        ]);
    }

    public function create()
    {
        return view('assets.create', [
            'title' => 'Tambah Aset',
            'units' => UnitUsaha::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'acquired_date' => ['nullable', 'date'],
            'unit_cost' => ['required', 'integer', 'min:0'],
            'qty' => ['required', 'integer', 'min:1'],
            'condition' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string'],
            'unit_usaha_id' => ['nullable', 'exists:unit_usahas,id'],
        ]);

        Asset::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('assets.index')->with('ok', 'Aset berhasil ditambahkan.');
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', [
            'title' => 'Edit Aset',
            'row' => $asset,
            'units' => UnitUsaha::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'acquired_date' => ['nullable', 'date'],
            'unit_cost' => ['required', 'integer', 'min:0'],
            'qty' => ['required', 'integer', 'min:1'],
            'condition' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string'],
            'unit_usaha_id' => ['nullable', 'exists:unit_usahas,id'],
        ]);

        $asset->update($data);

        return redirect()->route('assets.index')->with('ok', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Akses ditolak.');
        }

        $asset->delete();

        return redirect()->route('assets.index')->with('ok', 'Aset berhasil dihapus.');
    }
}
