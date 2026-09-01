<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard | DSS')</title>
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
        /* Dashboard Layout Styles */
        body { background-color: var(--color-surface-alt); }
        .dashboard-layout {
            display: grid;
            grid-template-columns: 15rem minmax(0, 1fr);
            min-height: 100vh;
        }
        .dashboard-sidebar {
            background-color: oklch(0.22 0.09 155);
            border-right: 1px solid oklch(0.30 0.10 155);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            height: 100dvh;
            z-index: 40;
        }
        .dashboard-header {
            background-color: var(--color-surface);
            border-bottom: 1px solid oklch(0.84 0.10 155 / 0.35);
            height: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 var(--space-6);
            position: sticky;
            top: 0;
            z-index: 30;
        }
        .sidebar-brand {
            border-bottom-color: oklch(0.30 0.10 155);
        }
        .sidebar-brand-text {
            color: oklch(0.90 0.06 155);
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-2) var(--space-4);
            color: oklch(0.78 0.06 155);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: var(--radius-md);
            margin: 0 var(--space-3) 2px;
            transition: all 0.15s ease;
            letter-spacing: -0.01em;
        }
        .sidebar-link:hover {
            background-color: oklch(0.30 0.09 155);
            color: oklch(0.95 0.04 155);
        }
        .sidebar-link.active {
            background-color: oklch(0.48 0.15 155);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 1px 4px oklch(0.48 0.15 155 / 0.30);
        }
        .sidebar-link ion-icon {
            font-size: 1.15rem;
            flex-shrink: 0;
            transition: transform 0.15s ease;
        }
        .sidebar-link:hover ion-icon {
            transform: scale(1.08);
        }
        .sidebar-footer {
            border-top-color: oklch(0.30 0.10 155);
        }
        .sidebar-logout {
            color: oklch(0.72 0.08 28) !important;
        }
        .sidebar-logout:hover {
            background-color: oklch(0.28 0.08 28) !important;
            color: oklch(0.90 0.08 28) !important;
        }
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--color-ink-700);
            padding: var(--space-2);
        }
        
        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 35;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        @media (max-width: 768px) {
            .dashboard-layout { grid-template-columns: minmax(0, 1fr); }

            .dashboard-sidebar {
                position: fixed;
                left: -15rem;
                top: 0;
                height: 100vh;
                height: 100dvh;
                transition: left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                width: 15rem;
                box-shadow: 0 0 30px oklch(0.14 0.01 255 / 0.25);
            }
            .dashboard-sidebar.open { left: 0; }
            .mobile-toggle { 
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 44px;
                min-height: 44px;
                margin-left: -8px;
            }
            .sidebar-overlay.open { display: block; opacity: 1; }
        }
    </style>
</head>
<body class="font-sans antialiased text-ink-900">

<div class="dashboard-layout">
    {{-- ── Sidebar Mobile Overlay ────────────────────────────────────── --}}
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside class="dashboard-sidebar" id="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand h-16 flex items-center gap-3 px-6 border-b mb-4 text-decoration-none cursor-default select-none">
            <img src="{{ asset('assets/images/isu_logo.png') }}" alt="" width="28" height="28">
            <span class="sidebar-brand-text font-display font-bold leading-tight">DSS Portal</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto">
            @php $user = Auth::user(); @endphp
            
            @if($user && $user->role === 'student')
                <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <ion-icon name="grid-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    Dashboard
                </a>
                <a href="{{ route('student.profile') }}" class="sidebar-link {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                    <ion-icon name="person-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    My Profile
                </a>
                <a href="{{ route('student.evaluation') }}" class="sidebar-link {{ request()->routeIs('student.evaluation') ? 'active' : '' }}">
                    <ion-icon name="create-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    Evaluate
                </a>
                <a href="{{ route('student.history') }}" class="sidebar-link {{ request()->routeIs('student.history') ? 'active' : '' }}">
                    <ion-icon name="time-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    History
                </a>
            @elseif($user && $user->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <ion-icon name="grid-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    Overview
                </a>
                <a href="{{ route('admin.stalls') }}" class="sidebar-link {{ request()->routeIs('admin.stalls') ? 'active' : '' }}">
                    <ion-icon name="storefront-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    Stalls
                </a>
                <a href="{{ route('admin.evaluations') }}" class="sidebar-link {{ request()->routeIs('admin.evaluations') ? 'active' : '' }}">
                    <ion-icon name="create-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    Evaluations
                </a>
                <a href="{{ route('admin.students') }}" class="sidebar-link {{ request()->routeIs('admin.students') ? 'active' : '' }}">
                    <ion-icon name="people-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    Students
                </a>
            @elseif($user && $user->role === 'staff')
                @php
                    $isStaffAssigned = !empty($user->stall_id) || \Illuminate\Support\Facades\DB::table('stalls')->where('staff_id', $user->id)->exists();
                @endphp
                <a href="{{ route('staff.dashboard') }}" class="sidebar-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                    <ion-icon name="grid-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    Dashboard
                </a>
                @if($isStaffAssigned)
                    <a href="{{ route('staff.standings') }}" class="sidebar-link {{ request()->routeIs('staff.standings') ? 'active' : '' }}">
                        <ion-icon name="podium-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                        Standings
                    </a>
                @endif
                <a href="{{ route('staff.profile') }}" class="sidebar-link {{ request()->routeIs('staff.profile*') ? 'active' : '' }}">
                    <ion-icon name="person-outline" class="sidebar-link-icon" aria-hidden="true"></ion-icon>
                    My Profile
                </a>
            @endif
        </nav>

        <!-- Sidebar Footer (Logout) -->
        <div class="sidebar-footer p-4 border-t">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
            <button type="button" class="sidebar-link sidebar-logout w-full text-left js-logout-trigger !m-0 !w-full cursor-pointer bg-transparent border-0">
                <ion-icon name="log-out-outline" class="sidebar-link-icon"></ion-icon>
                Logout
            </button>
        </div>
    </aside>

    {{-- ── Main Content Area ───────────────────────────────────────────── --}}
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="flex items-center gap-4">
                <button class="mobile-toggle" id="mobile-toggle" aria-label="Toggle Menu">
                    <ion-icon name="menu-outline" class="text-2xl text-ink-900"></ion-icon>
                </button>
                <h2 class="font-display font-semibold text-lg text-ink-900">
                    @yield('header_title', 'Dashboard')
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-sm font-bold text-ink-900 leading-none">{{ Auth::user()->name ?? (Auth::user()->student_number ?? 'User') }}</div>
                    <div class="text-[11px] text-ink-500 uppercase tracking-wider font-semibold mt-1">{{ Auth::user()->role ?? 'Role' }}</div>
                </div>
                <div class="w-9 h-9 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold border border-brand-200">
                    {{ substr(Auth::user()->name ?? (Auth::user()->student_number ?? 'U'), 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>

{{-- ── Logout Modal ────────────────────────────────────────────────────── --}}
<!-- Logout Confirmation Modal -->
<dialog id="logout-confirm-modal" class="confirm-modal">
    <div class="flex items-start gap-4 mb-4">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-700">
            <ion-icon name="log-out-outline" class="text-2xl text-brand-700"></ion-icon>
        </div>
        <div>
            <h3 class="text-base font-bold text-neutral-900 leading-tight mb-1" style="font-family: var(--font-display);">Confirm Logout</h3>
            <p class="text-neutral-500 text-xs leading-relaxed">
                Are you sure you want to end your current session?
            </p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
        <button type="button" class="btn btn-ghost btn-sm js-close-modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 js-confirm-logout" style="background-color: oklch(0.58 0.23 28); border-color: oklch(0.58 0.23 28);">Logout</button>
    </div>
</dialog>

<script>
    // Sidebar Mobile Toggle
    (function () {
        var toggle = document.getElementById('mobile-toggle');
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebar-overlay');

        if (toggle && sidebar && overlay) {
            toggle.addEventListener('click', function () {
                sidebar.classList.add('open');
                overlay.classList.add('open');
            });
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
            });
        }
    })();

    // Logout Modal Logic
    (function () {
        var modal = document.getElementById('logout-confirm-modal');
        var triggers = document.querySelectorAll('.js-logout-trigger');
        var closeBtns = modal ? modal.querySelectorAll('.js-close-modal') : [];
        var confirmBtn = modal ? modal.querySelector('.js-confirm-logout') : null;
        var logoutForm = document.getElementById('logout-form');

        if (!modal) return;

        triggers.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                modal.showModal();
            });
        });

        closeBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                modal.close();
            });
        });

        // Close on clicking backdrop area
        modal.addEventListener('click', function (e) {
            var rect = modal.getBoundingClientRect();
            var isInDialog = (rect.top <= e.clientY && e.clientY <= rect.top + rect.height &&
                rect.left <= e.clientX && e.clientX <= rect.left + rect.width);
            if (!isInDialog) {
                modal.close();
            }
        });

        if (confirmBtn && logoutForm) {
            confirmBtn.addEventListener('click', function () {
                confirmBtn.classList.add('btn-loading');
                confirmBtn.innerHTML = '<ion-icon name="hourglass-outline" class="btn-hourglass"></ion-icon> Logging out...';
                logoutForm.submit();
            });
        }
    })();

    // Auto-trigger Toast Notifications from Laravel session flash
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
