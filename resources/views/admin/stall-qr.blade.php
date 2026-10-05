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
        @media print { .no-print { display: none !important; } body { background: #fff; } .qr-poster { border: 0 !important; } }
    </style>
</head>
<body class="bg-neutral-50 font-sans antialiased text-neutral-900">

<div class="max-w-xl mx-auto px-4 py-6">
    <a href="{{ route('admin.stalls') }}" class="no-print text-xs font-bold text-brand-700 hover:text-brand-800">&larr; Back to Stalls</a>

    <div class="mt-4">
        @include('partials.stall-qr')
    </div>
</div>

</body>
</html>
