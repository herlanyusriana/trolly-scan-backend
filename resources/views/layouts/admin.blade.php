<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Panel Admin' }} | In-Out Trolley</title>
        <script>
            (() => {
                const initQrSelection = () => {
                    window.qrSelection = ({ ids = [] } = {}) => {
                        const normalizeIds = (values) => {
                            return Array.from(
                                new Set(
                                    values
                                        .map((value) => Number(value))
                                        .filter((value) => Number.isInteger(value) && value > 0),
                                ),
                            );
                        };

                        return {
                            ids: normalizeIds(ids),
                            selected: [],
                            allSelected: false,
                            get selectedSize() {
                                return this.selected.length;
                            },
                            isSelected(id) {
                                const normalized = Number(id);
                                return this.selected.includes(normalized);
                            },
                            toggle({ id }) {
                                const normalized = Number(id);
                                if (!this.ids.includes(normalized)) {
                                    return;
                                }

                                if (this.isSelected(normalized)) {
                                    this.selected = this.selected.filter((value) => value !== normalized);
                                } else {
                                    this.selected = [...this.selected, normalized];
                                }

                                this.allSelected = this.selected.length === this.ids.length && this.ids.length > 0;
                            },
                            toggleSelectAll() {
                                const checkboxes = document.querySelectorAll('input[data-qr-checkbox]');

                                if (this.allSelected) {
                                    this.selected = [];
                                    this.allSelected = false;
                                    checkboxes.forEach((checkbox) => {
                                        checkbox.checked = false;
                                    });
                                    return;
                                }

                                this.selected = [...this.ids];
                                this.allSelected = this.ids.length > 0;
                                checkboxes.forEach((checkbox) => {
                                    const id = parseInt(checkbox.value, 10);
                                    checkbox.checked = this.isSelected(id);
                                });
                            },
                            get printHref() {
                                if (this.selected.length === 0) {
                                    return '#';
                                }

                                const params = new URLSearchParams();
                                params.set('ids', this.selected.join(','));

                                return '{{ route('trolleys.print') }}' + '?' + params.toString();
                            },
                        };
                    };
                };

                if (typeof window.qrSelection !== 'function') {
                    initQrSelection();
                }

                document.addEventListener('alpine:init', initQrSelection);
            })();
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="relative overflow-x-hidden bg-slate-950 text-slate-100 antialiased" x-data="{ mobileMenuOpen: false }">
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-32 -left-24 h-72 w-72 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div class="absolute top-1/3 -right-32 h-80 w-80 rounded-full bg-emerald-500/15 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/4 h-64 w-64 rounded-full bg-purple-600/20 blur-3xl"></div>
        </div>
        @auth('admin')
            @php
                $navigation = [
                    [
                        'label' => 'Dashboard Overview',
                        'route' => 'admin.dashboard',
                        'active' => ['admin.dashboard'],
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12l8.955-8.955c.293-.293.767-.293 1.06 0L22.5 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>',
                    ],
                    [
                        'label' => 'Trolley Management',
                        'route' => 'trolleys.index',
                        'active' => ['trolleys.index', 'trolleys.create', 'trolleys.edit', 'trolleys.update'],
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h1.5l1.313 7.868A2.25 2.25 0 008.04 12.75h7.92a2.25 2.25 0 002.227-1.882L19.5 6.75H5.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 19.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3zM18 19.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" /></svg>',
                    ],
                    [
                        'label' => 'Vehicle Management',
                        'route' => 'vehicles.index',
                        'active' => ['vehicles.index', 'vehicles.create', 'vehicles.edit', 'vehicles.update'],
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 014.5-4.5h10.5a4.5 4.5 0 014.5 4.5v2.25A1.75 1.75 0 0120 19h-1.057a2.25 2.25 0 01-4.386 0H9.443a2.25 2.25 0 01-4.386 0H4A1.75 1.75 0 012.25 17.25V15zm4.5-6a3 3 0 116 0h-6zm9 0a3 3 0 116 0h-6z" /></svg>',
                    ],
                    [
                        'label' => 'Driver Management',
                        'route' => 'drivers.index',
                        'active' => ['drivers.index', 'drivers.create', 'drivers.edit', 'drivers.update'],
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0V21H4.5v-.75z" /></svg>',
                    ],
                    [
                        'label' => 'QR Code Management',
                        'route' => 'trolleys.qr.index',
                        'active' => ['trolleys.qr.*'],
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5h6v6h-6v-6zM13.5 4.5h6v6h-6v-6zM4.5 13.5h6v6h-6v-6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 13.5h1.5a3 3 0 013 3V21M13.5 16.5H15m-1.5 3H15M13.5 21H15" /></svg>',
                    ],
                    [
                        'label' => 'Movement History',
                        'route' => 'admin.history.index',
                        'active' => ['admin.history.*'],
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v4.5l3 1.5m5.25-2.25a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                    ],
                    [
                        'label' => 'Mobile Approvals',
                        'route' => 'admin.approvals.index',
                        'active' => ['admin.approvals.*'],
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25 4.5-4.5m.75 5.25v3.375c0 .621-.504 1.125-1.125 1.125H6.375A1.125 1.125 0 015.25 19.125V6.375C5.25 5.754 5.754 5.25 6.375 5.25h7.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 2.25h3.375c.621 0 1.125.504 1.125 1.125V6.75" /></svg>',
                    ],
                ];
            @endphp

            <div class="flex min-h-screen overflow-hidden">
                <!-- Mobile Sidebar Overlay -->
                <div
                    x-show="mobileMenuOpen"
                    x-transition.opacity
                    @click="mobileMenuOpen = false"
                    class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden"
                ></div>

                <!-- Sidebar -->
                <aside
                    class="fixed inset-y-0 left-0 z-50 w-64 flex-col border-r border-slate-800 bg-slate-900/95 px-6 py-8 backdrop-blur-sm transition-transform duration-300 lg:static lg:flex"
                    :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                    x-cloak
                >
                    <div class="flex items-center justify-between gap-2 text-lg font-semibold text-blue-400">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/logo GCI.png') }}" alt="PT Geum Cheon Indo" class="h-10 w-auto rounded-xl bg-white/5 p-1">
                            In-Out Trolley
                        </div>
                        <!-- Close button for mobile -->
                        <button
                            @click="mobileMenuOpen = false"
                            class="rounded-lg p-1 text-slate-400 hover:bg-slate-800 hover:text-white lg:hidden"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <nav class="mt-10 flex flex-1 flex-col gap-1 text-sm">
                        @foreach ($navigation as $item)
                            @php
                                $patterns = $item['active'] ?? [$item['route']];
                                $isActive = request()->routeIs(...$patterns);
                            @endphp
                            <a
                                href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium transition {{ $isActive ? 'bg-blue-500/20 text-white' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                                @click="mobileMenuOpen = false"
                            >
                                {!! $item['icon'] !!}
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="mt-auto">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-rose-500/20 px-4 py-3 text-sm font-semibold text-rose-200 transition hover:bg-rose-500/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H8.25m9.75 0l-3 3m3-3l-3-3" />
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </aside>

                <div class="flex flex-1 flex-col">
                    <header class="border-b border-slate-800/60 bg-slate-900/70 px-4 py-4 backdrop-blur sm:px-6 sm:py-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-center gap-3">
                                <!-- Mobile Menu Button -->
                                <button
                                    @click="mobileMenuOpen = !mobileMenuOpen"
                                    class="rounded-lg border border-slate-700 bg-slate-800/80 p-2 text-slate-300 transition hover:bg-slate-800 lg:hidden"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                    </svg>
                                </button>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-slate-400">Admin Panel</p>
                                    <h1 class="text-xl font-semibold text-white sm:text-2xl">{{ $title ?? 'Dashboard' }}</h1>
                                </div>
                            </div>
                            <div class="hidden items-center gap-3 lg:flex">
                                <span class="rounded-full border border-slate-700/60 bg-slate-800/80 px-3 py-1 text-xs font-semibold text-slate-300">
                                    {{ now()->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                        <!-- Mobile horizontal navigation - removed as we now have sidebar -->
                        <div class="mt-4 hidden gap-2 overflow-x-auto whitespace-nowrap rounded-2xl border border-slate-800/70 bg-slate-900/70 px-3 py-3">
                            @foreach ($navigation as $item)
                                @php
                                    $patterns = $item['active'] ?? [$item['route']];
                                    $isActive = request()->routeIs(...$patterns);
                                @endphp
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="inline-flex items-center gap-2 rounded-full border px-3 py-2 text-xs font-semibold transition {{ $isActive ? 'border-blue-500/60 bg-blue-600/80 text-white' : 'border-slate-700 bg-slate-900/80 text-slate-300' }}"
                                >
                                    {!! $item['icon'] !!}
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </header>

                    <main class="flex-1 overflow-y-auto px-3 py-4 sm:px-5 sm:py-5 lg:px-8">
                        <div class="mx-auto flex w-full max-w-7xl flex-col gap-4">
                            @if (session('status'))
                                <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @yield('content')
                        </div>
                    </main>
                </div>
            </div>
        @endauth

        @guest('admin')
            <div class="flex min-h-screen items-center justify-center bg-slate-950 px-4 py-12">
                <div class="grid w-full max-w-5xl gap-8 md:grid-cols-2">
                    <div class="relative hidden overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-blue-500/30 via-blue-700/20 to-slate-950 p-8 shadow-2xl md:flex">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(59,130,246,0.4),transparent_45%),radial-gradient(circle_at_80%_0%,rgba(147,197,253,0.4),transparent_35%)]"></div>
                        <div class="relative flex flex-col justify-between">
                            <div>
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-500/20 text-lg font-semibold text-blue-100">IT</span>
                                <h2 class="mt-6 text-3xl font-semibold text-white">In-Out Trolley</h2>
                                <p class="mt-3 text-sm text-blue-100/90">
                                    Pantau pergerakan troli secara real-time dan kelola proses check-in/out dengan cepat dan akurat.
                                </p>
                            </div>
                            <div class="mt-12 space-y-2 text-xs text-slate-200/60">
                                <p>• Statistik penggunaan harian & riwayat event lengkap</p>
                                <p>• Approval user mobile dengan audit trail</p>
                                <p>• QR code generator untuk identifikasi troli</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-800/80 bg-slate-900/80 p-8 shadow-xl backdrop-blur">
                        <div class="mb-8 text-center">
                            <h1 class="text-2xl font-semibold text-white">Masuk Admin</h1>
                            <p class="mt-2 text-sm text-slate-400">Gunakan akun panel untuk mengakses dashboard operasional.</p>
                        </div>
                        @if (session('status'))
                            <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow">
                                {{ session('status') }}
                            </div>
                        @endif
                        @yield('content')
                    </div>
                </div>
            </div>
        @endguest

        @stack('scripts')
    </body>
</html>
