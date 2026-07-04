@extends('layouts.app')

@section('title', 'About ISU Canteen')

@section('content')
{{-- ── Compact Page Header ────────────────────────────────────────────── --}}
<section class="relative bg-brand-900 text-white py-16 md:py-24 overflow-hidden">
  <div class="container relative z-10 animate-fade-up">
    <h1 class="text-3xl md:text-5xl font-display font-extrabold tracking-tight text-white mb-4">
      About the Platform
    </h1>
    <p class="text-base md:text-lg text-brand-100 max-w-2xl leading-relaxed text-balance">
      Empowering the ISU-Cauayan community with an objective, data-driven system for canteen evaluation and stall rankings.
    </p>
  </div>
  <!-- Subtle overlay grid/glow matching the brand identity -->
  <div class="absolute inset-0 bg-[radial-gradient(ellipse_70%_55%_at_50%_-10%,oklch(0.56_0.17_155_/0.2)_0%,transparent_70%)] pointer-events-none z-0"></div>
</section>

{{-- ── Main Two-Column Layout ─────────────────────────────────────────── --}}
<section class="container py-16 md:py-24 animate-fade-up delay-100">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
    
    {{-- Main Content (2/3 Column) --}}
    <div class="lg:col-span-2 space-y-12">
      
      {{-- Mission & Purpose --}}
      <div class="space-y-4">
        <h2 class="text-2xl font-display font-bold text-ink-900 tracking-tight">Our Mission & Purpose</h2>
        <p class="text-neutral-600 leading-relaxed text-sm md:text-base">
          The Canteen Decision Support System (DSS) was developed to foster a transparent and high-quality dining environment at Isabela State University, Cauayan Campus. By collecting honest feedback from students and staff, the system helps canteen management maintain high standards of service and hygiene while promoting fair competition among food stalls.
        </p>
      </div>

      {{-- Methodology Explanation --}}
      <div class="space-y-6">
        <h2 class="text-2xl font-display font-bold text-ink-900 tracking-tight">The Decision Science Methodology</h2>
        <p class="text-neutral-600 leading-relaxed text-sm md:text-base">
          To ensure rankings are completely fair and free from arbitrary bias, the platform employs a dual-algorithm approach from decision science:
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
          <!-- AHP Card -->
          <div class="bg-surface-alt p-6 rounded-xl border border-neutral-200/50">
            <div class="flex items-center gap-2.5 mb-3">
              <span class="material-symbols-outlined text-brand-700">balance</span>
              <h3 class="text-lg font-bold text-ink-900">1. AHP Weighting</h3>
            </div>
            <p class="text-neutral-600 text-xs md:text-sm leading-relaxed">
              The <strong>Analytic Hierarchy Process (AHP)</strong> is used to determine how much weight is given to each evaluation criterion (e.g., Cleanliness vs. Price). These weights are calculated based on pairwise comparisons, ensuring the ranking reflects actual student priorities.
            </p>
          </div>

          <!-- SAW Card -->
          <div class="bg-surface-alt p-6 rounded-xl border border-neutral-200/50">
            <div class="flex items-center gap-2.5 mb-3">
              <span class="material-symbols-outlined text-brand-700">leaderboard</span>
              <h3 class="text-lg font-bold text-ink-900">2. SAW Scoring</h3>
            </div>
            <p class="text-neutral-600 text-xs md:text-sm leading-relaxed">
              The <strong>Simple Additive Weighting (SAW)</strong> algorithm normalizes the raw ratings submitted by students and synthesizes them using the AHP weights. This outputs a single, composite score for each stall, ranging from 0 to 1, to generate the final rank.
            </p>
          </div>
        </div>
      </div>

      {{-- Parameter Descriptions --}}
      <div class="space-y-6">
        <h2 class="text-2xl font-display font-bold text-ink-900 tracking-tight">Key Evaluation Parameters</h2>
        <p class="text-neutral-600 leading-relaxed text-sm md:text-base">
          All stalls are graded across four criteria, which are regularly calibrated using the decision support model:
        </p>
        
        <div class="space-y-5">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-lg bg-brand-50 flex items-center justify-center text-brand-700 mt-0.5 shrink-0">
              <span class="material-symbols-outlined">restaurant</span>
            </div>
            <div>
              <h4 class="text-base font-bold text-ink-900">Taste & Quality</h4>
              <p class="text-neutral-600 text-sm leading-relaxed">Evaluation of raw ingredient freshness, presentation, preparation quality, and overall flavor profile.</p>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-lg bg-brand-50 flex items-center justify-center text-brand-700 mt-0.5 shrink-0">
              <span class="material-symbols-outlined">cleaning_services</span>
            </div>
            <div>
              <h4 class="text-base font-bold text-ink-900">Cleanliness & Hygiene</h4>
              <p class="text-neutral-600 text-sm leading-relaxed">Assessing trash disposal, counter sanitation, utensil cleanliness, and food handling practices.</p>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-lg bg-brand-50 flex items-center justify-center text-brand-700 mt-0.5 shrink-0">
              <span class="material-symbols-outlined">support_agent</span>
            </div>
            <div>
              <h4 class="text-base font-bold text-ink-900">Service Speed & Politeness</h4>
              <p class="text-neutral-600 text-sm leading-relaxed">Measuring average waiting times, staff responsiveness, friendliness, and ordering orderliness.</p>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-lg bg-brand-50 flex items-center justify-center text-brand-700 mt-0.5 shrink-0">
              <span class="material-symbols-outlined">payments</span>
            </div>
            <div>
              <h4 class="text-base font-bold text-ink-900">Price & Value Fairness</h4>
              <p class="text-neutral-600 text-sm leading-relaxed">Determining if portion size, nutritional value, and flavor match the student-friendly pricing.</p>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- Sidebar (1/3 Column) --}}
    <div class="space-y-6">
      
      {{-- Quick Facts --}}
      <div class="bg-white p-6 rounded-2xl border border-neutral-200/60 shadow-sm">
        <h3 class="text-lg font-bold text-ink-900 mb-4 pb-2 border-b border-neutral-100">Quick Facts</h3>
        <dl class="space-y-3 text-sm">
          <div class="flex justify-between">
            <dt class="text-neutral-500">Institution</dt>
            <dd class="font-semibold text-ink-900">ISU-Cauayan</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-neutral-500">Criteria Count</dt>
            <dd class="font-semibold text-ink-900">4 Dimensions</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-neutral-500">Weight Method</dt>
            <dd class="font-semibold text-ink-900">AHP</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-neutral-500">Ranking Method</dt>
            <dd class="font-semibold text-ink-900">SAW</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-neutral-500">Target Audience</dt>
            <dd class="font-semibold text-ink-900">Students & Staff</dd>
          </div>
        </dl>
      </div>

      {{-- Customer Service details --}}
      <div class="bg-white p-6 rounded-2xl border border-neutral-200/60 shadow-sm">
        <h3 class="text-lg font-bold text-ink-900 mb-2">Customer Service</h3>
        <p class="text-neutral-600 text-xs md:text-sm leading-relaxed mb-4">
          If you need assistance with account registration, evaluating a stall, or interpreting the system rankings, our team is here to assist.
        </p>
        <ul class="space-y-2 text-xs md:text-sm text-neutral-600">
          <li class="flex items-center gap-2">
            <span class="text-brand-500 font-bold">•</span> Friendly support staff
          </li>
          <li class="flex items-center gap-2">
            <span class="text-brand-500 font-bold">•</span> Quick response to concerns
          </li>
          <li class="flex items-center gap-2">
            <span class="text-brand-500 font-bold">•</span> Clear evaluation guidance
          </li>
        </ul>
      </div>

      {{-- Quick CTA action --}}
      <div class="bg-brand-900 text-white p-6 rounded-2xl border border-brand-950 shadow-sm relative overflow-hidden">
        <div class="relative z-10 space-y-4">
          <h3 class="text-lg font-bold text-white leading-tight">Ready to evaluate?</h3>
          <p class="text-brand-200 text-xs leading-relaxed">
            Every rating contributes to the consensus and ensures high-quality dining on campus.
          </p>
          @auth
            <a href="{{ route('student.evaluation') }}" class="btn btn-primary btn-sm w-full">Start Evaluation</a>
          @else
            <a href="{{ url('/register') }}" class="btn btn-primary btn-sm w-full">Create Account</a>
          @endauth
        </div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,oklch(0.56_0.17_155_/_0.2)_0%,transparent_70%)] pointer-events-none z-0"></div>
      </div>

    </div>
  </div>
</section>
@endsection


