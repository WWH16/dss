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
        .qr-poster { border: 0 !important; }
    }
</style>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="no-print mb-4">
        <h1 class="text-lg sm:text-xl font-bold text-neutral-900 tracking-tight">Your stall's QR code</h1>
        <p class="text-xs text-neutral-500 mt-1">
            Students scan this code to rate {{ $stall->name }}. Post it at your counter, or download it and share it in your group chats and pages.
        </p>
    </div>

    @include('partials.stall-qr')
</div>
@endsection
