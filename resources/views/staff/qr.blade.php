@extends('layouts.dashboard')

@section('title', 'Stall QR Code | DSS')
@section('header_title', 'QR Code')

@section('head')
<style>
    @page { margin: 16mm; }
    @media print {
        .dashboard-sidebar, .dashboard-header, .sidebar-overlay, .no-print { display: none !important; }
        .dashboard-layout { display: block !important; }
        main { padding: 0 !important; }
        body { background: #fff !important; }
        /* CHANGED: the poster's print border rule is gone; its print:border-0 class covers it. */
    }
</style>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="no-print mb-4">
        <h1 class="text-lg sm:text-xl font-bold text-neutral-900 tracking-tight">Your stall's QR code</h1>
        {{-- CHANGED: darker text, and the code is for the counter only, so the group-chat suggestion is gone. --}}
        <p class="text-xs text-neutral-600 mt-1">
            Students scan this code at your counter to rate {{ $stall->name }}. Print it and post it where customers order.
        </p>
    </div>

    @include('partials.stall-qr')
</div>
@endsection
