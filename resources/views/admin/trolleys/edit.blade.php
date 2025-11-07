@php
    $title = 'Ubah Troli';
@endphp

@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-3xl rounded-3xl border border-slate-800 bg-slate-900/70 p-8 shadow-xl shadow-slate-950/20">
        <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-white">Ubah Troli</h1>
                <p class="text-sm text-slate-500">Perbarui informasi troli dan status operasionalnya.</p>
            </div>
            <span class="rounded-full border border-slate-700 bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300">
                Kode: {{ $trolley->code }}
            </span>
        </div>

        <form method="POST" action="{{ route('trolleys.update', $trolley) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.trolleys.partials.form', ['trolley' => $trolley])

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('trolleys.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-700 px-5 py-2 text-sm font-semibold text-slate-300 transition hover:bg-slate-800">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        @if ($trolley->qr_code_path)
            <div class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/80 p-6">
                <p class="text-sm font-semibold text-white">QR Code Saat Ini</p>
                <div class="mt-4 flex flex-col gap-6 sm:flex-row sm:items-center">
                    <img src="{{ asset('storage/' . $trolley->qr_code_path) }}" alt="QR Code Troli {{ $trolley->code }}" class="h-40 w-40 rounded-2xl border border-slate-800 bg-slate-950 object-contain p-3">
                    <div>
                        <a href="{{ asset('storage/' . $trolley->qr_code_path) }}" target="_blank" class="inline-flex items-center gap-2 rounded-2xl border border-blue-500/40 px-4 py-2 text-sm font-semibold text-blue-200 transition hover:bg-blue-500/10">
                            Buka / Unduh
                        </a>
                        <p class="mt-3 text-xs text-slate-500">QR code akan diperbarui otomatis bila kamu mengubah kode troli.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
