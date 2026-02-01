<x-layouts.app :title="$title">
  <div class="mb-4 rounded-2xl bg-white p-5 shadow">
    <div class="text-sm text-slate-500">Total Nilai Aset</div>
    <div class="mt-1 text-xl font-bold">Rp {{ number_format($totalValue,0,',','.') }}</div>
  </div>

  <div class="rounded-2xl bg-white p-6 shadow">
    <form class="mb-4 flex flex-wrap gap-3">
      <select name="unit_usaha_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        <option value="">Semua Unit</option>
        @foreach($units as $u)
          <option value="{{ $u->id }}" @selected((string)$u->id===request('unit_usaha_id'))>{{ $u->name }}</option>
        @endforeach
      </select>

      <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
      <a href="{{ route('assets.index') }}" class="rounded-xl border px-4 py-2 text-sm">Reset</a>

      <a href="{{ route('assets.create') }}" class="ml-auto rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
        + Tambah Aset
      </a>
    </form>

    <div class="overflow-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b bg-slate-50 text-left">
            <th class="p-2">Nama</th>
            <th class="p-2">Unit</th>
            <th class="p-2">Tgl Perolehan</th>
            <th class="p-2 text-right">Harga</th>
            <th class="p-2 text-right">Qty</th>
            <th class="p-2 text-right">Total</th>
            <th class="p-2">Kondisi</th>
            <th class="p-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($rows as $r)
          <tr class="border-b">
            <td class="p-2">{{ $r->name }}</td>
            <td class="p-2">{{ $r->unitUsaha?->name ?? '-' }}</td>
            <td class="p-2">{{ $r->acquired_date?->format('d/m/Y') ?? '-' }}</td>
            <td class="p-2 text-right">Rp {{ number_format($r->unit_cost,0,',','.') }}</td>
            <td class="p-2 text-right">{{ $r->qty }}</td>
            <td class="p-2 text-right">Rp {{ number_format($r->total_value,0,',','.') }}</td>
            <td class="p-2">{{ $r->condition ?? '-' }}</td>
           <td class="p-2">
  <div class="flex gap-2">
    <a class="text-indigo-700 underline" href="{{ route('assets.edit', $r->id) }}">Edit</a>

    @if(auth()->user()->role === 'superadmin')
      <form method="POST" action="{{ route('assets.destroy', $r->id) }}"
            onsubmit="return confirm('Hapus aset ini?');">
        @csrf
        @method('DELETE')
        <button class="text-rose-700 underline" type="submit">Hapus</button>
      </form>
    @endif
  </div>
</td>

          </tr>
        @empty
          <tr><td colspan="8" class="p-3 text-slate-500">Belum ada aset.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
  </div>
</x-layouts.app>
