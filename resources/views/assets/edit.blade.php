<x-layouts.app :title="$title">
  <div class="rounded-2xl bg-white p-6 shadow">
    <form method="POST" action="{{ route('assets.update', $row->id) }}" class="grid gap-4 sm:grid-cols-2">
      @csrf
      @method('PUT')

      <div class="sm:col-span-2">
        <label class="mb-1 block text-sm font-semibold text-slate-700">Nama Aset</label>
        <input name="name" value="{{ old('name', $row->name) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
        @error('name') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Unit Usaha (opsional)</label>
        <select name="unit_usaha_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
          <option value="">-</option>
          @foreach($units as $u)
            <option value="{{ $u->id }}" @selected((string)$u->id===old('unit_usaha_id', (string)$row->unit_usaha_id))>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Tanggal Perolehan</label>
        <input type="date" name="acquired_date" value="{{ old('acquired_date', $row->acquired_date?->toDateString()) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Harga Satuan</label>
        <input type="number" min="0" name="unit_cost" value="{{ old('unit_cost', $row->unit_cost) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Qty</label>
        <input type="number" min="1" name="qty" value="{{ old('qty', $row->qty) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Kondisi</label>
        <input name="condition" value="{{ old('condition', $row->condition) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Lokasi</label>
        <input name="location" value="{{ old('location', $row->location) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
      </div>

      <div class="sm:col-span-2">
        <label class="mb-1 block text-sm font-semibold text-slate-700">Catatan</label>
        <textarea name="note" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" rows="3">{{ old('note', $row->note) }}</textarea>
      </div>

      <div class="sm:col-span-2 flex gap-2">
        <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
        <a href="{{ route('assets.index') }}" class="rounded-xl border px-4 py-2 text-sm">Kembali</a>
      </div>
    </form>
  </div>
</x-layouts.app>
