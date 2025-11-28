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
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30">
                <p class="text-xs uppercase tracking-wide text-slate-300">Troli Masuk</p>
                <div class="mt-4 flex items-end gap-3">
                <span class="text-3xl font-semibold text-white" data-dashboard-in>{{ number_format($stats['trolleys']['in']) }}</span>
                    <span class="rounded-full border border-slate-600 bg-slate-800/80 px-3 py-1 text-xs font-semibold text-slate-200">IN</span>
                </div>
                <p class="mt-6 text-xs text-slate-400">Jumlah troli yang saat ini tersedia di area penyimpanan.</p>
            </div>

            <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30">
                <p class="text-xs uppercase tracking-wide text-slate-300">Troli Keluar</p>
                <div class="mt-4 flex items-end gap-3">
                <span class="text-3xl font-semibold text-white" data-dashboard-out>{{ number_format($stats['trolleys']['out']) }}</span>
                    <span class="rounded-full border border-slate-600 bg-slate-800/80 px-3 py-1 text-xs font-semibold text-slate-200">OUT</span>
                </div>
                <p class="mt-4 text-xs text-slate-400">Troli yang sedang digunakan dan belum melakukan check-in.</p>
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
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30">
                <p class="text-xs uppercase tracking-wide text-slate-400">User Mobile Disetujui</p>
                <div class="mt-4 text-3xl font-semibold text-white" data-dashboard-approved>{{ number_format($stats['mobile_users']['approved']) }}</div>
                <p class="mt-6 text-xs text-slate-500">User aktif yang dapat mengakses aplikasi mobile.</p>
            </div>

            <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30">
                <p class="text-xs uppercase tracking-wide text-slate-400">Permintaan Menunggu</p>
                <div class="mt-4 text-3xl font-semibold text-white" data-dashboard-pending>{{ number_format($stats['mobile_users']['pending']) }}</div>
                <p class="mt-6 text-xs text-slate-500">Permintaan akun yang perlu ditinjau oleh admin.</p>
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
