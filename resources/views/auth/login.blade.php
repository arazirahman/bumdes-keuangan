<x-layouts.app :title="$title">
  <div class="mx-auto max-w-md rounded-2xl bg-white p-6 shadow">
    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
      @csrf

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
        <input name="email" type="email" value="{{ old('email') }}"
          class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
        @error('email') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Password</label>
        <input name="password" type="password"
          class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
        @error('password') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
      </div>

      <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="remember" class="rounded border-slate-300">
        Ingat saya
      </label>

      <button class="w-full rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
        Login
      </button>

      <div class="mt-3 text-xs text-slate-500">
        Akun default seeder:<br>
        <b>admin@bumdes.test</b> / <b>password123</b>
      </div>
    </form>
  </div>
</x-layouts.app>
