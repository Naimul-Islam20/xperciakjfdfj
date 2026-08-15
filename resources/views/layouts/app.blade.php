<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $site = $siteSettings ?? null;
        $defaultTitle = $site?->defaultMetaTitle() ?? 'Eco-friendly Disposable Packaging';
        $defaultDescription = $site?->defaultMetaDescription()
            ?? 'xperciainc offers a wide range of disposable food packaging for restaurants, cloud kitchens, catering, and takeaways.';
        $defaultKeywords = $site?->meta_keywords;
        $ogImage = $site?->ogImageUrl() ?? asset('images/logo-mark.svg');
        $favicon = $site?->faviconUrl() ?? asset('images/logo-mark.svg');
        $siteName = $site?->site_name ?: 'xperciainc';
        $faviconType = str_ends_with(parse_url($favicon, PHP_URL_PATH) ?? '', '.svg')
            ? 'image/svg+xml'
            : null;
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('title')@yield('title') — {{ $defaultTitle }}@else{{ $siteName }} — {{ $defaultTitle }}@endif</title>
    <meta name="description" content="@yield('meta_description', $defaultDescription)">
    @if ($defaultKeywords)
        <meta name="keywords" content="{{ $defaultKeywords }}">
    @endif

    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="@hasSection('title')@yield('title') — {{ $defaultTitle }}@else{{ $siteName }} — {{ $defaultTitle }}@endif">
    <meta property="og:description" content="@yield('meta_description', $defaultDescription)">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ $ogImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@hasSection('title')@yield('title') — {{ $defaultTitle }}@else{{ $siteName }} — {{ $defaultTitle }}@endif">
    <meta name="twitter:description" content="@yield('meta_description', $defaultDescription)">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="icon" href="{{ $favicon }}" @if ($faviconType) type="{{ $faviconType }}" @endif>
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-[#f3f5f2] text-brand-ink antialiased">
    @include('partials.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')

    @include('partials.search-overlay')
</body>
</html>
