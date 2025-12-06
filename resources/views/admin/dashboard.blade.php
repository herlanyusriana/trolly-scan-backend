@php
    $title = 'Dashboard';
@endphp

@extends('layouts.admin')

@section('content')
<div
    class="grid gap-6"
    data-dashboard-realtime
    data-dashboard-url="{{ route('admin.dashboard.realtime') }}"
>
        <!-- Main Stats Cards -->
        <div class="grid gap-4 lg:grid-cols-2">
            <a href="{{ route('admin.history.index') }}?status=in" class="group rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30 transition hover:border-emerald-500/50 hover:bg-slate-900">
                <p class="text-xs uppercase tracking-wide text-slate-300">Troli Masuk</p>
                <div class="mt-4 flex items-end gap-3">
                    <span class="text-3xl font-semibold text-white transition group-hover:text-emerald-400" data-dashboard-in>{{ number_format($stats['trolleys']['in']) }}</span>
                    <span class="rounded-full border border-emerald-600 bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-200">IN</span>
                </div>
                <p class="mt-6 text-xs text-slate-400">Klik untuk melihat detail troli yang tersedia</p>
            </a>

            <a href="{{ route('admin.history.index') }}?status=out" class="group rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30 transition hover:border-blue-500/50 hover:bg-slate-900">
                <p class="text-xs uppercase tracking-wide text-slate-300">Troli Keluar</p>
                <div class="mt-4 flex items-end gap-3">
                    <span class="text-3xl font-semibold text-white transition group-hover:text-blue-400" data-dashboard-out>{{ number_format($stats['trolleys']['out']) }}</span>
                    <span class="rounded-full border border-blue-600 bg-blue-500/20 px-3 py-1 text-xs font-semibold text-blue-200">OUT</span>
                </div>
                <p class="mt-4 text-xs text-slate-400">Klik untuk melihat detail troli yang sedang digunakan</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-3 text-xs text-slate-100">
                        <p class="font-semibold">Reinforce</p>
                        <p class="mt-1">Keluar: <span class="font-bold" data-dashboard-kind-out="reinforce">{{ number_format($stats['trolleys']['kinds_out']['reinforce']) }}</span></p>
                    </div>
                    <div class="rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-3 text-xs text-slate-100">
                        <p class="font-semibold">Backplate</p>
                        <p class="mt-1">Keluar: <span class="font-bold" data-dashboard-kind-out="backplate">{{ number_format($stats['trolleys']['kinds_out']['backplate']) }}</span></p>
                    </div>
                    <div class="rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-3 text-xs text-slate-100">
                        <p class="font-semibold">CompBase</p>
                        <p class="mt-1">Keluar: <span class="font-bold" data-dashboard-kind-out="compbase">{{ number_format($stats['trolleys']['kinds_out']['compbase']) }}</span></p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Duration Category Cards -->
        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('admin.history.index') }}?duration=less_than_3" class="group rounded-3xl border border-emerald-800/70 bg-gradient-to-br from-emerald-900/70 to-slate-900/70 p-6 shadow-xl shadow-emerald-950/30 transition hover:border-emerald-500/70 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-emerald-300 font-semibold">✅ Kurang dari 3 Hari</p>
                        <div class="mt-4 flex items-end gap-3">
                            <span class="text-4xl font-bold text-white transition group-hover:text-emerald-300" data-dashboard-less-3>{{ number_format($stats['trolleys']['duration_categories']['less_than_3']) }}</span>
                            <span class="mb-1 text-sm font-semibold text-emerald-200">TROLI</span>
                        </div>
                        <p class="mt-4 text-xs text-emerald-200/70">Troli dalam kondisi normal</p>
                    </div>
                    <div class="rounded-full border-2 border-emerald-500/50 bg-emerald-500/20 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.history.index') }}?duration=between_3_and_6" class="group rounded-3xl border border-amber-800/70 bg-gradient-to-br from-amber-900/70 to-slate-900/70 p-6 shadow-xl shadow-amber-950/30 transition hover:border-amber-500/70 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-amber-300 font-semibold">⚠️ Antara 3-6 Hari</p>
                        <div class="mt-4 flex items-end gap-3">
                            <span class="text-4xl font-bold text-white transition group-hover:text-amber-300" data-dashboard-3-6>{{ number_format($stats['trolleys']['duration_categories']['between_3_and_6']) }}</span>
                            <span class="mb-1 text-sm font-semibold text-amber-200">TROLI</span>
                        </div>
                        <p class="mt-4 text-xs text-amber-200/70">Perlu perhatian khusus</p>
                    </div>
                    <div class="rounded-full border-2 border-amber-500/50 bg-amber-500/20 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.history.index') }}?duration=more_than_6" class="group rounded-3xl border border-rose-800/70 bg-gradient-to-br from-rose-900/70 to-slate-900/70 p-6 shadow-xl shadow-rose-950/30 transition hover:border-rose-500/70 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-rose-300 font-semibold">🚨 Lebih dari 6 Hari</p>
                        <div class="mt-4 flex items-end gap-3">
                            <span class="text-4xl font-bold text-white transition group-hover:text-rose-300" data-dashboard-more-6>{{ number_format($stats['trolleys']['duration_categories']['more_than_6']) }}</span>
                            <span class="mb-1 text-sm font-semibold text-rose-200">TROLI</span>
                        </div>
                        <p class="mt-4 text-xs text-rose-200/70">Segera tindak lanjut!</p>
                    </div>
                    <div class="rounded-full border-2 border-rose-500/50 bg-rose-500/20 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Other Stats -->
        <div class="grid gap-4 md:grid-cols-2">
            <a href="{{ route('admin.approvals.index') }}" class="group rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30 transition hover:border-blue-500/50 hover:bg-slate-900">
                <p class="text-xs uppercase tracking-wide text-slate-400">User Mobile Disetujui</p>
                <div class="mt-4 text-3xl font-semibold text-white transition group-hover:text-blue-400" data-dashboard-approved>{{ number_format($stats['mobile_users']['approved']) }}</div>
                <p class="mt-6 text-xs text-slate-500">Klik untuk melihat daftar user</p>
            </a>

            <a href="{{ route('admin.approvals.index') }}" class="group rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30 transition hover:border-amber-500/50 hover:bg-slate-900">
                <p class="text-xs uppercase tracking-wide text-slate-400">Permintaan Menunggu</p>
                <div class="mt-4 text-3xl font-semibold text-white transition group-hover:text-amber-400" data-dashboard-pending>{{ number_format($stats['mobile_users']['pending']) }}</div>
                <p class="mt-6 text-xs text-slate-500">Klik untuk approve user baru</p>
            </a>
        </div>
    @if($overdueMovements->isNotEmpty())
    <div class="rounded-3xl border border-orange-800/70 bg-gradient-to-br from-orange-900/30 to-slate-900/70 shadow-xl shadow-orange-950/20">
        <div class="flex items-center justify-between border-b border-orange-800/50 px-6 py-5">
            <div>
                <h2 class="text-lg font-semibold text-white">⚠️ Troli Terlambat (OUT &gt; 3 Hari)</h2>
                <p class="mt-1 text-xs text-orange-300/80">Troli yang perlu segera dikembalikan</p>
            </div>
            <a href="{{ route('admin.history.index') }}?duration=more_than_3" class="rounded-full border border-orange-400/40 bg-orange-500/20 px-4 py-2 text-sm font-bold text-orange-200 transition hover:bg-orange-500/30">{{ $overdueMovements->count() }} Troli →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-slate-300">
                <thead class="bg-orange-900/30 text-xs uppercase tracking-wide text-orange-300">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Kode Troli</th>
                        <th class="px-6 py-3 text-left font-semibold">Jenis</th>
                        <th class="px-6 py-3 text-left font-semibold">User</th>
                        <th class="px-6 py-3 text-left font-semibold">Tujuan</th>
                        <th class="px-6 py-3 text-left font-semibold">Waktu Keluar</th>
                        <th class="px-6 py-3 text-left font-semibold">Durasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-900/30">
                    @foreach($overdueMovements as $movement)
                    <tr class="hover:bg-orange-900/10 transition-colors">
                        <td class="px-6 py-4 font-mono font-semibold text-white">{{ $movement->trolley->code }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full border border-slate-600 bg-slate-800/80 px-3 py-1 text-xs font-semibold text-slate-200">
                                {{ ucfirst($movement->trolley->kind) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-white">{{ $movement->mobileUser->name ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $movement->mobileUser->phone ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $movement->destination ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-400">
                            {{ $movement->checked_out_at?->format('d M Y H:i') ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $days = $movement->days_out;
                                $colorClass = 'orange';
                                if ($days > 6) {
                                    $colorClass = 'rose';
                                } elseif ($days > 3) {
                                    $colorClass = 'amber';
                                }
                            @endphp
                            <span class="inline-flex items-center rounded-full border border-{{ $colorClass }}-400/40 bg-{{ $colorClass }}-500/20 px-3 py-1 text-xs font-bold text-{{ $colorClass }}-200">
                                {{ $days }} Hari
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 shadow-xl shadow-slate-950/20">
                <div class="flex items-center justify-between border-b border-slate-800 px-6 py-5">
                    <h2 class="text-lg font-semibold text-white">Pergerakan Troli Terbaru</h2>
                    <span class="text-xs text-slate-500">20 event terakhir</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-slate-300">
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">Troli</th>
                                <th class="px-6 py-3 text-left font-semibold">User</th>
                                <th class="px-6 py-3 text-left font-semibold">Status</th>
                                <th class="px-6 py-3 text-left font-semibold">Lokasi/Tujuan</th>
                                <th class="px-6 py-3 text-left font-semibold">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80" data-dashboard-table>
                            @include('admin.dashboard.partials.recent-rows', ['recentMovements' => $recentMovements])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-900/70 shadow-xl shadow-slate-950/20">
            <div class="flex items-center justify-between border-b border-slate-800 px-6 py-5">
                <h2 class="text-lg font-semibold text-white">User Pending</h2>
                <span class="text-xs text-slate-500">Terbaru</span>
            </div>
            <ul class="divide-y divide-slate-800/80 text-sm">
                @forelse($pendingUsers as $user)
                    <li class="flex flex-col gap-2 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-white">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $user->phone }}</p>
                            </div>
                            <span class="rounded-full border border-amber-400/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-100">PENDING</span>
                        </div>
                        <a href="{{ route('admin.approvals.show', $user) }}" class="inline-flex items-center text-xs font-semibold text-blue-300 hover:text-blue-200">
                            Lihat Detail
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </li>
                @empty
                    <li class="px-6 py-6 text-center text-slate-500">Tidak ada permintaan baru.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="hidden"></div>
</div>
@endsection
