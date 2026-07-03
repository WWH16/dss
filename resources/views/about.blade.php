@extends('layouts.app')

@section('title', 'About ISU Canteen')

@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@endsection

@section('content')
<section class="about-hero text-white text-center py-5">
  <div class="container">
    <h1 class="display-5 fw-bold">About ISU Canteen</h1>
    <p class="lead">Dedicated to quality food, responsive customer service, and improved campus dining experiences.</p>
  </div>
</section>

<section class="container py-5">
  <div class="row gx-5">
    <div class="col-lg-6">
      <h2>ISU-Cauayan Canteen Evaluation</h2>
      <p>Our system helps students and staff evaluate canteen stalls using food quality, price, cleanliness, and service. The goal is to support transparent decisions and highlight the best performers in daily campus dining.</p>
      <p>The ISU canteen aims to create a friendly dining space where customers feel respected and receive prompt service. This tool gathers real feedback and transforms it into actionable ranking insights.</p>
    </div>
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm p-4">
        <h3>Customer Service</h3>
        <p>We value every customer's feedback. If you need help, our support team is ready to assist with registration, evaluation, and understanding the ranking results.</p>
        <ul class="list-unstyled">
          <li>• Friendly support staff</li>
          <li>• Quick response to concerns</li>
          <li>• Clear evaluation guidance</li>
          <li>• Reports and performance insight</li>
        </ul>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
