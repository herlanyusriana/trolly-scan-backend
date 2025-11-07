@php
    $title = 'Detail User';
@endphp

@extends('layouts.admin')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/20">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-white">{{ $mobileUser->name }}</h1>
                    @php
                        $badge = match ($mobileUser->status) {
                            'approved' => 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200',
                            'rejected' => 'border-rose-400/40 bg-rose-500/10 text-rose-200',
                            default => 'border-amber-400/40 bg-amber-500/10 text-amber-200',
                        };
                    @endphp
                    <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $badge }}">
                        {{ $mobileUser->status }}
                    </span>
                </div>

                <dl class="mt-6 space-y-4 text-sm text-slate-300">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Telepon</dt>
                        <dd class="font-medium text-white">{{ $mobileUser->phone }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Email</dt>
                        <dd class="font-medium text-white">{{ $mobileUser->email ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Diajukan</dt>
                        <dd class="font-medium text-white">{{ $mobileUser->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Terakhir Diperbarui</dt>
                        <dd class="font-medium text-white">{{ $mobileUser->updated_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 shadow-xl shadow-slate-950/20">
                <div class="border-b border-slate-800 px-6 py-5">
                    <h2 class="text-lg font-semibold text-white">Riwayat Persetujuan</h2>
                    <p class="text-sm text-slate-500">Catatan keputusan admin terhadap akun ini.</p>
                </div>
                <ul class="divide-y divide-slate-800/80 text-sm">
                    @forelse($mobileUser->approvalLogs()->latest()->get() as $log)
                        @php
                            $logBadge = $log->decision === 'approved'
                                ? 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200'
                                : 'border-rose-400/40 bg-rose-500/10 text-rose-200';
                        @endphp
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $logBadge }}">
                                    {{ $log->decision }}
                                </span>
                                <span class="text-xs text-slate-500">{{ $log->decision_at->format('d M Y H:i') }}</span>
                            </div>
                            <p class="mt-3 text-sm text-slate-300">{{ $log->notes ?? '-' }}</p>
                        </li>
                    @empty
                        <li class="px-6 py-6 text-center text-slate-500">Belum ada riwayat.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    @if ($mobileUser->status === 'pending')
        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/20">
                <h2 class="text-lg font-semibold text-white">Setujui Akun</h2>
                <p class="mt-1 text-sm text-slate-500">Berikan akses aplikasi mobile kepada pengguna.</p>
                <form method="POST" action="{{ route('admin.approvals.approve', $mobileUser) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="notes" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Catatan (opsional)</label>
                        <textarea id="notes" name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-500">
                        Setujui Akun
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/20">
                <h2 class="text-lg font-semibold text-white">Tolak Akun</h2>
                <p class="mt-1 text-sm text-slate-500">Berikan alasan yang jelas agar user dapat melakukan perbaikan.</p>
                <form method="POST" action="{{ route('admin.approvals.reject', $mobileUser) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="reason" class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Alasan Penolakan</label>
                        <textarea id="reason" name="reason" rows="3" required class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500/30"></textarea>
                        @error('reason')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-600/30 transition hover:bg-rose-500">
                        Tolak Akun
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection
