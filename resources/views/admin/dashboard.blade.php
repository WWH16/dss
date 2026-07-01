<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">

            <div>
                <h2 class="text-success">Admin Dashboard</h2>
                <p class="mb-0">
                    Welcome back,
                    <strong>{{ Auth::user()->name }}</strong>
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

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- SUMMARY --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Total Students</h5>
                    <h2>{{ $studentCount }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Total Stalls</h5>
                    <h2>{{ $stallCount }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Total Evaluations</h5>
                    <h2>{{ $evaluationCount }}</h2>
                </div>
            </div>
        </div>

    </div>

    {{-- ADD STALL --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-success text-white">
            Add Food Stall
        </div>

        <div class="card-body">

            <form action="{{ route('admin.stall.add') }}" method="POST">

                @csrf

                <div class="row">

                    <div class="col-md-5">
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="Stall Name"
                            required>
                    </div>


                    <div class="col-md-2">
                        <button class="btn btn-success w-100">
                            Add
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- STALL LIST --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-success text-white">
            Food Stalls
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                <tr>
                    <th>Name</th>
                    <th width="120">Action</th>
                </tr>

                </thead>

                <tbody>

                @forelse($stalls as $stall)

                    <tr>

                        <td>{{ $stall->name }}</td>


                        <td>

                            <form action="{{ route('admin.stall.delete',$stall->id) }}"
                                  method="POST">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3" class="text-center">
                            No stalls available.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- EVALUATION RATINGS --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-success text-white">
            Student Evaluation Ratings
        </div>

        <div class="card-body table-responsive">

            <table class="table table-striped">

                <thead>

                <tr>

                    <th>Student</th>
                    <th>Stall</th>
                    <th>Cleanliness</th>
                    <th>Service</th>
                    <th>Taste</th>
                    <th>Price</th>
                    <th>Comment</th>

                </tr>

                </thead>

                <tbody>

                @forelse($evaluations as $eval)

                    <tr>

                        <td>{{ $eval->student_name }}</td>

                        <td>{{ $eval->stall_name }}</td>

                        <td>{{ $eval->cleanliness }}</td>

                        <td>{{ $eval->service }}</td>

                        <td>{{ $eval->taste }}</td>

                        <td>{{ $eval->price }}</td>

                        <td>{{ $eval->comment }}</td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center">
                            No evaluations yet.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- AVERAGE RESULT --}}
    <div class="card shadow-sm">

        <div class="card-header bg-success text-white">
            Average Evaluation Result Per Stall
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered">

                <thead>

                <tr>

                    <th>Food Stall</th>
                    <th>Cleanliness</th>
                    <th>Service</th>
                    <th>Taste</th>
                    <th>Price</th>

                </tr>

                </thead>

                <tbody>

                @forelse($results as $result)

                    <tr>

                        <td>{{ $result->name }}</td>

                        <td>{{ number_format($result->cleanliness,2) }}</td>

                        <td>{{ number_format($result->service,2) }}</td>

                        <td>{{ number_format($result->taste,2) }}</td>

                        <td>{{ number_format($result->price,2) }}</td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center">
                            No evaluation results yet.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>