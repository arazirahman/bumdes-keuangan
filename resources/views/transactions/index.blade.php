<x-layouts.app :title="$title">
  <div class="mb-4 rounded-2xl bg-white p-5 shadow">
    <div class="text-sm text-slate-500">Saldo Kas Saat Ini</div>
    <div class="mt-1 text-xl font-bold">Rp {{ number_format($saldo,0,',','.') }}</div>
  </div>

  <div class="rounded-2xl bg-white p-6 shadow">
    <form class="mb-4 flex flex-wrap gap-3">
      <select name="type" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        <option value="">Semua Jenis</option>
        <option value="income" @selected(request('type')==='income')>Pemasukan</option>
        <option value="expense" @selected(request('type')==='expense')>Pengeluaran</option>
      </select>

      <select name="unit_usaha_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        <option value="">Semua Unit</option>
        @foreach($units as $u)
          <option value="{{ $u->id }}" @selected((string)$u->id===request('unit_usaha_id'))>{{ $u->name }}</option>
        @endforeach
      </select>

      <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
      <a href="{{ route('transactions.index') }}" class="rounded-xl border px-4 py-2 text-sm">Reset</a>
    </form>

    <div class="overflow-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b bg-slate-50 text-left">
            <th class="p-2">Tgl</th>
            <th class="p-2">Jenis</th>
            <th class="p-2">Unit</th>
            <th class="p-2">Kategori</th>
            <th class="p-2">Keterangan</th>
            <th class="p-2 text-right">Nominal</th>
            <th class="p-2">Bukti</th>
            <th class="p-2">Aksi</th>
          </tr>
        </thead>

        <tbody>
        @forelse($rows as $r)
          <tr class="border-b">
            <td class="p-2">{{ $r->trx_date->format('d/m/Y') }}</td>

            <td class="p-2">
              <span class="rounded-lg px-2 py-1 text-xs {{ $r->type==='income' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                {{ $r->type==='income' ? 'Masuk' : 'Keluar' }}
              </span>
            </td>

            <td class="p-2">{{ $r->unitUsaha?->name }}</td>
            <td class="p-2">{{ $r->category?->name }}</td>
            <td class="p-2">{{ $r->description }}</td>

            <td class="p-2 text-right">Rp {{ number_format($r->amount,0,',','.') }}</td>

            <td class="p-2">
              @if($r->proof_path)
                <a class="text-indigo-700 underline" target="_blank" href="{{ asset('storage/'.$r->proof_path) }}">Lihat</a>
              @else
                <span class="text-slate-400">-</span>
              @endif
            </td>

            {{-- AKSI --}}
            <td class="p-2">
              <div class="flex flex-wrap gap-2">
                {{-- Edit: operator & superadmin --}}
                @if(in_array(auth()->user()->role, ['operator','superadmin']))
                  <a href="{{ route('transactions.edit', $r->id) }}" class="text-indigo-700 underline">Edit</a>
                @else
                  <span class="text-slate-400">-</span>
                @endif

                {{-- Hapus: hanya superadmin --}}
                @if(auth()->user()->role === 'superadmin')
                  <form method="POST" action="{{ route('transactions.destroy', $r->id) }}"
                        onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-700 underline">Hapus</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="p-3 text-slate-500">Belum ada transaksi.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
  </div>
</x-layouts.app>
