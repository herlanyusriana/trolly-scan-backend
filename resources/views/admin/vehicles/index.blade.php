@php
    $title = 'Kendaraan Operasional';
@endphp

@extends('layouts.admin')

@section('content')
    <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 shadow-xl shadow-slate-950/30">
        <div class="flex flex-col gap-4 border-b border-slate-800/60 px-6 py-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-white">Manajemen Kendaraan</h1>
                <p class="text-sm text-slate-400">Kelola daftar kendaraan yang digunakan untuk distribusi troli.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Kendaraan
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-slate-200">
                <thead class="bg-slate-900/70 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Plat Nomor</th>
                        <th class="px-6 py-3 text-left font-semibold">Nama</th>
                        <th class="px-6 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                        <th class="px-6 py-3 text-left font-semibold">Catatan</th>
                        <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse ($vehicles as $vehicle)
                        @php
                            $statusClasses = [
                                'available' => 'border-emerald-400/40 bg-emerald-500/10 text-emerald-200',
                                'maintenance' => 'border-amber-400/40 bg-amber-500/10 text-amber-100',
                                'inactive' => 'border-slate-500/40 bg-slate-600/10 text-slate-200',
                            ];
                        @endphp
                        <tr class="transition hover:bg-slate-900/60">
                            <td class="px-6 py-4 font-semibold text-white">{{ $vehicle->plate_number }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $vehicle->name ?? '–' }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $vehicle->category ?? '–' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $statusClasses[$vehicle->status] ?? 'border-slate-600 bg-slate-700/40 text-slate-200' }}">
                                    {{ $vehicle->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $vehicle->notes ?? '–' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center gap-1 rounded-full border border-amber-400/40 px-3 py-1.5 text-xs font-semibold text-amber-200 transition hover:bg-amber-500/10">
                                        Edit
                                    </a>
                                    <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Yakin hapus kendaraan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-rose-400/40 px-3 py-1.5 text-xs font-semibold text-rose-200 transition hover:bg-rose-500/10">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada data kendaraan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-800/60 px-6 py-4">
            {{ $vehicles->links() }}
        </div>
    </div>
@endsection

