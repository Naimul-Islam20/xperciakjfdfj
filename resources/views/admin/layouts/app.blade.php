<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') — xperciainc</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-brand-mist text-brand-ink antialiased">
    @php
        $adminNav = [
            [
                'route' => 'admin.dashboard',
                'label' => 'Dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
            ],
            [
                'route' => 'admin.contacts.index',
                'label' => 'Contact',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
            ],
            [
                'route' => 'admin.products.index',
                'label' => 'Products',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
            ],
            [
                'route' => 'admin.categories.index',
                'label' => 'Categories',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>',
            ],
            [
                'route' => 'admin.subcategories.index',
                'label' => 'Subcategories',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h10M4 14h16M4 18h10"/>',
            ],
            [
                'route' => 'admin.home-page.index',
                'label' => 'Home Page',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm0 8a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zm10 0a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>',
            ],
            [
                'route' => 'admin.site-info.index',
                'label' => 'Site Info',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>',
            ],
            [
                'route' => 'admin.about.index',
                'label' => 'About Us',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-8H7v8M7 3v5h8"/>',
            ],
            [
                'route' => 'admin.admins.index',
                'label' => 'Admins',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
            ],
        ];
        $adminLogo = ($siteSettings ?? null)?->logoUrl() ?? asset('images/logo-mark.svg');
        $adminSiteName = ($siteSettings ?? null)?->site_name ?: 'Admin';
    @endphp

    <div class="admin-shell flex min-h-screen w-full">
        {{-- Desktop sidebar --}}
        <aside class="admin-sidebar hidden w-64 shrink-0 border-r border-brand-ink/10 bg-white lg:flex lg:flex-col">
            <div class="flex h-20 items-center justify-center border-b border-brand-ink/10 px-4">
                <a href="{{ route('admin.dashboard') }}" class="flex h-full w-full items-center justify-center py-1">
                    <img src="{{ $adminLogo }}" alt="{{ $adminSiteName }}" class="h-full w-auto max-w-full object-contain">
                </a>
            </div>
            <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                @foreach ($adminNav as $item)
                    @php
                        $isActive = request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']);
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $isActive ? 'bg-brand-ink text-white' : 'text-brand-ink/70 hover:bg-brand-mist hover:text-brand-ink' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            {!! $item['icon'] !!}
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Mobile sidebar drawer --}}
        <div class="admin-mobile-sidebar lg:hidden" hidden data-admin-sidebar>
            <button type="button" class="admin-mobile-sidebar-backdrop" data-admin-sidebar-close aria-label="Close menu"></button>
            <aside class="admin-mobile-sidebar-panel" role="dialog" aria-modal="true" aria-label="Admin menu">
                <div class="admin-mobile-sidebar-head">
                    <img src="{{ $adminLogo }}" alt="{{ $adminSiteName }}" class="h-10 w-auto max-w-[140px] object-contain">
                    <button type="button" class="admin-mobile-sidebar-close" data-admin-sidebar-close aria-label="Close menu">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
                <nav class="admin-mobile-sidebar-nav">
                    @foreach ($adminNav as $item)
                        @php
                            $isActive = request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']);
                        @endphp
                        <a href="{{ route($item['route']) }}"
                           class="admin-mobile-sidebar-link {{ $isActive ? 'is-active' : '' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                {!! $item['icon'] !!}
                            </svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </aside>
        </div>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="admin-topbar border-b border-brand-ink/10 bg-white px-4 sm:px-6">
                <div class="flex h-16 items-center justify-between gap-3 lg:h-20">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button"
                                id="admin-menu-toggle"
                                class="inline-flex shrink-0 items-center justify-center border-0 bg-transparent p-0 lg:hidden"
                                aria-label="Open menu"
                                aria-expanded="false">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <h1 class="truncate font-display text-lg font-semibold sm:text-xl">@yield('heading', 'Dashboard')</h1>
                            @hasSection('subheading')
                                <p class="mt-0.5 hidden truncate text-sm text-brand-ink/60 sm:block">@yield('subheading')</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                        <span class="hidden max-w-[10rem] truncate text-sm text-brand-ink/60 md:inline">{{ auth()->user()->name }}</span>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-ink/15 px-2.5 py-1.5 text-sm font-medium hover:bg-brand-mist sm:px-3">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="admin-main w-full flex-1 px-2 py-4 sm:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="admin-toast-stack" data-admin-toast-stack aria-live="polite" aria-relevant="additions"></div>

    @php
        $adminToasts = array_values(array_filter([
            session('success') ? ['type' => 'success', 'message' => session('success')] : null,
            session('error') ? ['type' => 'error', 'message' => session('error')] : null,
            session('warning') ? ['type' => 'warning', 'message' => session('warning')] : null,
            session('status') ? ['type' => 'success', 'message' => session('status')] : null,
        ]));
    @endphp

    <script type="application/json" data-admin-toasts>@json($adminToasts)</script>
</body>
</html>
