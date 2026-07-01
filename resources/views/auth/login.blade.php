<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | ISU Canteen DSS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  </head>

  <body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand text-success fw-bold" href="{{ url('/') }}">
          ISU Canteen DSS
        </a>

        <div class="collapse navbar-collapse">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
            <li class="nav-item"><a class="nav-link active" href="{{ url('/login') }}">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="auth-page">
      <div class="auth-card shadow-sm">

        <div class="text-center mb-4">
          <h1 class="fw-bold text-success">Login</h1>
          <p class="text-muted">Access your evaluation dashboard</p>
        </div>

        @if($success)
          <div class="alert alert-success">{{ $success }}</div>
        @endif

        @if($error)
          <div class="alert alert-danger">{{ $error }}</div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
          @csrf

          <div class="mb-3">
            <label class="form-label">Role</label>
            <select id="role" name="role" class="form-select" onchange="toggleLoginFields()">
              <option value="student" {{ $selectedRole=='student'?'selected':'' }}>Student</option>
              <option value="staff" {{ $selectedRole=='staff'?'selected':'' }}>Staff</option>
              <option value="admin" {{ $selectedRole=='admin'?'selected':'' }}>Admin</option>
            </select>
          </div>

          <div class="mb-3" id="student_number_field">
            <label class="form-label">Student Number</label>
            <input type="text" name="student_number" class="form-control">
          </div>

          <div class="mb-3" id="email_field">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <button type="submit" class="btn btn-success w-100">
            Login
          </button>
        </form>

      </div>
    </div>

    <script>
      function toggleLoginFields() {
        const role = document.getElementById('role').value;
        document.getElementById('student_number_field').style.display =
          role === 'student' ? 'block' : 'none';

        document.getElementById('email_field').style.display =
          role === 'student' ? 'none' : 'block';
      }

      document.addEventListener('DOMContentLoaded', toggleLoginFields);
    </script>

  </body>
</html>