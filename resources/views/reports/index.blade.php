<x-layouts.app :title="$title">
  <div class="rounded-2xl bg-white p-6 shadow">
    <form class="mb-6 flex flex-wrap items-end gap-3">
      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Bulan</label>
        <input type="month" name="month" value="{{ $report['month'] }}"
          class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Unit Usaha (opsional)</label>
        <select name="unit_usaha_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
          <option value="">Semua Unit</option>
          @foreach($units as $u)
            <option value="{{ $u->id }}" @selected((string)$u->id === (string)request('unit_usaha_id'))>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>

      <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tampilkan</button>

      <a href="{{ route('reports.pdf', request()->query()) }}"
         class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
        Cetak PDF
      </a>

      <a href="{{ route('reports.excel', request()->query()) }}"
         class="rounded-xl border px-4 py-2 text-sm">
        Unduh Excel
      </a>
    </form>

    <div class="grid gap-4 lg:grid-cols-3">
      {{-- A) Laba Rugi --}}
      <div class="rounded-2xl border p-5">
        <div class="text-sm font-semibold text-slate-700">A. Laporan Laba Rugi</div>
        <div class="mt-3 space-y-2 text-sm">
          <div class="flex justify-between"><span>Pendapatan</span><b>Rp {{ number_format($report['pendapatan'],0,',','.') }}</b></div>
          <div class="flex justify-between"><span>Biaya</span><b>Rp {{ number_format($report['biaya'],0,',','.') }}</b></div>
          <div class="mt-2 border-t pt-2 flex justify-between">
            <span><b>Laba Bersih</b></span>
            <b>Rp {{ number_format($report['laba_bersih'],0,',','.') }}</b>
          </div>
        </div>
      </div>

      {{-- B) Arus Kas --}}
      <div class="rounded-2xl border p-5">
        <div class="text-sm font-semibold text-slate-700">B. Laporan Arus Kas</div>
        <div class="mt-3 space-y-2 text-sm">
          <div class="flex justify-between"><span>Uang Masuk</span><b>Rp {{ number_format($report['uang_masuk'],0,',','.') }}</b></div>
          <div class="flex justify-between"><span>Uang Keluar</span><b>Rp {{ number_format($report['uang_keluar'],0,',','.') }}</b></div>
          <div class="mt-2 border-t pt-2 flex justify-between">
            <span><b>Saldo Akhir</b></span>
            <b>Rp {{ number_format($report['saldo_akhir'],0,',','.') }}</b>
          </div>
        </div>
      </div>

      {{-- C) Neraca --}}
      <div class="rounded-2xl border p-5">
        <div class="text-sm font-semibold text-slate-700">C. Neraca Sederhana</div>
        <div class="mt-3 space-y-2 text-sm">
          <div class="flex justify-between"><span>Kas</span><b>Rp {{ number_format($report['kas'],0,',','.') }}</b></div>
          <div class="flex justify-between"><span>Aset Lain</span><b>Rp {{ number_format($report['aset_lain'],0,',','.') }}</b></div>
          <div class="flex justify-between border-t pt-2"><span><b>Total Aset</b></span><b>Rp {{ number_format($report['total_aset'],0,',','.') }}</b></div>

          <div class="mt-4 text-xs text-slate-500">
            Modal dihitung otomatis dari kategori pemasukan yang mengandung kata “Modal”.
          </div>

          <div class="mt-2 flex justify-between"><span>Modal</span><b>Rp {{ number_format($report['modal'],0,',','.') }}</b></div>
        </div>
      </div>
    </div>
  </div>
</x-layouts.app>
