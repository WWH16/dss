<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ISU-Cauayan Canteen Evaluation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand text-success fw-bold d-flex align-items-center gap-2" href="index.php">
          <img src="assets/images/isu_logo.jpg" alt="ISU Logo" class="navbar-logo">
          ISU-Cauayan Canteen Evaluation
        </a>
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

    <section class="hero text-white">
      <div class="container hero-content text-center">
        <h1 class="display-4 fw-bold">Canteen Stall Evaluation for ISU-Cauayan</h1>
        <p class="lead mt-3">Collect student feedback, compare stalls with AHP and SAW, and help campus dining stay transparent and fair.</p>
        <div class="mt-4">
          <a href="{{ url('/login') }}" class="btn btn-success btn-lg me-2">Login</a>
          <a href="{{ url('/register') }}" class="btn btn-outline-light btn-lg">Register</a>
        </div>
      </div>
    </section>

    <section class="container py-5">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature-card p-4 shadow-sm">
            <h4>Student Evaluation</h4>
            <p>Submit ratings for cleanliness, service, taste, and price to support fair stall rankings.</p>
            <a href="{{ route('evaluation') }}" class="btn btn-success btn-sm mt-3">Go to Evaluation
</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card p-4 shadow-sm">
            <h4>Student Dashboard</h4>
            <p>Access the student dashboard to review your submission and session status.</p>
            <a href="{{ url('/login?role=student') }}" class="btn btn-success btn-sm mt-3">Open Student Dashboard</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card p-4 shadow-sm">
            <h4>Admin Dashboard</h4>
            <p>Admins can review rankings and manage the system from the admin dashboard.</p>
            <a href="{{ url('/login?role=admin') }}" class="btn btn-success btn-sm mt-3">Open Admin Dashboard</a>
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
