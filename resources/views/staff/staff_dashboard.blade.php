<!doctype html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Staff Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

    {{-- HEADER --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body d-flex justify-content-between">

            <div>

                <h2 class="text-success">
                    Staff Dashboard
                </h2>

                <p class="mb-0">

                    Welcome,

                    <strong>{{ $profile->name }}</strong>

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

    {{-- PROFILE --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-success text-white">

            Staff Profile

        </div>

        <div class="card-body">

            <p><strong>Name:</strong> {{ $profile->name }}</p>

            <p><strong>Email:</strong> {{ $profile->email }}</p>

            <p><strong>Assigned Stall:</strong>

                {{ $stall->name ?? 'Not Assigned' }}

            </p>

        </div>

    </div>

    {{-- RATINGS --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-success text-white">

            Evaluation Summary

        </div>

        <div class="card-body">

            @if($ratings)

            <table class="table table-bordered">

                <tr>

                    <th>Cleanliness</th>

                    <td>{{ number_format(optional($ratings->first())->cleanliness ?? 0, 2) }}</td>

                </tr>

                <tr>

                    <th>Service</th>

                    <td>{{ number_format(optional($ratings->first())->service ?? 0, 2) }}</td>

                </tr>

                <tr>

                    <th>Taste</th>

                    <td>{{ number_format(optional($ratings->first())->taste ?? 0, 2) }}</td>

                </tr>

                <tr>

                    <th>Price</th>

                    <td>{{ number_format(optional($ratings->first())->price ?? 0, 2) }}</td>

                </tr>

            </table>

            @else

                <div class="alert alert-info">

                    No evaluation yet.

                </div>

            @endif

        </div>

    </div>

    {{-- IMPROVEMENTS --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-warning">

            Things to Improve

        </div>

        <div class="card-body">

            @if(count($improvements))

                <ul>

                    @foreach($improvements as $item)

                        <li>{{ $item['score'] }}</li>

                    @endforeach

                </ul>

            @else

                <div class="alert alert-success">

                    Great! No major improvements needed.

                </div>

            @endif

        </div>

    </div>

    {{-- EVALUATIONS --}}

    <div class="card shadow-sm">

        <div class="card-header bg-success text-white">

            Recent Evaluations

        </div>

        <div class="card-body">

            @if($evaluations->isNotEmpty())

            <table class="table table-striped">

                <thead>

                <tr>

                    <th>Date</th>

                    <th>Cleanliness</th>

                    <th>Service</th>

                    <th>Taste</th>

                    <th>Price</th>

                    <th>Comment</th>

                </tr>

                </thead>

                <tbody>

                @foreach($evaluations as $eval)

                <tr>

                    <td>{{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}</td>

                    <td>{{ $eval->cleanliness }}</td>

                    <td>{{ $eval->service }}</td>

                    <td>{{ $eval->taste }}</td>

                    <td>{{ $eval->price }}</td>

                    <td>{{ $eval->comment ?? '-' }}</td>

                </tr>

                @endforeach

                </tbody>

            </table>

            @else

                <div class="alert alert-info">

                    No evaluations found.

                </div>

            @endif

        </div>

    </div>

</div>

</body>

</html>