@php
    $title = 'Movement History';
    $dateFrom = $filters['date_from'] ?? null;
    $dateTo = $filters['date_to'] ?? null;
    $sequenceNumber = $filters['sequence_number'] ?? null;
    $activeFilters = $activeFilters ?? array_filter(
        $filters,
        fn ($value) => $value !== null && $value !== ''
    );
@endphp

@extends('layouts.admin')

@section('content')
    <div
        class="space-y-8 pb-12"
        data-history-root
        data-history-url="{{ route('admin.history.refresh') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
    >
        <section>
            <h2 class="text-xl font-semibold text-white">Ringkasan Aktivitas</h2>
            <p class="mt-1 text-sm text-slate-400">
                Rekap pergerakan troli sesuai filter yang diterapkan.
            </p>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-5 shadow-lg shadow-slate-950/30">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Total Event</p>
                    <span class="mt-3 block text-3xl font-semibold text-white" data-history-stat="total">{{ number_format($stats['total']) }}</span>
                    <p class="mt-2 text-xs text-slate-500">Pergerakan tercatat dalam rentang waktu yang dipilih.</p>
                </div>
                <div class="rounded-3xl border border-rose-500/30 bg-rose-500/10 p-5 shadow-lg shadow-rose-900/30">
                    <p class="text-xs uppercase tracking-wide text-rose-200/80">Sedang Keluar</p>
                    <span class="mt-3 block text-3xl font-semibold text-rose-100" data-history-stat="out">{{ number_format($stats['out']) }}</span>
                    <p class="mt-2 text-xs text-rose-200/70">Troli masih berada di luar area penyimpanan.</p>
                </div>
                <div class="rounded-3xl border border-emerald-500/30 bg-emerald-500/10 p-5 shadow-lg shadow-emerald-900/30">
                    <p class="text-xs uppercase tracking-wide text-emerald-200/70">Sudah Kembali</p>
                    <span class="mt-3 block text-3xl font-semibold text-emerald-100" data-history-stat="in">{{ number_format($stats['in']) }}</span>
                    <p class="mt-2 text-xs text-emerald-200/60">Troli yang sudah melakukan proses check-in.</p>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-800/70 bg-slate-900/70 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-3 border-b border-slate-800/60 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Riwayat Pergerakan Troli</h3>
                    <p class="text-sm text-slate-400">Gunakan filter untuk memeriksa pergerakan tertentu dan ekspor sebagai CSV.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('admin.history.export', $activeFilters) }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-3-6L12 15m0 0L6 10.5M12 15V3" />
                        </svg>
                        Export CSV
                    </a>
                </div>
            </div>

            <div class="border-b border-slate-800/60 px-3 py-6 sm:px-6">
                <div class="rounded-3xl border border-slate-800/70 bg-slate-900/50 p-4 shadow-inner shadow-slate-950/20 sm:p-6">
                    <form
                        method="GET"
                        action="{{ route('admin.history.index') }}"
                        class="grid gap-4 md:grid-cols-3 xl:grid-cols-4"
                        data-history-form
                    >
                    <div class="flex flex-col gap-2">
                        <label for="date_from" class="text-xs font-semibold uppercase tracking-wide text-slate-400">Dari Tanggal</label>
                        <input
                            id="date_from"
                            type="date"
                            name="date_from"
                            value="{{ $dateFrom }}"
                            class="rounded-2xl border border-slate-700/70 bg-slate-900 px-4 py-2 text-sm text-slate-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                        >
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="date_to" class="text-xs font-semibold uppercase tracking-wide text-slate-400">Sampai Tanggal</label>
                        <input
                            id="date_to"
                            type="date"
                            name="date_to"
                            value="{{ $dateTo }}"
                            class="rounded-2xl border border-slate-700/70 bg-slate-900 px-4 py-2 text-sm text-slate-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                        >
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="sequence_number" class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nomor Urutan Keberangkatan</label>
                        <input
                            id="sequence_number"
                            type="number"
                            min="1"
                            name="sequence_number"
                            value="{{ $sequenceNumber }}"
                            placeholder="Misal: 12"
                            class="rounded-2xl border border-slate-700/70 bg-slate-900 px-4 py-2 text-sm text-slate-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                        >
                    </div>

                    <div class="flex flex-wrap items-center gap-3 md:col-span-3 xl:col-span-4">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11.25 18.75a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                            </svg>
                            Terapkan Filter
                        </button>
                        <a
                            href="{{ route('admin.history.index') }}"
                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-700/70 px-5 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800/80"
                        >
                            Reset
                        </a>
                    </div>
                    </form>
                </div>
            </div>

            <div class="px-3 pb-4 sm:px-6">
                <div class="rounded-2xl border border-slate-800/60 bg-slate-950/50 shadow-inner shadow-slate-950/30">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[960px] text-sm text-slate-200">
                            <thead class="bg-slate-900/70 text-xs uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">No. Urut</th>
                                    <th class="px-4 py-3 text-left font-semibold">Troli</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jenis</th>
                                    <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                                    <th class="px-4 py-3 text-left font-semibold">Operator</th>
                                    <th class="px-4 py-3 text-left font-semibold">Kendaraan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Driver</th>
                                    <th class="px-4 py-3 text-left font-semibold">Tujuan / Lokasi</th>
                                    <th class="px-4 py-3 text-left font-semibold">Catatan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/70" data-history-table>
                                @include('admin.history.partials.table-body', ['movements' => $movements])
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800/60 px-6 py-4" data-history-pagination>
                {{ $movements->links() }}
            </div>
        </section>
    </div>
@endsection
