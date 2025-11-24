@php
    $title = 'QR Code Management';
    $printableIds = $trolleys->pluck('id');
@endphp

@extends('layouts.admin')

@section('content')
    <div
        x-data="qrSelection({ ids: @js($printableIds) })"
        class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/20"
    >
        <div class="flex flex-col gap-4 border-b border-slate-800 pb-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-white">QR Code Management</h1>
                <p class="text-sm text-slate-400">
                    Lihat dan unduh QR code seluruh troli. Pilih banyak sekaligus untuk cetak massal agar hemat kertas.
                </p>
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
                    href="{{ route('trolleys.print') }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 px-5 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800/80"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12M9 7.5v-3h6v3m1.5 4.5h1.125c.621 0 1.125.504 1.125 1.125v6.75A1.125 1.125 0 0119.125 21H4.875A1.125 1.125 0 013.75 19.875v-6.75C3.75 12.504 4.254 12 4.875 12H6m12 0v-1.125A1.125 1.125 0 0016.875 9.75h-9.75A1.125 1.125 0 006 10.875V12m12 0H6" />
                    </svg>
                    Cetak Semua
                </a>

                <button
                    type="button"
                    x-on:click="selectedSize > 0 && (window.location = printHref)"
                    x-bind:aria-disabled="selectedSize === 0"
                    class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-500"
                    x-bind:class="selectedSize === 0 ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12M9 7.5v-3h6v3m1.5 4.5h1.125c.621 0 1.125.504 1.125 1.125v6.75A1.125 1.125 0 0119.125 21H4.875A1.125 1.125 0 013.75 19.875v-6.75C3.75 12.504 4.254 12 4.875 12H6m12 0v-1.125A1.125 1.125 0 0016.875 9.75h-9.75A1.125 1.125 0 006 10.875V12m12 0H6" />
                    </svg>
                    Cetak Terpilih
                    <span x-show="selectedSize > 0" class="rounded-full bg-white/10 px-2 py-0.5 text-xs font-bold" x-text="selectedSize"></span>
                </button>
            </div>
        </div>

        <div class="mt-6">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($trolleys as $trolley)
                    <div class="flex flex-col gap-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-inner">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-widest text-slate-500">Kode Troli</p>
                                <h2 class="text-lg font-semibold text-white">{{ $trolley->code }}</h2>
                                <p class="text-xs uppercase tracking-wide text-slate-500">{{ strtoupper($trolley->type) }} • {{ strtoupper($trolley->status) }}</p>
                            </div>
                            <label class="inline-flex items-center gap-2 text-xs text-slate-400">
                                <input
                                    type="checkbox"
                                    value="{{ $trolley->id }}"
                                    data-qr-checkbox
                                    x-bind:checked="isSelected({{ $trolley->id }})"
                                    x-on:change="toggle({ id: {{ $trolley->id }} })"
                                    class="rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500"
                                >
                                Pilih
                            </label>
                        </div>
                        <div class="flex items-center justify-center">
                            <img
                                src="{{ asset('storage/' . $trolley->qr_code_path) }}"
                                alt="QR Code Troli {{ $trolley->code }}"
                                class="h-48 w-full max-w-[200px] rounded-xl border border-slate-800 bg-white p-4 shadow-lg"
                            >
                        </div>
                        <div class="flex items-center justify-between">
                            <a href="{{ asset('storage/' . $trolley->qr_code_path) }}" target="_blank" class="text-xs font-semibold text-blue-300 hover:text-blue-200">
                                Lihat / Unduh
                            </a>
                            <span class="rounded-full bg-slate-800 px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-slate-300">
                                QR READY
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-slate-800 bg-slate-900/80 p-8 text-center text-slate-500">
                        Belum ada QR code yang tersedia. Tambahkan troli terlebih dahulu.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@include('admin.trolleys.partials.qr-selection-script')
