@php
    $title = 'Data Troli';
    $printableIds = $trolleys->getCollection()
        ->filter(fn ($trolley) => filled($trolley->qr_code_path))
        ->pluck('id')
        ->values();
    $searchValue = $search ?? request('q');
    $statusValue = $status ?? request('status');
@endphp

@extends('layouts.admin')

@section('content')
    <div
        x-data="qrSelection({ ids: @js($printableIds) })"
        class="rounded-3xl border border-slate-800 bg-slate-900/70 shadow-xl shadow-slate-950/20"
    >
        <div class="flex flex-col gap-4 border-b border-slate-800 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-white">Data Troli</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola troli dan cetak QR code secara massal tanpa boros kertas.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('trolleys.export', ['q' => $searchValue, 'status' => $statusValue]) }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-emerald-500/40 px-4 py-2 text-sm font-semibold text-emerald-100 transition hover:bg-emerald-500/10"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v12m0 0l3.75-3.75M12 16.5l-3.75-3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    Export CSV
                </a>
                <a
                    href="{{ route('trolleys.export.xlsx', ['q' => $searchValue, 'status' => $statusValue]) }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-blue-500/40 px-4 py-2 text-sm font-semibold text-blue-100 transition hover:bg-blue-500/10"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25L11.25 6m0 0L13.5 8.25M11.25 6v9m0 6.75c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9z" />
                    </svg>
                    Export XLSX
                </a>
                <form action="{{ route('trolleys.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <label class="sr-only" for="trolley-search">Cari troli</label>
                    <input
                        id="trolley-search"
                        type="text"
                        name="q"
                        value="{{ $searchValue }}"
                        placeholder="Cari kode / jenis..."
                        class="w-56 rounded-2xl border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-100 placeholder-slate-500 shadow-inner focus:border-blue-500 focus:ring focus:ring-blue-500/30 sm:w-64"
                    >
                    <label class="sr-only" for="trolley-status">Status</label>
                    <select
                        id="trolley-status"
                        name="status"
                        class="w-36 rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring focus:ring-blue-500/30"
                    >
                        <option value="" @selected($statusValue === null || $statusValue === '')>Semua Status</option>
                        <option value="in" @selected($statusValue === 'in')>IN (Tersedia)</option>
                        <option value="out" @selected($statusValue === 'out')>OUT (Sedang Digunakan)</option>
                    </select>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607-10.607A7.5 7.5 0 0016.65 16.65z" />
                        </svg>
                        Cari Trolly
                    </button>
                    @if ($searchValue || $statusValue)
                        <a
                            href="{{ route('trolleys.index') }}"
                            class="inline-flex items-center gap-1 rounded-2xl border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-800/80"
                        >
                            Reset
                        </a>
                    @endif
                </form>

                <a href="{{ route('trolleys.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Troli
                </a>
            </div>
        </div>

        <div class="px-6 py-5">
            <div class="rounded-2xl border border-slate-800/60 bg-slate-950/50 shadow-inner shadow-slate-950/30">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[960px] text-sm text-slate-300">
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">
                            <span class="sr-only">Pilih</span>
                        </th>
                        <th class="px-6 py-4 text-left font-semibold">Kode</th>
                        <th class="px-6 py-4 text-left font-semibold">Jenis</th>
                        <th class="px-6 py-4 text-left font-semibold">Tipe</th>
                        <th class="px-6 py-4 text-left font-semibold">Last Movement</th>
                        <th class="px-6 py-4 text-left font-semibold">Durasi Status</th>
                        <th class="px-6 py-4 text-left font-semibold">Status</th>
                        <th class="px-6 py-4 text-left font-semibold">QR Code</th>
                        <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                        <tbody class="divide-y divide-slate-800/80">
                    @forelse($trolleys as $trolley)
                        @php
                            // Calculate duration in days for OUT status
                            $durationDays = 0;
                            $durationClass = '';
                            $rowBgClass = '';
                            
                            if ($trolley->status === 'out' && $trolley->status_since) {
                                $durationDays = $trolley->status_since->diffInDays(now());
                                
                                if ($durationDays > 6) {
                                    // > 6 days - Rose/Red
                                    $durationClass = 'border-rose-500/60 bg-rose-500/20 text-rose-200 font-bold';
                                    $rowBgClass = 'bg-rose-950/30';
                                } elseif ($durationDays >= 3) {
                                    // 3-6 days - Amber/Yellow
                                    $durationClass = 'border-amber-500/60 bg-amber-500/20 text-amber-200 font-bold';
                                    $rowBgClass = 'bg-amber-950/20';
                                } else {
                                    // < 3 days - Emerald/Green
                                    $durationClass = 'border-emerald-500/50 bg-emerald-500/15 text-emerald-200';
                                    $rowBgClass = 'bg-emerald-950/10';
                                }
                            }
                            
                            $statusClass = $trolley->status === 'out'
                                ? ($durationDays > 6 
                                    ? 'border-rose-500/60 bg-rose-500/20 text-rose-200 font-bold'
                                    : ($durationDays >= 3 
                                        ? 'border-amber-500/60 bg-amber-500/20 text-amber-200 font-bold'
                                        : 'border-blue-400/40 bg-blue-500/10 text-blue-200'))
                                : 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200';
                            $hasQr = filled($trolley->qr_code_path);
                        @endphp
                        <tr class="hover:bg-slate-900/60 transition {{ $rowBgClass }}">
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
                                    </label>
                                @else
                                    <span class="text-slate-700">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono font-semibold text-white">{{ $trolley->code }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ \App\Models\Trolley::TYPE_LABELS[$trolley->type] ?? ucfirst($trolley->type) }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ \App\Models\Trolley::KIND_LABELS[$trolley->kind] ?? ucfirst($trolley->kind) }}</td>
                            <td class="px-6 py-4 text-slate-300">
                                @php
                                    $lastStatus = $trolley->last_movement_status;
                                    $lastAt = $trolley->last_movement_at;
                                @endphp
                                @if ($lastStatus && $lastAt)
                                    <div class="flex flex-col leading-tight">
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $lastStatus === 'out' ? 'border-blue-400/40 bg-blue-500/10 text-blue-200' : 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200' }}">
                                            {{ strtoupper($lastStatus) }}
                                        </span>
                                        <span class="mt-1 text-xs text-slate-500">{{ $lastAt->format('d M Y H:i') }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                @if ($trolley->status_duration_label)
                                    <div class="flex flex-col leading-tight">
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $durationClass ?: 'text-white border-slate-600 bg-slate-800/50' }}">
                                            {{ $trolley->status_duration_label }}
                                            @if($trolley->status === 'out' && $durationDays >= 3)
                                                @if($durationDays > 6)
                                                    🚨
                                                @else
                                                    ⚠️
                                                @endif
                                            @endif
                                        </span>
                                        @if ($trolley->status_since)
                                            <span class="mt-1 text-xs text-slate-500">Sejak {{ $trolley->status_since->format('d M Y H:i') }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $statusClass }}">
                                    {{ strtoupper($trolley->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                @if ($hasQr)
                                    <a href="{{ asset('storage/' . $trolley->qr_code_path) }}" target="_blank" class="inline-flex items-center gap-1 text-blue-300 hover:text-blue-200 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                                        </svg>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-slate-300">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($hasQr)
                                        <a href="{{ route('trolleys.print', ['ids' => $trolley->id]) }}" class="inline-flex items-center gap-1 rounded-full border border-blue-400/40 bg-blue-500/5 px-3 py-1.5 text-xs font-semibold text-blue-200 transition hover:bg-blue-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                                            </svg>
                                            Cetak
                                        </a>
                                    @endif
                                    <a href="{{ route('trolleys.edit', $trolley) }}" class="inline-flex items-center gap-1 rounded-full border border-slate-600 bg-slate-800/50 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('trolleys.destroy', $trolley) }}" method="POST" onsubmit="return confirm('Yakin hapus troli ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-rose-400/40 bg-rose-500/5 px-3 py-1.5 text-xs font-semibold text-rose-200 transition hover:bg-rose-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-500">Belum ada data troli.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="border-t border-slate-800 px-6 py-4">
    {{ $trolleys->links() }}
</div>
@endsection

@include('admin.trolleys.partials.qr-selection-script')
