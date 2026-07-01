<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About ISU Canteen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand text-success fw-bold" href="index.php">ISU Canteen DSS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navmenu">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
           <li class="nav-item"><a class="nav-link active" href="{{ url('/about') }}">About</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li>
        </ul>
        </div>
      </div>
    </nav>
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
    <footer class="footer text-center py-4 bg-light">
      <div class="container">
        <p class="mb-0">&copy; 2026 ISU-Cauayan Canteen DSS</p>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
