@php
    $title = 'Movement History';
    $dateFrom = $filters['date_from'] ?? null;
    $dateTo = $filters['date_to'] ?? null;
    $sequenceNumber = $filters['sequence_number'] ?? null;
    $statusFilter = $filters['status'] ?? null;
    $durationFilter = $filters['duration'] ?? null;
    $activeFilters = $activeFilters ?? array_filter(
        $filters,
        fn ($value) => $value !== null && $value !== ''
    );

    // Duration filter labels
    $durationLabels = [
        'less_than_3' => '✅ Kurang dari 3 Hari',
        'between_3_and_6' => '⚠️ Antara 3-6 Hari',
        'more_than_6' => '🚨 Lebih dari 6 Hari',
        'more_than_3' => '⚠️ Lebih dari 3 Hari',
    ];

    $durationColors = [
        'less_than_3' => 'border-emerald-500/60 bg-emerald-500/20 text-emerald-200',
        'between_3_and_6' => 'border-amber-500/60 bg-amber-500/20 text-amber-200',
        'more_than_6' => 'border-rose-500/60 bg-rose-500/20 text-rose-200',
        'more_than_3' => 'border-orange-500/60 bg-orange-500/20 text-orange-200',
    ];
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

            @if($durationFilter || $statusFilter)
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-xs text-slate-500">Filter Aktif:</span>
                    @if($durationFilter && isset($durationLabels[$durationFilter]))
                        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $durationColors[$durationFilter] ?? 'border-slate-600 bg-slate-800 text-slate-200' }}">
                            {{ $durationLabels[$durationFilter] }}
                        </span>
                    @endif
                    @if($statusFilter)
                        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $statusFilter === 'out' ? 'border-blue-500/60 bg-blue-500/20 text-blue-200' : 'border-emerald-500/60 bg-emerald-500/20 text-emerald-200' }}">
                            Status: {{ strtoupper($statusFilter) }}
                        </span>
                    @endif
                </div>
            @endif

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
            <div class="flex flex-col gap-3 border-b border-slate-800/60 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-white sm:text-lg">Riwayat Pergerakan Troli</h3>
                    <p class="text-xs text-slate-400 sm:text-sm">Gunakan filter untuk memeriksa pergerakan tertentu dan ekspor sebagai CSV.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <a
                        href="{{ route('admin.history.export', $activeFilters) }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-500 sm:px-4 sm:text-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-3-6L12 15m0 0L6 10.5M12 15V3" />
                        </svg>
                        <span class="hidden sm:inline">Export</span> CSV
                    </a>
                    <a
                        href="{{ route('admin.history.export.xlsx', $activeFilters) }}"
                        class="inline-flex items-center gap-2 rounded-2xl border border-blue-500/40 px-3 py-2 text-xs font-semibold text-blue-100 transition hover:bg-blue-500/10 sm:px-4 sm:text-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25L11.25 6m0 0L13.5 8.25M11.25 6v9m0 6.75c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9z" />
                        </svg>
                        <span class="hidden sm:inline">Export</span> XLSX
                    </a>
                </div>
            </div>

            <div class="border-b border-slate-800/60 px-3 py-5 sm:px-5 md:px-6">
                <div class="rounded-3xl border border-slate-800/70 bg-slate-900/50 p-4 shadow-inner shadow-slate-950/20 sm:p-5">
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

                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 md:col-span-3 xl:col-span-4">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500 sm:px-5 sm:text-sm"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11.25 18.75a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                            </svg>
                            Terapkan Filter
                        </button>
                        <a
                            href="{{ route('admin.history.index') }}"
                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-700/70 px-4 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-800/80 sm:px-5 sm:text-sm"
                        >
                            Reset
                        </a>
                    </div>
                    </form>
                </div>
            </div>

            <div class="px-3 pb-4 sm:px-5 md:px-6">
                <!-- Desktop Table View -->
                <div class="hidden rounded-2xl border border-slate-800/60 bg-slate-950/50 shadow-inner shadow-slate-950/30 lg:block">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-slate-200">
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

                <!-- Mobile Card View -->
                <div class="space-y-3 lg:hidden" data-history-table>
                    @forelse($movements as $movement)
                        <div class="rounded-2xl border border-slate-800/60 bg-slate-950/50 p-4 shadow-lg">
                            <div class="mb-3 flex items-start justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-lg bg-slate-800 px-2 py-1 text-xs font-semibold text-slate-300">
                                            #{{ $movement->sequence_number }}
                                        </span>
                                        @php
                                            $statusBadge = $movement->status === 'out'
                                                ? 'border-blue-500/60 bg-blue-500/20 text-blue-200'
                                                : 'border-emerald-500/60 bg-emerald-500/20 text-emerald-200';
                                        @endphp
                                        <span class="rounded-full border px-2 py-1 text-xs font-semibold uppercase {{ $statusBadge }}">
                                            {{ $movement->status }}
                                        </span>
                                    </div>
                                    <h3 class="mt-2 text-base font-semibold text-white">{{ $movement->trolley->code }}</h3>
                                    <p class="text-xs text-slate-400">{{ $movement->trolley->kind }} - {{ $movement->trolley->type }}</p>
                                </div>
                            </div>

                            <div class="space-y-2 border-t border-slate-800/60 pt-3 text-xs">
                                @if($movement->user)
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Operator:</span>
                                        <span class="font-medium text-slate-200">{{ $movement->user->name }}</span>
                                    </div>
                                @endif
                                @if($movement->vehicle)
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Kendaraan:</span>
                                        <span class="font-medium text-slate-200">{{ $movement->vehicle->plate_number }}</span>
                                    </div>
                                @endif
                                @if($movement->driver)
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Driver:</span>
                                        <span class="font-medium text-slate-200">{{ $movement->driver->name }}</span>
                                    </div>
                                @endif
                                @if($movement->destination || $movement->location)
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">{{ $movement->status === 'out' ? 'Tujuan' : 'Lokasi' }}:</span>
                                        <span class="font-medium text-slate-200">{{ $movement->destination ?? $movement->location ?? '-' }}</span>
                                    </div>
                                @endif
                                @if($movement->notes)
                                    <div class="mt-2 rounded-lg bg-slate-900/50 p-2">
                                        <span class="text-slate-500">Catatan:</span>
                                        <p class="mt-1 text-slate-300">{{ $movement->notes }}</p>
                                    </div>
                                @endif
                                <div class="mt-3 flex justify-between border-t border-slate-800/40 pt-2">
                                    <span class="text-slate-500">Waktu:</span>
                                    <span class="font-medium text-slate-200">{{ $movement->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-slate-800/60 bg-slate-950/50 px-6 py-12 text-center">
                            <p class="text-sm text-slate-500">Tidak ada data pergerakan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="border-t border-slate-800/60 px-6 py-4" data-history-pagination>
                {{ $movements->withQueryString()->links() }}
            </div>
        </section>
    </div>
@endsection
