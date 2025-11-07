<div>
    <label for="code" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Kode Troli</label>
    <input
        type="text"
        id="code"
        name="code"
        value="{{ old('code', $trolley->code ?? '') }}"
        required
        class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
        placeholder="TR-001"
    >
    @error('code')
        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
    @enderror
</div>

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="type" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Jenis Troli</label>
        <select
            id="type"
            name="type"
            required
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
        >
            @php
                $selectedType = old('type', $trolley->type ?? \App\Models\Trolley::TYPES[0]);
            @endphp
            @foreach (\App\Models\Trolley::TYPE_LABELS as $value => $label)
                <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kind" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Tipe Troli</label>
        @php
            $selectedKind = old('kind', $trolley->kind ?? \App\Models\Trolley::KINDS[0]);
        @endphp
        <select
            id="kind"
            name="kind"
            required
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
        >
            @foreach (\App\Models\Trolley::KIND_LABELS as $value => $label)
                <option value="{{ $value }}" @selected($selectedKind === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('kind')
            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Status</label>
        <select
            id="status"
            name="status"
            required
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
        >
            <option value="in" @selected(old('status', $trolley->status ?? '') === 'in')>IN (Tersedia)</option>
            <option value="out" @selected(old('status', $trolley->status ?? '') === 'out')>OUT (Sedang Digunakan)</option>
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
        placeholder="Informasi tambahan mengenai troli"
    >{{ old('notes', $trolley->notes ?? '') }}</textarea>
    @error('notes')
        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
    @enderror
</div>
