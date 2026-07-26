<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Evaluation</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body class="survey-page">

<div class="survey-paper">

    <div class="survey-top">

        <img src="{{ asset('assets/images/isu_logo.jpg') }}"
             class="survey-logo"
             alt="ISU Logo">

        <div class="survey-title">

            <p class="line-1 text-uppercase fw-bold mb-1">
                Isabela State University
            </p>

            <p class="line-2 mb-1">
                Cauayan Campus
            </p>

            <h1 class="text-success">
                Citizen / Client Satisfaction Survey
            </h1>

            <p class="line-3 mb-0">
                Please answer each statement by selecting the rating that best reflects your opinion.
            </p>

        </div>

        <img src="{{ asset('assets/images/bagong-pilipinas-logo.png') }}"
             class="survey-logo"
             alt="Bagong Pilipinas">

    </div>

    <div class="survey-divider"></div>

    <div class="survey-section-box mb-4">

        <div class="row g-3 survey-info-grid">

            <div class="col-md-4 survey-info-item">
                <dl class="mb-0">
                    <dt>Name</dt>
                    <dd>{{ $profile->name ?? $profile->student_number }}</dd>
                </dl>
            </div>

            <div class="col-md-4 survey-info-item">
                <dl class="mb-0">
                    <dt>Date</dt>
                    <dd>{{ now()->format('F d, Y') }}</dd>
                </dl>
            </div>

            <div class="col-md-4 survey-info-item">
                <dl class="mb-0">
                    <dt>Time</dt>
                    <dd>{{ now()->format('h:i A') }}</dd>
                </dl>
            </div>

        </div>

    </div>

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    @if($stalls->isEmpty())

        <div class="alert alert-warning">

            No stalls are available yet.

            Please ask an administrator to add stall information first.

        </div>

    @else

    <form action="{{ route('student.evaluation.store') }}" method="POST">

        @csrf

        <div class="survey-section-box mb-4">

            <div class="row g-3 align-items-center">

                <div class="col-md-8">

                    <label class="form-label">

                        Food Stall Evaluated

                    </label>

                    <select
                        name="stall_id"
                        class="form-select"
                        required>

                        <option value="">
                            Choose a Stall
                        </option>

                        @foreach($stalls as $stall)

                            <option value="{{ $stall->id }}">

                                {{ $stall->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-4">

                    <div class="alert alert-info mb-0 py-3">

                        <strong>Scale</strong>

                        <div>5 = Strongly Agree / Excellent</div>

                        <div>4 = Agree / Very Good</div>

                        <div>3 = Neutral / Good</div>

                        <div>2 = Disagree / Fair</div>

                        <div>1 = Strongly Disagree / Poor</div>

                    </div>

                </div>

            </div>

        </div>
                <div class="survey-table table-responsive">

            <table class="table table-bordered align-middle">

                <thead>

                    <tr>

                        <th style="width:70%">
                            Statement
                        </th>

                        <th class="text-center">5</th>

                        <th class="text-center">4</th>

                        <th class="text-center">3</th>

                        <th class="text-center">2</th>

                        <th class="text-center">1</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($displayStatements as $index => $statement)

                        <tr>

                            <td>

                                {{ $index + 1 }}.
                                {{ $statement['statement'] }}

                            </td>

                            @for($val = 5; $val >= 1; $val--)

                                <td class="text-center">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="responses[{{ $statement['id'] }}]"
                                        value="{{ $val }}"
                                        required>

                                </td>

                            @endfor

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <div class="survey-section-box mb-4">

            <label class="form-label">

                Compliments / Suggestions / Complaints

            </label>

            <textarea
                name="comment"
                rows="4"
                class="form-control"
                placeholder="Share compliments, suggestions, or complaints here.">{{ old('comment') }}</textarea>

        </div>

        <div class="survey-note">

            Please mark only one rating for each statement.

        </div>
                <div class="survey-footer mt-4">

            <div class="survey-footer-left">

                <div class="line"></div>

                <div class="small text-muted">

                    Signature over printed name

                </div>

            </div>

            <div class="survey-footer-right">

                <div class="line"></div>

                <div class="small text-muted">

                    Designation / Department

                </div>

            </div>

        </div>

        <div class="survey-footnote mt-3">

            This information is treated confidentially and used to improve student satisfaction.

        </div>

        <div class="mt-4">

            <button type="submit" class="btn btn-success">

                Submit Evaluation

            </button>

        </div>

    </form>

    @endif

    <div class="mt-4 text-end">

        <form action="{{ route('logout') }}" method="POST" id="logout-form">

            @csrf

            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">

                Logout

            </button>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Logout Confirmation Modal (Bootstrap 5) -->
<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="logoutConfirmModalLabel">
          <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor" class="text-danger">
              <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
          </svg>
          Confirm Logout
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-secondary small py-3">
        Are you sure you want to end your session?
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-sm btn-danger px-3" id="bootstrap-confirm-logout">Logout</button>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var confirmBtn = document.getElementById('bootstrap-confirm-logout');
        var logoutForm = document.getElementById('logout-form');
        if (confirmBtn && logoutForm) {
            confirmBtn.addEventListener('click', function () {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = 'Logging out...';
                logoutForm.submit();
            });
        }
    });
</script>

</body>
</html>