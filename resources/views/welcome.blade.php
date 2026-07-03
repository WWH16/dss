<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ISU-Cauayan Canteen DSS — Transparent Campus Dining</title>
    <meta name="description" content="A Decision Support System collecting student feedback to fairly rank ISU-Cauayan canteen stalls using AHP and SAW algorithms.">

    {{-- Tailwind v4 + app CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Fonts: Plus Jakarta Sans + Sora + Material Symbols --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Sora:wght@700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
</head>
<body>

{{-- ── Navigation ──────────────────────────────────────────────────────── --}}
<nav class="site-nav" role="navigation" aria-label="Main navigation">
    <div class="container">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="nav-brand" aria-label="ISU-Cauayan Canteen DSS Home">
                <img src="{{ asset('assets/images/isu_logo.jpg') }}" alt="" width="32" height="32" aria-hidden="true">
                ISU Canteen DSS
            </a>

            <button class="nav-toggle" id="nav-toggle" aria-controls="nav-links-wrap" aria-expanded="false" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="nav-links-wrap" id="nav-links-wrap">
                <ul class="nav-links" role="list">
                    <li><a href="{{ url('/') }}" class="active">Home</a></li>
                    <li><a href="{{ url('/about') }}">About</a></li>
                </ul>
                <div class="nav-cta">
                    <a href="{{ url('/login') }}" class="btn btn-ghost btn-sm">Login</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary btn-sm">Register</a>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
<section class="hero" aria-labelledby="hero-heading">
    <div class="container">
        <div class="hero-inner">
            <div class="animate-fade-up">
                <span class="hero-badge">ISU-Cauayan · Decision Support System</span>
            </div>

            <h1 class="hero-heading animate-fade-up delay-100" id="hero-heading">
                Fair Rankings for<br>
                <em>Every Canteen Stall</em>
            </h1>

            <p class="hero-sub animate-fade-up delay-200">
                Collect student feedback, evaluate stalls using AHP and SAW algorithms,
                and keep campus dining transparent, fair, and high-quality.
            </p>

            <div class="hero-actions animate-fade-up delay-300">
                <a href="{{ url('/register') }}" class="btn btn-primary btn-lg" id="hero-register-btn">
                    Start Evaluating
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a href="{{ url('/login') }}" class="hero-btn-outline" id="hero-login-btn">Login to Dashboard</a>
            </div>
        </div>
    </div>

    {{-- Hero visual --}}
    <div class="hero-visual" aria-hidden="true">
        <div class="hero-image-wrap">
            <img
                src="{{ asset('assets/images/canteen.png') }}"
                alt=""
                class="hero-image"
                width="1200"
                height="800"
                loading="eager"
            >
            <div class="hero-image-vignette"></div>
        </div>
    </div>

    <a href="#how-it-works" class="hero-scroll" aria-label="Scroll to learn how it works">
        <span class="hero-scroll-line" aria-hidden="true"></span>
        Scroll
    </a>
</section>

{{-- ── Stats strip ─────────────────────────────────────────────────────── --}}
<div class="stats-strip" role="complementary" aria-label="System statistics">
    <div class="container">
        <div class="stats-inner">
            <div class="stat-item">
                <span class="stat-value">4</span>
                <span class="stat-label">Criteria</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">AHP</span>
                <span class="stat-label">Pairwise weight</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">SAW</span>
                <span class="stat-label">Ranking method</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">Live</span>
                <span class="stat-label">Feedback loop</span>
            </div>
        </div>
    </div>
</div>

{{-- ── How It Works ─────────────────────────────────────────────────────── --}}
<section class="how-section" id="how-it-works" aria-labelledby="how-heading">
    <div class="container">
        <header class="section-header">
            <span class="section-label">The Process</span>
            <h2 class="section-title" id="how-heading">
                From Feedback<br>to Fair Rankings
            </h2>
            <p class="section-sub">
                A structured, algorithmic approach to evaluating canteen stalls —
                so every score is objective and every stall earns its place.
            </p>
        </header>

        <ol class="steps-list" aria-label="How the DSS works">
            <li class="step-item">
                <span class="step-num" aria-hidden="true">01</span>
                <div>
                    <h3 class="step-content-title">Students Submit Evaluations</h3>
                    <p class="step-content-body">
                        Logged-in students rate each canteen stall across four criteria:
                        cleanliness, service quality, taste, and price fairness.
                        Ratings are recorded securely per session.
                    </p>
                </div>
            </li>
            <li class="step-item">
                <span class="step-num" aria-hidden="true">02</span>
                <div>
                    <h3 class="step-content-title">Criteria Are Weighted with AHP</h3>
                    <p class="step-content-body">
                        Analytic Hierarchy Process pairwise comparisons determine how
                        much each criterion matters relative to the others — producing
                        a mathematically sound weight for each dimension.
                    </p>
                </div>
            </li>
            <li class="step-item">
                <span class="step-num" aria-hidden="true">03</span>
                <div>
                    <h3 class="step-content-title">Stalls Are Ranked Using SAW</h3>
                    <p class="step-content-body">
                        Simple Additive Weighting aggregates each stall's scores across
                        all criteria using the AHP-derived weights, producing a final
                        composite score and rank ordering.
                    </p>
                </div>
            </li>
            <li class="step-item">
                <span class="step-num" aria-hidden="true">04</span>
                <div>
                    <h3 class="step-content-title">Admins Act on Transparent Results</h3>
                    <p class="step-content-body">
                        Administrators see live rankings in the dashboard, can review
                        evaluation history, and use insights to guide decisions about
                        stall standards and campus dining quality.
                    </p>
                </div>
            </li>
        </ol>
    </div>
</section>

{{-- ── Evaluation Criteria ──────────────────────────────────────────────── --}}
<section class="criteria-section" aria-labelledby="criteria-heading">
    <div class="container">
        <header class="section-header">
            <span class="section-label">Evaluation Criteria</span>
            <h2 class="section-title" id="criteria-heading">
                What Students Evaluate
            </h2>
            <p class="section-sub">
                Four dimensions rated per stall, each carrying a weight determined
                by the AHP pairwise comparison matrix.
            </p>
        </header>

        <div class="criteria-grid" role="list">
            <div class="criterion" role="listitem">
                <div class="criterion-icon" aria-hidden="true">
                    <span class="material-symbols-outlined text-brand-600">cleaning_services</span>
                </div>
                <div>
                    <div class="criterion-title">Cleanliness</div>
                    <div class="criterion-desc">
                        Hygiene of the preparation area, utensils, and food handling practices.
                    </div>
                </div>
            </div>

            <div class="criterion" role="listitem">
                <div class="criterion-icon" aria-hidden="true">
                    <span class="material-symbols-outlined text-brand-600">support_agent</span>
                </div>
                <div>
                    <div class="criterion-title">Service Quality</div>
                    <div class="criterion-desc">
                        Responsiveness, politeness, and speed of the canteen staff.
                    </div>
                </div>
            </div>

            <div class="criterion" role="listitem">
                <div class="criterion-icon" aria-hidden="true">
                    <span class="material-symbols-outlined text-brand-600">restaurant</span>
                </div>
                <div>
                    <div class="criterion-title">Taste</div>
                    <div class="criterion-desc">
                        Overall flavor, food quality, and satisfaction with meals offered.
                    </div>
                </div>
            </div>

            <div class="criterion" role="listitem">
                <div class="criterion-icon" aria-hidden="true">
                    <span class="material-symbols-outlined text-brand-600">payments</span>
                </div>
                <div>
                    <div class="criterion-title">Price Fairness</div>
                    <div class="criterion-desc">
                        Value for money relative to portion size and food quality provided.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── CTA ──────────────────────────────────────────────────────────────── --}}
<section class="cta-section" aria-labelledby="cta-heading">
    <div class="container">
        <div class="cta-inner">
            <h2 class="cta-heading" id="cta-heading">
                Ready to make your voice count?
            </h2>
            <p class="cta-sub">
                Register as a student and submit your first evaluation today.
                Every rating shapes a fairer campus dining experience.
            </p>
            <div class="cta-actions">
                <a href="{{ url('/register') }}" class="btn btn-primary btn-lg" id="cta-register-btn">
                    Create an Account
                </a>
                <a href="{{ url('/about') }}" class="hero-btn-outline" id="cta-about-btn">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ── Footer ───────────────────────────────────────────────────────────── --}}
<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand-col">
                <a href="{{ url('/') }}" class="footer-brand-logo">
                    <img src="{{ asset('assets/images/isu_logo.jpg') }}" alt="" width="28" height="28" aria-hidden="true">
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
                    <li><a href="{{ url('/login') }}">Login to Portal</a></li>
                    <li><a href="{{ url('/register') }}">Create Account</a></li>
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

</body>
</html>
