<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DSS Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="dns-prefetch" href="https://unpkg.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Sora:wght@700;800&display=swap" rel="stylesheet">

    {{-- Ionicons (iOS Icon System) --}}
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" defer></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js" defer></script>

    @yield('head')

    <style>
        body { 
            background-color: var(--color-surface-alt); 
        }
    </style>
</head>
<body class="font-sans antialiased text-ink-900 min-h-screen flex flex-col p-4 sm:p-6 lg:p-8">

    @yield('content')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                if (window.showToast) window.showToast(@json(session('success')), 'success', 4000);
            @endif
            @if(session('error'))
                if (window.showToast) window.showToast(@json(session('error')), 'error', 5000);
            @endif
            @if(session('warning'))
                if (window.showToast) window.showToast(@json(session('warning')), 'warning', 4500);
            @endif
            @if(session('info') || session('status'))
                if (window.showToast) window.showToast(@json(session('info') ?? session('status')), 'info', 4000);
            @endif
        });
    </script>
    @yield('scripts')

</body>
</html>
