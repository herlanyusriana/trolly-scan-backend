@php
    $title = 'Persetujuan Akun';
@endphp

@extends('layouts.admin')

@section('content')
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 shadow-xl shadow-slate-950/20 sm:rounded-3xl">
        <div class="flex flex-col gap-3 border-b border-slate-800 px-4 py-4 sm:gap-4 sm:px-6 sm:py-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-lg font-semibold text-white sm:text-xl">Persetujuan Akun Mobile</h1>
                <p class="text-xs text-slate-500 sm:text-sm">Kelola workflow persetujuan pengguna aplikasi mobile.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.approvals.index', ['status' => 'pending']) }}" class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide sm:px-4 {{ $status === 'pending' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'border border-slate-700 bg-slate-800/80 text-slate-300 hover:bg-slate-800' }}">
                    Pending
                </a>
                <a href="{{ route('admin.approvals.index', ['status' => 'approved']) }}" class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide sm:px-4 {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'border border-slate-700 bg-slate-800/80 text-slate-300 hover:bg-slate-800' }}">
                    Disetujui
                </a>
                <a href="{{ route('admin.approvals.index', ['status' => 'rejected']) }}" class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide sm:px-4 {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30' : 'border border-slate-700 bg-slate-800/80 text-slate-300 hover:bg-slate-800' }}">
                    Ditolak
                </a>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full text-sm text-slate-300">
                <thead class="bg-slate-900/70 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Nama</th>
                        <th class="px-6 py-3 text-left font-semibold">Telepon</th>
                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                        <th class="px-6 py-3 text-left font-semibold">Bergabung</th>
                        <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-900/60 transition">
                            <td class="px-6 py-4 font-medium text-white">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $user->phone }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $badge = match ($user->status) {
                                        'approved' => 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200',
                                        'rejected' => 'border-rose-400/40 bg-rose-500/10 text-rose-200',
                                        default => 'border-amber-400/40 bg-amber-500/10 text-amber-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $badge }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">{{ $user->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.approvals.show', $user) }}" class="inline-flex items-center gap-2 rounded-full border border-blue-500/40 px-4 py-2 text-xs font-semibold text-blue-200 transition hover:bg-blue-500/10">
                                    Detail
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12l-3.75 3.75M21 12H3" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="space-y-3 p-3 sm:p-4 lg:hidden">
            @forelse($users as $user)
                @php
                    $badge = match ($user->status) {
                        'approved' => 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200',
                        'rejected' => 'border-rose-400/40 bg-rose-500/10 text-rose-200',
                        default => 'border-amber-400/40 bg-amber-500/10 text-amber-200',
                    };
                @endphp
                <div class="rounded-2xl border border-slate-800/60 bg-slate-950/50 p-4 shadow-lg">
                    <div class="mb-3 flex items-start justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-white">{{ $user->name }}</h3>
                            <p class="mt-1 text-sm text-slate-400">{{ $user->phone }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold uppercase {{ $badge }}">
                            {{ $user->status }}
                        </span>
                    </div>

                    <div class="space-y-2 border-t border-slate-800/60 pt-3 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Bergabung:</span>
                            <span class="font-medium text-slate-200">{{ $user->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-800/40">
                        <a href="{{ route('admin.approvals.show', $user) }}" class="flex items-center justify-center gap-2 rounded-xl border border-blue-500/40 px-4 py-2.5 text-sm font-semibold text-blue-200 transition hover:bg-blue-500/10">
                            Lihat Detail
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12l-3.75 3.75M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-800/60 bg-slate-950/50 px-6 py-12 text-center">
                    <p class="text-sm text-slate-500">Belum ada data.</p>
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-800 px-4 py-3 sm:px-6 sm:py-4">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
@endsection
