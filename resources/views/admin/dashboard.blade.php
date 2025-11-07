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
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-emerald-500/30 bg-emerald-500/10 p-6 shadow-xl shadow-emerald-900/30">
                <p class="text-xs uppercase tracking-wide text-slate-400">Troli Masuk</p>
                <div class="mt-4 flex items-end gap-3">
                <span class="text-3xl font-semibold text-white" data-dashboard-in>{{ number_format($stats['trolleys']['in']) }}</span>
                    <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $statusPills['in'] }}">IN</span>
                </div>
                <p class="mt-6 text-xs text-slate-500">Jumlah troli yang saat ini tersedia di area penyimpanan.</p>
            </div>

            <div class="rounded-3xl border border-rose-500/30 bg-rose-500/10 p-6 shadow-xl shadow-rose-900/30">
                <p class="text-xs uppercase tracking-wide text-slate-400">Troli Keluar</p>
                <div class="mt-4 flex items-end gap-3">
                <span class="text-3xl font-semibold text-white" data-dashboard-out>{{ number_format($stats['trolleys']['out']) }}</span>
                    <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $statusPills['out'] }}">OUT</span>
                </div>
                <p class="mt-6 text-xs text-slate-500">Troli yang sedang digunakan dan belum melakukan check-in.</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-blue-500/30 bg-blue-500/10 p-6 shadow-xl shadow-blue-900/30">
                <p class="text-xs uppercase tracking-wide text-slate-400">User Mobile Disetujui</p>
                <div class="mt-4 text-3xl font-semibold text-white" data-dashboard-approved>{{ number_format($stats['mobile_users']['approved']) }}</div>
                <p class="mt-6 text-xs text-slate-500">User aktif yang dapat mengakses aplikasi mobile.</p>
            </div>

            <div class="rounded-3xl border border-amber-500/30 bg-amber-500/10 p-6 shadow-xl shadow-amber-900/30">
                <p class="text-xs uppercase tracking-wide text-slate-400">Permintaan Menunggu</p>
                <div class="mt-4 text-3xl font-semibold text-white" data-dashboard-pending>{{ number_format($stats['mobile_users']['pending']) }}</div>
                <p class="mt-6 text-xs text-slate-500">Permintaan akun yang perlu ditinjau oleh admin.</p>
            </div>
        </div>
    </div>

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
                                <th class="px-6 py-3 text-left font-semibold">Waktu Keluar</th>
                                <th class="px-6 py-3 text-left font-semibold">Waktu Masuk</th>
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

    <div class="mt-8 grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-blue-500/30 bg-blue-500/10 p-5 shadow-lg shadow-blue-900/30">
            <p class="text-xs uppercase tracking-wide text-blue-200/70">Reinforce</p>
            <span class="mt-3 block text-2xl font-semibold text-blue-100" data-dashboard-kind="reinforce">{{ number_format($stats['trolleys']['kinds']['reinforce']) }}</span>
            <p class="mt-2 text-xs text-blue-200/60">Troli dengan struktur reinforce yang tercatat.</p>
        </div>
        <div class="rounded-3xl border border-purple-500/30 bg-purple-500/10 p-5 shadow-lg shadow-purple-900/30">
            <p class="text-xs uppercase tracking-wide text-purple-200/70">Backplate</p>
            <span class="mt-3 block text-2xl font-semibold text-purple-100" data-dashboard-kind="backplate">{{ number_format($stats['trolleys']['kinds']['backplate']) }}</span>
            <p class="mt-2 text-xs text-purple-200/60">Troli jenis backplate yang tersedia.</p>
        </div>
        <div class="rounded-3xl border border-cyan-500/30 bg-cyan-500/10 p-5 shadow-lg shadow-cyan-900/30">
            <p class="text-xs uppercase tracking-wide text-cyan-200/70">CompBase</p>
            <span class="mt-3 block text-2xl font-semibold text-cyan-100" data-dashboard-kind="compbase">{{ number_format($stats['trolleys']['kinds']['compbase']) }}</span>
            <p class="mt-2 text-xs text-cyan-200/60">Troli tipe compbase yang terdaftar.</p>
        </div>
    </div>
@endsection
