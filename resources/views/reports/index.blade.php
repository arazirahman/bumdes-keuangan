<x-layouts.app :title="$title">
  <div class="rounded-2xl bg-white p-6 shadow">
    <form class="flex flex-wrap items-end gap-3">

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Bulan</label>
        <input type="month" name="month" value="{{ $report['month'] }}"
          class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
      </div>

      {{-- Filter Desa: hanya superadmin --}}
      @if(auth()->user()->role === 'superadmin')
        <div>
          <label class="mb-1 block text-sm font-semibold text-slate-700">Desa</label>
          <select name="village_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="">Semua Desa</option>
            @foreach($villages as $v)
              <option value="{{ $v->id }}" @selected((string)$v->id === request('village_id'))>
                {{ $v->name }}
              </option>
            @endforeach
          </select>
        </div>
      @endif

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Unit Usaha</label>
        <select name="unit_usaha_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
          <option value="">Semua Unit</option>
          @foreach($units as $u)
            <option value="{{ $u->id }}" @selected((string)$u->id === request('unit_usaha_id'))>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>

      <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tampilkan</button>

      <a href="{{ route('reports.pdf', request()->query()) }}"
         class="ml-auto rounded-xl border bg-white px-4 py-2 text-sm hover:bg-slate-50">
        Cetak PDF
      </a>

      <a href="{{ route('reports.excel', request()->query()) }}"
         class="rounded-xl border bg-white px-4 py-2 text-sm hover:bg-slate-50">
        Unduh Excel
      </a>
    </form>
  </div>

  <div class="mt-6 grid gap-4 lg:grid-cols-3">
    <div class="rounded-2xl bg-white p-6 shadow">
      <div class="text-lg font-bold">Laba Rugi</div>
      <div class="mt-3 flex justify-between text-sm"><span>Pendapatan</span><b>Rp {{ number_format($report['pendapatan'],0,',','.') }}</b></div>
      <div class="mt-2 flex justify-between text-sm"><span>Biaya</span><b>Rp {{ number_format($report['biaya'],0,',','.') }}</b></div>
      <div class="mt-3 flex justify-between text-sm"><span>Laba Bersih</span><b>Rp {{ number_format($report['laba_bersih'],0,',','.') }}</b></div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow">
      <div class="text-lg font-bold">Arus Kas</div>
      <div class="mt-3 flex justify-between text-sm"><span>Uang Masuk</span><b>Rp {{ number_format($report['uang_masuk'],0,',','.') }}</b></div>
      <div class="mt-2 flex justify-between text-sm"><span>Uang Keluar</span><b>Rp {{ number_format($report['uang_keluar'],0,',','.') }}</b></div>
      <div class="mt-3 flex justify-between text-sm"><span>Saldo Akhir</span><b>Rp {{ number_format($report['saldo_akhir'],0,',','.') }}</b></div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow">
      <div class="text-lg font-bold">Neraca Sederhana</div>
      <div class="mt-3 flex justify-between text-sm"><span>Kas</span><b>Rp {{ number_format($report['kas'],0,',','.') }}</b></div>
      <div class="mt-2 flex justify-between text-sm"><span>Aset Lain</span><b>Rp {{ number_format($report['aset_lain'],0,',','.') }}</b></div>
      <div class="mt-2 flex justify-between text-sm"><span>Total Aset</span><b>Rp {{ number_format($report['total_aset'],0,',','.') }}</b></div>
      <div class="mt-3 flex justify-between text-sm"><span>Modal</span><b>Rp {{ number_format($report['modal'],0,',','.') }}</b></div>
    </div>
  </div>
</x-layouts.app>
