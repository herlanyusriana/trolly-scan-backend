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
        <div class="grid gap-3 sm:gap-4 lg:grid-cols-2">
            <a href="{{ route('admin.history.index') }}?status=in" class="group rounded-2xl border border-slate-800/70 bg-slate-900/70 p-4 shadow-xl shadow-slate-950/30 transition hover:border-emerald-500/50 hover:bg-slate-900 sm:rounded-3xl sm:p-6">
                <p class="text-xs uppercase tracking-wide text-slate-300">Troli Masuk</p>
                <div class="mt-3 flex items-end gap-2 sm:mt-4 sm:gap-3">
                    <span class="text-2xl font-semibold text-white transition group-hover:text-emerald-400 sm:text-3xl" data-dashboard-in>{{ number_format($stats['trolleys']['in']) }}</span>
                    <span class="rounded-full border border-emerald-600 bg-emerald-500/20 px-2 py-0.5 text-xs font-semibold text-emerald-200 sm:px-3 sm:py-1">IN</span>
                </div>
                <p class="mt-4 text-xs text-slate-400 sm:mt-6">Klik untuk melihat detail troli yang tersedia</p>
            </a>

            <a href="{{ route('admin.history.index') }}?status=out" class="group rounded-2xl border border-slate-800/70 bg-slate-900/70 p-4 shadow-xl shadow-slate-950/30 transition hover:border-blue-500/50 hover:bg-slate-900 sm:rounded-3xl sm:p-6">
                <p class="text-xs uppercase tracking-wide text-slate-300">Troli Keluar</p>
                <div class="mt-3 flex items-end gap-2 sm:mt-4 sm:gap-3">
                    <span class="text-2xl font-semibold text-white transition group-hover:text-blue-400 sm:text-3xl" data-dashboard-out>{{ number_format($stats['trolleys']['out']) }}</span>
                    <span class="rounded-full border border-blue-600 bg-blue-500/20 px-2 py-0.5 text-xs font-semibold text-blue-200 sm:px-3 sm:py-1">OUT</span>
                </div>
                <p class="mt-3 text-xs text-slate-400 sm:mt-4">Klik untuk melihat detail troli yang sedang digunakan</p>
                <div class="mt-4 grid gap-2 sm:mt-5 sm:grid-cols-3 sm:gap-3">
                    <div class="rounded-xl border border-slate-700 bg-slate-800/80 px-3 py-2 text-xs text-slate-100 sm:rounded-2xl sm:px-4 sm:py-3">
                        <p class="font-semibold">Reinforce</p>
                        <p class="mt-1">Keluar: <span class="font-bold" data-dashboard-kind-out="reinforce">{{ number_format($stats['trolleys']['kinds_out']['reinforce']) }}</span></p>
                    </div>
                    <div class="rounded-xl border border-slate-700 bg-slate-800/80 px-3 py-2 text-xs text-slate-100 sm:rounded-2xl sm:px-4 sm:py-3">
                        <p class="font-semibold">Backplate</p>
                        <p class="mt-1">Keluar: <span class="font-bold" data-dashboard-kind-out="backplate">{{ number_format($stats['trolleys']['kinds_out']['backplate']) }}</span></p>
                    </div>
                    <div class="rounded-xl border border-slate-700 bg-slate-800/80 px-3 py-2 text-xs text-slate-100 sm:rounded-2xl sm:px-4 sm:py-3">
                        <p class="font-semibold">CompBase</p>
                        <p class="mt-1">Keluar: <span class="font-bold" data-dashboard-kind-out="compbase">{{ number_format($stats['trolleys']['kinds_out']['compbase']) }}</span></p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Duration Category Cards -->
        <div class="grid gap-3 sm:gap-4 md:grid-cols-3">
            <a href="{{ route('admin.duration-category.index', ['category' => 'less_than_3']) }}" class="group rounded-2xl border border-slate-800/70 bg-slate-900/70 p-4 shadow-xl shadow-slate-950/30 transition hover:border-emerald-500/50 hover:bg-slate-900 sm:rounded-3xl sm:p-6">
                <p class="text-xs uppercase tracking-wide text-slate-300">✅ Kurang dari 3 Hari</p>
                <div class="mt-3 flex items-end gap-2 sm:mt-4 sm:gap-3">
                    <span class="text-2xl font-semibold text-white transition group-hover:text-emerald-400 sm:text-3xl" data-dashboard-less-3>{{ number_format($stats['trolleys']['duration_categories']['less_than_3']) }}</span>
                    <span class="rounded-full border border-emerald-600 bg-emerald-500/20 px-2 py-0.5 text-xs font-semibold text-emerald-200 sm:px-3 sm:py-1">TROLI</span>
                </div>
                <p class="mt-3 text-xs text-slate-400 sm:mt-4">Troli dalam kondisi normal</p>
            </a>

            <a href="{{ route('admin.duration-category.index', ['category' => 'between_3_and_6']) }}" class="group rounded-2xl border border-slate-800/70 bg-slate-900/70 p-4 shadow-xl shadow-slate-950/30 transition hover:border-amber-500/50 hover:bg-slate-900 sm:rounded-3xl sm:p-6">
                <p class="text-xs uppercase tracking-wide text-slate-300">⚠️ Antara 3-6 Hari</p>
                <div class="mt-3 flex items-end gap-2 sm:mt-4 sm:gap-3">
                    <span class="text-2xl font-semibold text-white transition group-hover:text-amber-400 sm:text-3xl" data-dashboard-3-6>{{ number_format($stats['trolleys']['duration_categories']['between_3_and_6']) }}</span>
                    <span class="rounded-full border border-amber-600 bg-amber-500/20 px-2 py-0.5 text-xs font-semibold text-amber-200 sm:px-3 sm:py-1">TROLI</span>
                </div>
                <p class="mt-3 text-xs text-slate-400 sm:mt-4">Perlu perhatian khusus</p>
            </a>

            <a href="{{ route('admin.duration-category.index', ['category' => 'more_than_6']) }}" class="group rounded-2xl border border-slate-800/70 bg-slate-900/70 p-4 shadow-xl shadow-slate-950/30 transition hover:border-rose-500/50 hover:bg-slate-900 sm:rounded-3xl sm:p-6">
                <p class="text-xs uppercase tracking-wide text-slate-300">🚨 Lebih dari 6 Hari</p>
                <div class="mt-3 flex items-end gap-2 sm:mt-4 sm:gap-3">
                    <span class="text-2xl font-semibold text-white transition group-hover:text-rose-400 sm:text-3xl" data-dashboard-more-6>{{ number_format($stats['trolleys']['duration_categories']['more_than_6']) }}</span>
                    <span class="rounded-full border border-rose-600 bg-rose-500/20 px-2 py-0.5 text-xs font-semibold text-rose-200 sm:px-3 sm:py-1">TROLI</span>
                </div>
                <p class="mt-3 text-xs text-slate-400 sm:mt-4">Segera tindak lanjut!</p>
            </a>
        </div>

        <!-- Other Stats -->
        <div class="grid gap-3 sm:gap-4 md:grid-cols-3">
            <a href="{{ route('admin.approvals.index') }}" class="group rounded-2xl border border-slate-800/70 bg-slate-900/70 p-4 shadow-xl shadow-slate-950/30 transition hover:border-blue-500/50 hover:bg-slate-900 sm:rounded-3xl sm:p-6">
                <p class="text-xs uppercase tracking-wide text-slate-300">User Mobile Disetujui</p>
                <div class="mt-3 flex items-end gap-2 sm:mt-4 sm:gap-3">
                    <span class="text-2xl font-semibold text-white transition group-hover:text-blue-400 sm:text-3xl" data-dashboard-approved>{{ number_format($stats['mobile_users']['approved']) }}</span>
                </div>
                <p class="mt-4 text-xs text-slate-400 sm:mt-6">Klik untuk melihat daftar user</p>
            </a>

            <a href="{{ route('admin.approvals.index') }}" class="group rounded-2xl border border-slate-800/70 bg-slate-900/70 p-4 shadow-xl shadow-slate-950/30 transition hover:border-amber-500/50 hover:bg-slate-900 sm:rounded-3xl sm:p-6">
                <p class="text-xs uppercase tracking-wide text-slate-300">👤 User Pending</p>
                <div class="mt-3 flex items-end gap-2 sm:mt-4 sm:gap-3">
                    <span class="text-2xl font-semibold text-white transition group-hover:text-amber-400 sm:text-3xl" data-dashboard-pending>{{ number_format($stats['mobile_users']['pending']) }}</span>
                    <span class="rounded-full border border-amber-600 bg-amber-500/20 px-2 py-0.5 text-xs font-semibold text-amber-200 sm:px-3 sm:py-1">USER</span>
                </div>
                <p class="mt-3 text-xs text-slate-400 sm:mt-4">Klik untuk approve user baru</p>
            </a>

            <a href="{{ route('trolleys.index') }}" class="group rounded-2xl border border-slate-800/70 bg-slate-900/70 p-4 shadow-xl shadow-slate-950/30 transition hover:border-blue-500/50 hover:bg-slate-900 sm:rounded-3xl sm:p-6">
                <p class="text-xs uppercase tracking-wide text-slate-300">🛒 Jumlah Troli</p>
                <div class="mt-3 flex items-end gap-2 sm:mt-4 sm:gap-3">
                    <span class="text-2xl font-semibold text-white transition group-hover:text-blue-400 sm:text-3xl">{{ number_format($stats['trolleys']['total']) }}</span>
                    <span class="rounded-full border border-blue-600 bg-blue-500/20 px-2 py-0.5 text-xs font-semibold text-blue-200 sm:px-3 sm:py-1">TROLI</span>
                </div>
                <p class="mt-3 text-xs text-slate-400 sm:mt-4">Total semua troli</p>
            </a>
        </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 shadow-xl shadow-slate-950/20 sm:rounded-3xl">
        <div class="flex flex-col gap-2 border-b border-slate-800 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:py-5">
            <h2 class="text-base font-semibold text-white sm:text-lg">Pergerakan Troli Terbaru</h2>
            <span class="text-xs text-slate-500">20 event terakhir</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs text-slate-300 sm:text-sm">
                <thead class="bg-slate-900/70 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold sm:px-6 sm:py-3">Troli</th>
                        <th class="px-3 py-2 text-left font-semibold sm:px-6 sm:py-3">User</th>
                        <th class="px-3 py-2 text-left font-semibold sm:px-6 sm:py-3">Status</th>
                        <th class="px-3 py-2 text-left font-semibold sm:px-6 sm:py-3">Lokasi/Tujuan</th>
                        <th class="px-3 py-2 text-left font-semibold sm:px-6 sm:py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80" data-dashboard-table>
                    @include('admin.dashboard.partials.recent-rows', ['recentMovements' => $recentMovements])
                </tbody>
            </table>
        </div>
    </div>

    <div class="hidden"></div>
</div>
@endsection
