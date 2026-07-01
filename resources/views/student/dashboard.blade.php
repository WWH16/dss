<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body class="bg-light">

<div class="container py-5">

    <!-- HEADER -->
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">

            <div>
                <h2 class="text-success mb-1">Student Dashboard</h2>
                <p class="mb-0">
                    Welcome,
                    <strong>{{ $profile->name ?? $profile->student_number }}</strong>
                </p>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-danger">
                    Logout
                </button>
            </form>

        </div>
    </div>

    <!-- PROFILE -->
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-success text-white">
            <h5 class="mb-0">My Profile</h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $profile->name }}</p>
                    <p><strong>Student Number:</strong> {{ $profile->student_number }}</p>
                    <p><strong>Email:</strong> {{ $profile->email }}</p>
                </div>

                <div class="col-md-6">
                    <p><strong>Course:</strong> {{ $profile->course }}</p>
                    <p><strong>Year Level:</strong> {{ $profile->year_level }}</p>
                </div>

            </div>

        </div>

    </div>

    <!-- EVALUATE BUTTON -->
    <div class="card shadow-sm mb-4">

        <div class="card-body text-center">

            <h4 class="text-success">
                Food Stall Evaluation
            </h4>

            <p>
                Click the button below to evaluate a food stall.
            </p>

            <a href="{{ route('student.evaluation') }}"
               class="btn btn-success btn-lg">

                Evaluate Food Stall

            </a>

        </div>

    </div>

    <!-- MY EVALUATIONS -->
    <div class="card shadow-sm">

        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                My Evaluated Stalls
            </h5>
        </div>

        <div class="card-body">

            @if($myStudentEvals->isEmpty())

                <div class="alert alert-info">
                    You haven't evaluated any food stall yet.
                </div>

            @else

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <th>Food Stall</th>

                            <th>Date Evaluated</th>

                        </tr>

                    </thead>

                    <tbody>

                    @foreach($myStudentEvals as $eval)

                        <tr>

                            <td>

                                @php
                                    $stall = $stalls->firstWhere('id', $eval->stall_id);
                                @endphp

                                {{ $stall->name ?? 'Unknown Stall' }}

                            </td>

                            <td>

                                {{ \Carbon\Carbon::parse($eval->created_at)->format('F d, Y h:i A') }}

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            @endif

        </div>

    </div>

</div>

</body>
</html>