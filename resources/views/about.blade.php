@extends('layouts.app')

@section('title', 'About ISU Canteen')

@section('content')
<section class="about-hero text-white text-center py-16 md:py-24 relative overflow-hidden bg-brand-900">
  <div class="container relative z-10">
    <h1 class="text-4xl md:text-5xl font-display font-extrabold tracking-tight text-white mb-4">About ISU Canteen</h1>
    <p class="text-base md:text-lg text-brand-100 max-w-2xl mx-auto leading-relaxed text-wrap: balance">
      Dedicated to quality food, responsive customer service, and improved campus dining experiences.
    </p>
  </div>
  <div class="absolute inset-0 bg-gradient-to-b from-brand-800/80 to-brand-950/90 z-0 pointer-events-none"></div>
</section>

<section class="container py-16 md:py-24">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
    <div class="space-y-6">
      <h2 class="text-3xl font-display font-bold text-neutral-900 tracking-tight leading-tight">ISU-Cauayan Canteen Evaluation</h2>
      <p class="text-neutral-600 leading-relaxed text-sm md:text-base">
        Our system helps students and staff evaluate canteen stalls using food quality, price, cleanliness, and service. The goal is to support transparent decisions and highlight the best performers in daily campus dining.
      </p>
      <p class="text-neutral-600 leading-relaxed text-sm md:text-base">
        The ISU canteen aims to create a friendly dining space where customers feel respected and receive prompt service. This tool gathers real feedback and transforms it into actionable ranking insights.
      </p>
    </div>
    
    <div class="bg-white rounded-2xl border border-neutral-200/60 p-6 md:p-8 shadow-sm">
      <h3 class="text-xl font-bold text-neutral-900 mb-4 tracking-tight leading-snug">Customer Service</h3>
      <p class="text-neutral-600 text-sm md:text-base leading-relaxed mb-6">
        We value every customer's feedback. If you need help, our support team is ready to assist with registration, evaluation, and understanding the ranking results.
      </p>
      <ul class="space-y-3.5 text-sm md:text-base text-neutral-600">
        <li class="flex items-start gap-3">
          <span class="text-brand-500 font-bold mt-0.5">•</span>
          <span>Friendly support staff</span>
        </li>
        <li class="flex items-start gap-3">
          <span class="text-brand-500 font-bold mt-0.5">•</span>
          <span>Quick response to concerns</span>
        </li>
        <li class="flex items-start gap-3">
          <span class="text-brand-500 font-bold mt-0.5">•</span>
          <span>Clear evaluation guidance</span>
        </li>
        <li class="flex items-start gap-3">
          <span class="text-brand-500 font-bold mt-0.5">•</span>
          <span>Reports and performance insight</span>
        </li>
      </ul>
    </div>
  </div>
</section>
@endsection
