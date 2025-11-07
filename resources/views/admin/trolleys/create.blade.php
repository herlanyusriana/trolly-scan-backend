@php
    $title = 'Tambah Troli';
@endphp

@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-3xl rounded-3xl border border-slate-800 bg-slate-900/70 p-8 shadow-xl shadow-slate-950/20">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-white">Tambah Troli</h1>
            <p class="mt-2 text-sm text-slate-500">Masukkan detail troli dan status operasional saat ini.</p>
        </div>

        <form method="POST" action="{{ route('trolleys.store') }}" class="space-y-6">
            @csrf
            @include('admin.trolleys.partials.form', ['trolley' => null])

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('trolleys.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-700 px-5 py-2 text-sm font-semibold text-slate-300 transition hover:bg-slate-800">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500">
                    Simpan Troli
                </button>
            </div>
        </form>

        <p class="mt-6 text-xs text-slate-500">
            QR code akan dibuat otomatis setelah troli tersimpan. Jalankan perintah <code class="rounded bg-slate-800 px-2 py-1 text-[10px] text-slate-300">php artisan storage:link</code> agar file dapat diakses publik.
        </p>
    </div>
@endsection
