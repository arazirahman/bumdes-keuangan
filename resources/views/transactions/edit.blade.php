<x-layouts.app :title="$title">
  <div class="rounded-2xl bg-white p-6 shadow">
    <form method="POST" action="{{ route('transactions.update', $row->id) }}" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">
      @csrf
      @method('PUT')

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Tanggal</label>
        <input type="date" name="trx_date" value="{{ old('trx_date', $row->trx_date->toDateString()) }}"
          class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        @error('trx_date') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Jenis</label>
        <select name="type" id="type"
          class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
          <option value="income" @selected(old('type', $row->type)==='income')>Pemasukan</option>
          <option value="expense" @selected(old('type', $row->type)==='expense')>Pengeluaran</option>
        </select>
        @error('type') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Unit Usaha</label>
        <select name="unit_usaha_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
          @foreach($units as $u)
            <option value="{{ $u->id }}" @selected((string)$u->id===old('unit_usaha_id', (string)$row->unit_usaha_id))>{{ $u->name }}</option>
          @endforeach
        </select>
        @error('unit_usaha_id') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Kategori</label>
        <select name="category_id" id="category_id"
          class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm"></select>
        @error('category_id') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
      </div>

      <div class="sm:col-span-2">
        <label class="mb-1 block text-sm font-semibold text-slate-700">Keterangan</label>
        <input name="description" value="{{ old('description', $row->description) }}"
          class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Nominal (Rp)</label>
        <input name="amount" type="number" min="1" value="{{ old('amount', $row->amount) }}"
          class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        @error('amount') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Bukti (foto)</label>
        <input name="proof" type="file" accept="image/*"
          class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">

        @if($row->proof_path)
          <div class="mt-2 text-sm">
            Bukti saat ini: <a class="text-indigo-700 underline" target="_blank" href="{{ asset('storage/'.$row->proof_path) }}">Lihat</a>
          </div>
          <label class="mt-2 flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remove_proof" value="1" class="rounded border-slate-300">
            Hapus bukti lama
          </label>
        @endif

        @error('proof') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
      </div>

      <div class="sm:col-span-2 flex flex-wrap gap-2">
        <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
          Simpan Perubahan
        </button>
        <a href="{{ route('transactions.index') }}" class="rounded-xl border px-4 py-2 text-sm">
          Batal
        </a>
      </div>
    </form>
  </div>

  <script>
    const incomeCats = @json($incomeCats->map(fn($c)=>['id'=>$c->id,'name'=>$c->name])->values());
    const expenseCats = @json($expenseCats->map(fn($c)=>['id'=>$c->id,'name'=>$c->name])->values());

    const typeEl = document.getElementById('type');
    const catEl  = document.getElementById('category_id');

    function renderCats(type){
      const list = (type === 'expense') ? expenseCats : incomeCats;
      catEl.innerHTML = '';
      list.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        catEl.appendChild(opt);
      });

      const oldCat = @json(old('category_id', $row->category_id));
      if (oldCat) catEl.value = String(oldCat);
    }

    renderCats(typeEl.value || 'income');
    typeEl.addEventListener('change', () => renderCats(typeEl.value));
  </script>
</x-layouts.app>
