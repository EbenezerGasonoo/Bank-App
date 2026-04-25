<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', config('app.name', 'Poise Commerce Bank')) — {{ config('app.name', 'Poise Commerce Bank') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .theme-poise-retail .text-page-muted { color: #5c6c7d; }
    </style>
</head>
<body class="theme-poise-retail antialiased min-h-screen flex flex-col relative">
    <div class="pc-bank-page-bg" aria-hidden="true">
        <div class="pc-bank-orb pc-bank-orb-1"></div>
        <div class="pc-bank-orb pc-bank-orb-2"></div>
        <div class="pc-bank-orb pc-bank-orb-3"></div>
    </div>
    @include('partials.public-top-bar', ['current' => $current ?? null])
    @include('partials.public-nav', ['current' => $current ?? null])

    <main class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 pt-28 pb-16">
        <div class="pc-content-shell glass rounded-2xl p-6 sm:p-8 md:p-10 overflow-hidden">
            @yield('content')
        </div>
    </main>
</body>
</html>
