@php
    $title = 'Tambah Driver';
@endphp

@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-3xl rounded-3xl border border-slate-800/70 bg-slate-900/70 p-8 shadow-xl shadow-slate-950/20">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-white">Tambah Driver</h1>
            <p class="mt-2 text-sm text-slate-500">Input data driver untuk pemilihan cepat di aplikasi mobile.</p>
        </div>

        <form method="POST" action="{{ route('drivers.store') }}" class="space-y-6">
            @csrf
            @include('admin.drivers.partials.form', ['driver' => null])

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('drivers.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-700 px-5 py-2 text-sm font-semibold text-slate-300 transition hover:bg-slate-800/80">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500">
                    Simpan Driver
                </button>
            </div>
        </form>
    </div>
@endsection

