<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Code · {{ $stall->name }} | DSS</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css'])
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js" defer></script>
    <style>
        @page { margin: 16mm; }
        /* CHANGED: the poster's print border rule is gone; its print:border-0 class covers it. */
        @media print { .no-print { display: none !important; } body { background: #fff; } }
    </style>
</head>
<body class="bg-neutral-50 font-sans antialiased text-neutral-900">

<div class="max-w-xl mx-auto px-4 py-6">
    {{-- CHANGED: uses the shared back link (drawn arrow icon instead of the &larr; text glyph), still 44px tall and hidden in print. --}}
    @include('partials.back-link', ['href' => route('admin.stalls'), 'label' => 'Back to Stalls', 'class' => 'no-print'])

    <div class="mt-4">
        @include('partials.stall-qr')
    </div>
</div>

</body>
</html>
