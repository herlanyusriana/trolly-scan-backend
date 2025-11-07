@php
    $title = 'Masuk';
@endphp

@extends('layouts.admin')

@section('content')
    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-800/80 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                placeholder="admin@example.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-300">Kata Sandi</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-800/80 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                placeholder="********"
            >
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-xs text-slate-400">
                <input type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500">
                Ingat saya
            </label>

            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500">
                Masuk
            </button>
        </div>
    </form>
@endsection
