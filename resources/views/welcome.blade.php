@extends('layouts.app')

@section('title', 'ISU-Cauayan Canteen DSS — Transparent Campus Dining')

@section('content')
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
                    <a href="{{ $dashboardRoute }}" class="btn btn-primary btn-lg">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ url('/register') }}" class="btn btn-primary btn-lg" id="hero-register-btn">
                        Start Evaluating
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="{{ url('/login') }}" class="hero-btn-outline" id="hero-login-btn">Login to Dashboard</a>
                @endauth
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
                @auth
                    <a href="{{ $dashboardRoute }}" class="btn btn-primary btn-lg">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ url('/register') }}" class="btn btn-primary btn-lg" id="cta-register-btn">
                        Create an Account
                    </a>
                    <a href="{{ url('/about') }}" class="hero-btn-outline" id="cta-about-btn">
                        Learn More
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection
