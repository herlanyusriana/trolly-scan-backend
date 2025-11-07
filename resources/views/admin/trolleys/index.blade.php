@php
    $title = 'Data Troli';
    $printableIds = $trolleys->getCollection()
        ->filter(fn ($trolley) => filled($trolley->qr_code_path))
        ->pluck('id')
        ->values();
@endphp

@extends('layouts.admin')

@section('content')
    <div
        x-data="qrSelection({ ids: @js($printableIds) })"
        class="rounded-3xl border border-slate-800 bg-slate-900/70 shadow-xl shadow-slate-950/20"
    >
        <div class="flex flex-col gap-4 border-b border-slate-800 px-6 py-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-white">Data Troli</h1>
                <p class="text-sm text-slate-500">Kelola troli dan cetak QR code secara massal tanpa boros kertas.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 transition hover:bg-slate-800/80"
                    x-on:click="toggleSelectAll"
                    x-bind:class="allSelected ? 'border-blue-500/40 bg-blue-600/20 text-blue-100' : ''"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 9.75l7.5 7.5L21 7.5" />
                    </svg>
                    <span x-text="allSelected ? 'Batalkan Pilih Semua' : 'Pilih Semua QR'"></span>
                </button>

                <a
                    href="#"
                    x-bind:href="printHref"
                    x-bind:aria-disabled="selectedSize === 0"
                    class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-500"
                    x-bind:class="selectedSize === 0 ? 'pointer-events-none opacity-40 hover:bg-emerald-600' : ''"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12M9 7.5v-3h6v3m1.5 4.5h1.125c.621 0 1.125.504 1.125 1.125v6.75A1.125 1.125 0 0119.125 21H4.875A1.125 1.125 0 013.75 19.875v-6.75C3.75 12.504 4.254 12 4.875 12H6m12 0v-1.125A1.125 1.125 0 0016.875 9.75h-9.75A1.125 1.125 0 006 10.875V12m12 0H6" />
                    </svg>
                    Cetak QR
                    <span x-show="selectedSize > 0" class="rounded-full bg-white/10 px-2 py-0.5 text-xs font-bold" x-text="selectedSize"></span>
                </a>

                <a href="{{ route('trolleys.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Troli
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-slate-300">
                <thead class="bg-slate-900/70 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">
                            <span class="sr-only">Pilih</span>
                        </th>
                        <th class="px-6 py-3 text-left font-semibold">Kode</th>
                        <th class="px-6 py-3 text-left font-semibold">Jenis (Internal/External)</th>
                        <th class="px-6 py-3 text-left font-semibold">Tipe (Reinforce/Backplate/CompBase)</th>
                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                        <th class="px-6 py-3 text-left font-semibold">QR Code</th>
                        <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($trolleys as $trolley)
                        @php
                            $statusClass = $trolley->status === 'out'
                                ? 'border-rose-400/40 bg-rose-500/10 text-rose-200'
                                : 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200';
                            $hasQr = filled($trolley->qr_code_path);
                        @endphp
                        <tr class="hover:bg-slate-900/60 transition">
                            <td class="px-6 py-4">
                                @if ($hasQr)
                                    <label class="inline-flex items-center gap-2 text-xs text-slate-400">
                                        <input
                                            type="checkbox"
                                            value="{{ $trolley->id }}"
                                            data-qr-checkbox
                                            x-bind:checked="isSelected({{ $trolley->id }})"
                                            x-on:change="toggle({ id: {{ $trolley->id }} })"
                                            class="rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500"
                                        >
                                        <span>Pilih</span>
                                    </label>
                                @else
                                    <span class="text-slate-700">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-white">{{ $trolley->code }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ \App\Models\Trolley::TYPE_LABELS[$trolley->type] ?? ucfirst($trolley->type) }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ \App\Models\Trolley::KIND_LABELS[$trolley->kind] ?? ucfirst($trolley->kind) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $statusClass }}">
                                    {{ strtoupper($trolley->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                @if ($hasQr)
                                    <a href="{{ asset('storage/' . $trolley->qr_code_path) }}" target="_blank" class="inline-flex items-center gap-1 text-blue-300 hover:text-blue-200">
                                        Lihat
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-slate-600">Belum tersedia</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-slate-300">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('trolleys.edit', $trolley) }}" class="inline-flex items-center gap-1 rounded-full border border-amber-400/40 px-3 py-1.5 text-xs font-semibold text-amber-200 transition hover:bg-amber-500/10">
                                        Edit
                                    </a>
                                    <form action="{{ route('trolleys.destroy', $trolley) }}" method="POST" onsubmit="return confirm('Yakin hapus troli ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-rose-400/40 px-3 py-1.5 text-xs font-semibold text-rose-200 transition hover:bg-rose-500/10">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">Belum ada data troli.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-800 px-6 py-4">
            {{ $trolleys->links() }}
        </div>
    </div>
@endsection

@include('admin.trolleys.partials.qr-selection-script')
