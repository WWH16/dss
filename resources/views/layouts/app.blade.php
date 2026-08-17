<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Decision Support System: ISU Cauayan Canteen Client Evaluation System')</title>
    <meta name="description" content="@yield('meta_description', 'Decision Support System: ISU Cauayan Canteen Client Evaluation System — collecting student feedback to rank canteen stalls using AHP and SAW algorithms.')">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Tailwind v4 + app CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Fonts: Plus Jakarta Sans + Sora --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Sora:wght@700;800&display=swap" rel="stylesheet">

    {{-- Ionicons (iOS Icon System) --}}
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    @yield('head')
</head>
<body>

{{-- ── Navigation ──────────────────────────────────────────────────────── --}}
<nav class="site-nav" role="navigation" aria-label="Main navigation">
    <div class="container">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="nav-brand" aria-label="Decision Support System: ISU Cauayan Canteen Client Evaluation System">
                <img src="{{ asset('assets/images/isu_logo.png') }}" alt="" width="32" height="32" aria-hidden="true">
                <span>ISU Cauayan Canteen DSS</span>
            </a>

            <button class="nav-toggle" id="nav-toggle" aria-controls="nav-links-wrap" aria-expanded="false" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="nav-links-wrap" id="nav-links-wrap">
                <ul class="nav-links" role="list">
                    <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
                    <li><a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'active' : '' }}">About</a></li>
                </ul>
                <div class="nav-cta">
                    @auth
                        @php
                            $user = Auth::user();
                            $dashboardRoute = '#';
                            if ($user->role === 'student') {
                                $dashboardRoute = route('student.dashboard');
                            } elseif ($user->role === 'staff') {
                                $dashboardRoute = route('staff.dashboard');
                            } elseif ($user->role === 'admin') {
                                $dashboardRoute = route('admin.dashboard');
                            }
                        @endphp
                        <a href="{{ $dashboardRoute }}" class="btn btn-ghost btn-sm">Dashboard</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="inline" style="display: inline;">
                            @csrf
                            <button type="button" class="btn btn-primary btn-sm js-logout-trigger">Logout</button>
                        </form>
                    @else
                        <a href="{{ url('/login') }}" class="btn btn-ghost btn-sm">Login</a>
                        <a href="{{ url('/register') }}" class="btn btn-primary btn-sm">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- ── Page Content ────────────────────────────────────────────────────── --}}
<main>
    @yield('content')
</main>

{{-- ── Footer ───────────────────────────────────────────────────────────── --}}
<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand-col">
                <a href="{{ url('/') }}" class="footer-brand-logo">
                    <img src="{{ asset('assets/images/isu_logo.png') }}" alt="" width="28" height="28" aria-hidden="true">
                    <span>ISU Cauayan Canteen Client Evaluation System</span>
                </a>
                <p class="footer-brand-desc">
                    A Decision Support System tailored for campus dining. Utilizing AHP pairwise criteria weighting and SAW calculations to foster fair, transparent canteen stall rankings.
                </p>
            </div>

            <div class="footer-links-col">
                <h4 class="footer-col-title">Platform</h4>
                <ul class="footer-links-list" role="list">
                    <li><a href="{{ url('/') }}">Home Portal</a></li>
                    <li><a href="{{ url('/about') }}">About DSS</a></li>
                </ul>
            </div>

            <div class="footer-links-col">
                <h4 class="footer-col-title">Account Portal</h4>
                <ul class="footer-links-list" role="list">
                    @auth
                        @php
                            $user = Auth::user();
                            $dashboardRoute = '#';
                            if ($user->role === 'student') {
                                $dashboardRoute = route('student.dashboard');
                            } elseif ($user->role === 'staff') {
                                $dashboardRoute = route('staff.dashboard');
                            } elseif ($user->role === 'admin') {
                                $dashboardRoute = route('admin.dashboard');
                            }
                        @endphp
                        <li><a href="{{ $dashboardRoute }}">My Dashboard</a></li>
                    @else
                        <li><a href="{{ url('/login') }}">Login to Portal</a></li>
                        <li><a href="{{ url('/register') }}">Create Account</a></li>
                    @endauth
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copy">&copy; 2026 Isabela State University — Cauayan Campus. All rights reserved.</p>
            <p class="footer-meta">AHP &amp; SAW Evaluation Platform</p>
        </div>
    </div>
</footer>

{{-- Mobile nav toggle script --}}
<script>
    (function () {
        var toggle = document.getElementById('nav-toggle');
        var wrap   = document.getElementById('nav-links-wrap');
        if (!toggle || !wrap) return;
        toggle.addEventListener('click', function () {
            var isOpen = wrap.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    })();
</script>

<!-- Back to Top Button -->
<button id="back-to-top" class="back-to-top" aria-label="Back to top">
    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
        <path d="M440-160v-487L216-423l-56-57 320-320 320 320-56 57-224-224v487h-80Z"/>
    </svg>
</button>

<script>
    (function () {
        var btn = document.getElementById('back-to-top');
        if (!btn) return;
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        });
        btn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    })();
</script>

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
