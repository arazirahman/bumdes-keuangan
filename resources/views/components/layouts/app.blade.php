<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title ?? 'SIMKEU BUMDes' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50">
  <div class="mx-auto max-w-6xl p-4 sm:p-6">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
        <div class="text-sm tracking-widest text-indigo-700">SIMKEU BUMDes</div>
        <div class="text-2xl font-bold text-slate-900">{{ $title ?? '' }}</div>
      </div>

      @auth
      <div class="flex items-center gap-3">
        <div class="text-sm text-slate-600">
          {{ auth()->user()->name }}
          <span class="text-slate-400">({{ auth()->user()->role }})</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
            Logout
          </button>
        </form>
      </div>
      @endauth
    </div>

    @auth
    <nav class="mb-6 flex flex-wrap items-center gap-2">
      <a href="{{ route('dashboard') }}"
         class="ml-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
        Dashboard
      </a>

      <a href="{{ route('transactions.index') }}"
         class="ml-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
        Transaksi
      </a>

      {{-- LAPORAN: superadmin saja --}}
      @if(auth()->user()->role === 'superadmin')
        <a href="{{ route('reports.index') }}"
           class="ml-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
          Laporan
        </a>
      @endif

      {{-- ASET: superadmin saja --}}
@if(in_array(auth()->user()->role, ['operator','superadmin']))
  <a href="{{ route('assets.index') }}"
     class="ml-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
    Aset
  </a>
@endif


      {{-- + Tambah: operator & superadmin --}}
      @if(in_array(auth()->user()->role, ['operator','superadmin']))
        <a href="{{ route('transactions.create') }}"
           class="ml-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
          + Tambah
        </a>
<a href="{{ route('reports.index') }}"
     class="ml-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
    Laporan
  </a>

      @endif
    </nav>
    @endauth

    @if(session('ok'))
      <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
        {{ session('ok') }}
      </div>
    @endif

    {{ $slot }}

  </div>
</body>
</html>
