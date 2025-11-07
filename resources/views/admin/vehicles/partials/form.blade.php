<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-1">
        <label for="plate_number" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Plat Nomor</label>
        <input
            type="text"
            id="plate_number"
            name="plate_number"
            value="{{ old('plate_number', $vehicle->plate_number ?? '') }}"
            required
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
            placeholder="B 1234 CD"
        >
        @error('plate_number')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>
    <div class="sm:col-span-1">
        <label for="name" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Nama / Alias</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $vehicle->name ?? '') }}"
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
            placeholder="Contoh: Truk Box Utama"
        >
        @error('name')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="category" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Kategori</label>
        <input
            type="text"
            id="category"
            name="category"
            value="{{ old('category', $vehicle->category ?? '') }}"
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
            placeholder="Pickup, Trailer, dll"
        >
        @error('category')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Status</label>
        @php
            $selectedStatus = old('status', $vehicle->status ?? \App\Models\Vehicle::STATUSES[0]);
        @endphp
        <select
            id="status"
            name="status"
            required
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
        >
            @foreach (\App\Models\Vehicle::STATUS_LABELS as $value => $label)
                <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label for="notes" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Catatan</label>
    <textarea
        id="notes"
        name="notes"
        rows="3"
        class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
        placeholder="Informasi tambahan mengenai kendaraan"
    >{{ old('notes', $vehicle->notes ?? '') }}</textarea>
    @error('notes')
        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
    @enderror
</div>
