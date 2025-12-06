@php
    $title = $categoryConfig['title'];
@endphp

@extends('layouts.admin')

@section('content')
    <div class="space-y-6 pb-12">
        <!-- Header with Category Info -->
        <div class="rounded-3xl border {{ $categoryConfig['border'] }} {{ $categoryConfig['bg'] }} p-6 shadow-xl">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold {{ $categoryConfig['text'] }}">
                        {{ $categoryConfig['icon'] }} {{ $categoryConfig['title'] }}
                    </h1>
                    <p class="mt-2 text-sm {{ $categoryConfig['text'] }}/80">
                        {{ $categoryConfig['description'] }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800/80"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali ke Dashboard
                    </a>
                    <a
                        href="{{ route('admin.duration-category.export', ['category' => $category]) }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-{{ $categoryConfig['color'] }}-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-{{ $categoryConfig['color'] }}-600/30 transition hover:bg-{{ $categoryConfig['color'] }}-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-3-6L12 15m0 0L6 10.5M12 15V3" />
                        </svg>
                        Export CSV
                    </a>
                    <a
                        href="{{ route('admin.duration-category.export.xlsx', ['category' => $category]) }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Export XLSX
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30">
            <div class="flex items-center gap-4">
                <div class="rounded-full {{ $categoryConfig['bg'] }} {{ $categoryConfig['border'] }} border-2 p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 {{ $categoryConfig['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Total Troli dalam Kategori Ini</p>
                    <p class="mt-1 text-4xl font-bold text-white">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 shadow-xl shadow-slate-950/30">
            <div class="border-b border-slate-800/60 px-4 py-4 sm:px-6">
                <h3 class="text-lg font-semibold text-white">Daftar Troli</h3>
                <p class="mt-1 text-sm text-slate-400">Troli yang saat ini berada dalam kategori {{ strtolower($categoryConfig['title']) }}</p>
            </div>

            <div class="px-3 pb-4 sm:px-5 md:px-6">
                <div class="rounded-2xl border border-slate-800/60 bg-slate-950/50 shadow-inner shadow-slate-950/30">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[960px] text-sm text-slate-200">
                            <thead class="bg-slate-900/70 text-xs uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Kode Troli</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jenis</th>
                                    <th class="px-4 py-3 text-left font-semibold">Tipe</th>
                                    <th class="px-4 py-3 text-left font-semibold">User</th>
                                    <th class="px-4 py-3 text-left font-semibold">Tujuan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Kendaraan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Driver</th>
                                    <th class="px-4 py-3 text-left font-semibold">Waktu Keluar</th>
                                    <th class="px-4 py-3 text-left font-semibold">Durasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                @forelse($movements as $movement)
                                    @php
                                        $days = $movement->days_out ?? 0;
                                        $rowBgClass = '';
                                        $badgeClass = '';
                                        
                                        if ($days > 6) {
                                            $rowBgClass = 'bg-rose-950/30';
                                            $badgeClass = 'border-rose-500/60 bg-rose-500/20 text-rose-200 font-bold';
                                        } elseif ($days >= 3) {
                                            $rowBgClass = 'bg-amber-950/20';
                                            $badgeClass = 'border-amber-500/60 bg-amber-500/20 text-amber-200 font-bold';
                                        } else {
                                            $rowBgClass = 'bg-emerald-950/10';
                                            $badgeClass = 'border-emerald-500/50 bg-emerald-500/15 text-emerald-200';
                                        }
                                    @endphp
                                    <tr class="transition hover:bg-slate-900/60 {{ $rowBgClass }}">
                                        <td class="px-4 py-3 font-mono font-semibold text-white">{{ $movement->trolley?->code ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ $movement->trolley?->type_label ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ $movement->trolley?->kind_label ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="font-medium text-white">{{ $movement->mobileUser?->name ?? '-' }}</p>
                                                <p class="text-xs text-slate-500">{{ $movement->mobileUser?->phone ?? '-' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-slate-300">{{ $movement->destination ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-300">
                                            {{ $movement->vehicle?->plate_number ?? $movement->vehicle_snapshot ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-300">
                                            {{ $movement->driver?->name ?? $movement->driver_snapshot ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-400">
                                            {{ $movement->checked_out_at?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                                                {{ $days }} Hari
                                                @if($days > 6)
                                                    🚨
                                                @elseif($days >= 3)
                                                    ⚠️
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                                            Tidak ada troli dalam kategori ini saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($movements->hasPages())
                <div class="border-t border-slate-800/60 px-6 py-4">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
