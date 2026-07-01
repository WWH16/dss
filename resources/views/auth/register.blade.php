<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | ISU Canteen DSS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand text-success fw-bold" href="/">ISU Canteen DSS</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ url('/about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="auth-page">
    <div class="auth-card shadow-sm">

        <div class="text-center mb-4">
            <h1 class="fw-bold text-success">Register</h1>
            <p class="text-muted">Create your account for student or staff access</p>
        </div>

        {{-- ERROR HANDLING --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ url('/register') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select id="role" name="role" class="form-select" onchange="toggleRegisterFields()">
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            {{-- STUDENT FIELDS --}}
            <div id="student_fields">

                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select name="course" class="form-select">
                        <option value="">Select your course</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSHM">BSHM</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Year</label>
                    <select name="year_level" class="form-select">
                        <option value="">Select year</option>
                        <option value="1st year">1st year</option>
                        <option value="2nd year">2nd year</option>
                        <option value="3rd year">3rd year</option>
                        <option value="4th year">4th year</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Student Number</label>
                    <input type="text" name="student_number" class="form-control">
                </div>

            </div>

            {{-- STAFF FIELD --}}
            <div id="staff_field" style="display:none;">
                <div class="mb-3">
                    <label class="form-label">Stall Number / Name</label>
                    <input type="text" name="stall_name" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button class="btn btn-success w-100">Register</button>
        </form>

    </div>
</div>

<script>
function toggleRegisterFields() {
    const role = document.getElementById('role').value;
    document.getElementById('student_fields').style.display =
        (role === 'student') ? 'block' : 'none';

    document.getElementById('staff_field').style.display =
        (role === 'staff') ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', toggleRegisterFields);
</script>

</body>
</html>