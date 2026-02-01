<x-layouts.app :title="$title">
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl bg-white p-5 shadow">
      <div class="text-sm text-slate-500">Saldo Kas</div>
      <div class="mt-2 text-xl font-bold">Rp {{ number_format($saldo,0,',','.') }}</div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow">
      <div class="text-sm text-slate-500">Pemasukan ({{ $month }})</div>
      <div class="mt-2 text-xl font-bold">Rp {{ number_format($income,0,',','.') }}</div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow">
      <div class="text-sm text-slate-500">Pengeluaran ({{ $month }})</div>
      <div class="mt-2 text-xl font-bold">Rp {{ number_format($expense,0,',','.') }}</div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow">
      <div class="text-sm text-slate-500">Laba/Rugi ({{ $month }})</div>
      <div class="mt-2 text-xl font-bold">Rp {{ number_format($profit,0,',','.') }}</div>
    </div>
  </div>

  <div class="mt-6 rounded-2xl bg-white p-6 shadow">
    <form class="flex flex-wrap items-end gap-3">
      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Bulan</label>
        <input type="month" name="month" value="{{ $month }}"
          class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
      </div>
      <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tampilkan</button>
    </form>
  </div>
</x-layouts.app>
