<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="name" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Nama Driver</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $driver->name ?? '') }}"
            required
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
            placeholder="Nama lengkap"
        >
        @error('name')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="phone" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Nomor Telepon</label>
        <input
            type="text"
            id="phone"
            name="phone"
            value="{{ old('phone', $driver->phone ?? '') }}"
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
            placeholder="08xxxxxxxxxx"
        >
        @error('phone')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="license_number" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Nomor SIM</label>
        <input
            type="text"
            id="license_number"
            name="license_number"
            value="{{ old('license_number', $driver->license_number ?? '') }}"
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
            placeholder="SIM A / B / dll"
        >
        @error('license_number')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Status</label>
        @php
            $selectedStatus = old('status', $driver->status ?? \App\Models\Driver::STATUSES[0]);
        @endphp
        <select
            id="status"
            name="status"
            required
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
        >
            @foreach (\App\Models\Driver::STATUS_LABELS as $value => $label)
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
        placeholder="Informasi tambahan mengenai driver"
    >{{ old('notes', $driver->notes ?? '') }}</textarea>
    @error('notes')
        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
    @enderror
</div>
