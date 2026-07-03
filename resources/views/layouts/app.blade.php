<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ISU-Cauayan Canteen DSS — Transparent Campus Dining')</title>
    <meta name="description" content="@yield('meta_description', 'A Decision Support System collecting student feedback to fairly rank ISU-Cauayan canteen stalls using AHP and SAW algorithms.')">

    {{-- Tailwind v4 + app CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Fonts: Plus Jakarta Sans + Sora + Material Symbols --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Sora:wght@700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

    @yield('head')
</head>
<body>

{{-- ── Navigation ──────────────────────────────────────────────────────── --}}
<nav class="site-nav" role="navigation" aria-label="Main navigation">
    <div class="container">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="nav-brand" aria-label="ISU-Cauayan Canteen DSS Home">
                <img src="{{ asset('assets/images/isu_logo.png') }}" alt="" width="32" height="32" aria-hidden="true">
                ISU Canteen DSS
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
                        <form action="{{ route('logout') }}" method="POST" class="inline" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Logout</button>
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
                    <span>ISU Canteen DSS</span>
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

@yield('scripts')

</body>
</html>
