@php $isReset = isset($token); $recaptchaKey = $isReset ? null : config('services.recaptcha.site_key'); @endphp
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isReset ? 'Reset Password' : 'Forgot Password' }} | ISU Cauayan Canteen Evaluation System</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Sora:wght@700;800&display=swap" rel="stylesheet">

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    {{-- Google reCAPTCHA v3, on the send-link form only, as on the login page. --}}
    @if($recaptchaKey)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaKey }}"></script>
    @endif
</head>
<body class="bg-neutral-50/50 min-h-screen flex items-center justify-center px-4 relative antialiased">

    <div class="absolute top-6 left-6 z-50">
        <a href="{{ url('/login') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-[4px] border border-neutral-200 bg-white text-neutral-600 hover:text-neutral-900 hover:border-neutral-300 shadow-sm transition-all" aria-label="Go back to login">
            <ion-icon name="arrow-back-outline" class="text-xl leading-none"></ion-icon>
        </a>
    </div>

    <div class="w-full max-w-md bg-white rounded-[4px] border border-neutral-200/60 p-6 md:p-8 shadow-sm my-12 relative z-10">

        <div class="text-center mb-6">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('assets/images/isu_logo.png') }}" alt="ISU Logo" class="w-14 h-14 object-contain">
            </div>
            <h1 class="text-lg font-display font-bold text-neutral-900 tracking-tight">{{ $isReset ? 'Set a new password' : 'Forgot your password?' }}</h1>
            <p class="text-neutral-500 text-xs mt-1 font-medium">
                {{ $isReset ? 'Choose a new password for your account.' : 'Enter your account email and we will send you a reset link.' }}
            </p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-[4px] text-xs font-semibold flex items-center gap-2" role="status">
                <ion-icon name="checkmark-circle" class="text-lg leading-none text-emerald-600 shrink-0"></ion-icon>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3.5 bg-red-50 border border-red-100 text-red-800 rounded-[4px] text-xs font-semibold flex items-center gap-2" role="alert">
                <ion-icon name="alert-circle" class="text-lg leading-none text-red-600 shrink-0"></ion-icon>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ $isReset ? route('password.update') : route('password.email') }}" id="password-form" class="space-y-4">
            @csrf
            @if($isReset)
                <input type="hidden" name="token" value="{{ $token }}">
            @endif

            <div>
                <label for="email" class="block text-xs font-semibold text-neutral-700 mb-1.5">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" placeholder="e.g. user@example.com" class="w-full px-4 py-2.5 bg-white border @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm font-medium text-neutral-800 placeholder:text-neutral-400 focus:outline-none" autocomplete="email" required @unless($isReset) autofocus @endunless>
            </div>

            @if($isReset)
                <div>
                    <label for="password" class="block text-xs font-semibold text-neutral-700 mb-1.5">New Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="••••••••" class="w-full pl-4 pr-11 py-2.5 bg-white border @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm font-medium text-neutral-800 placeholder:text-neutral-400 focus:outline-none" autocomplete="new-password" required autofocus>
                        <button type="button" onclick="togglePasswordVisibility('password', 'password_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password visibility">
                            <ion-icon id="password_icon" name="eye-outline" class="text-lg leading-none"></ion-icon>
                        </button>
                    </div>
                    {{-- The register form's checklist, without its strength bar. --}}
                    <div class="grid grid-cols-2 gap-x-4 gap-y-0 mt-1.5 font-medium">
                        @foreach (['length' => '8+ characters', 'case' => 'Upper & lowercase', 'number' => 'One number', 'symbol' => 'One symbol'] as $rule => $label)
                            <div data-rule="{{ $rule }}" class="flex items-center gap-1 text-neutral-500 transition-colors duration-300">
                                <ion-icon name="ellipse-outline" class="text-[12px] leading-none text-neutral-400 transition-colors duration-300"></ion-icon>
                                <span class="text-[9px] whitespace-nowrap">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-neutral-700 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" class="w-full pl-4 pr-11 py-2.5 bg-white border border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 rounded-[4px] text-sm font-medium text-neutral-800 placeholder:text-neutral-400 focus:outline-none" autocomplete="new-password" required>
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'password_confirmation_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password confirmation visibility">
                            <ion-icon id="password_confirmation_icon" name="eye-outline" class="text-lg leading-none"></ion-icon>
                        </button>
                    </div>
                    {{-- Live match hint, wired by data-password-match in resources/js/app.js. --}}
                    <p data-password-match aria-live="polite" hidden class="text-red-600 data-[match=yes]:text-emerald-700 text-xs mt-1.5 font-semibold flex items-center gap-1"></p>
                </div>
            @endif

            @php $cooldown ??= 0; @endphp
            <button type="submit" id="password-submit-btn" class="btn btn-primary w-full py-2.5 font-bold tracking-wide mt-2 rounded-[4px] border-0 cursor-pointer relative flex items-center justify-center gap-2 disabled:opacity-45 disabled:cursor-not-allowed disabled:pointer-events-none" @disabled($cooldown > 0)>
                <ion-icon id="password-btn-loader" name="hourglass-outline" class="text-lg leading-none btn-hourglass" aria-hidden="true" style="display:none;"></ion-icon>
                <span id="password-btn-content">{{ $isReset ? 'Reset Password' : ($cooldown > 0 ? "Resend in {$cooldown}s" : 'Send Reset Link') }}</span>
            </button>

            @if($recaptchaKey)
                <input type="hidden" name="g_recaptcha_response" id="g_recaptcha_response">
                <p class="text-[10px] text-neutral-400 text-center mt-2 leading-tight">
                    Protected by reCAPTCHA (<a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" class="underline hover:text-neutral-600">Privacy</a> & <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer" class="underline hover:text-neutral-600">Terms</a>)
                </p>
            @endif
        </form>

        <p class="text-center text-[11px] text-neutral-400 mt-4">
            Remembered it?
            <a href="{{ url('/login') }}" class="text-brand-700 font-semibold hover:underline">Back to login</a>
        </p>

    </div>

    {{-- Same submit loading state as the login page; the eye toggle is shared from resources/js/app.js. --}}
    <script>
        // The disabled button blocks a second click or Enter, so no extra double-submit guard is needed.
        document.getElementById('password-form').addEventListener('submit', (e) => {
            const form = e.currentTarget;
            document.getElementById('password-btn-content').textContent = @json($isReset ? 'Resetting…' : 'Sending…');
            document.getElementById('password-btn-loader').style.display = '';
            const btn = document.getElementById('password-submit-btn');
            btn.disabled = true;
            btn.classList.add('btn-loading');
            form.classList.add('pointer-events-none');
            form.style.opacity = '0.6';

            // As on the login page: fetch a reCAPTCHA token first, then submit. If reCAPTCHA did not load,
            // the form still submits and the server-side check decides.
            const siteKey = @json($recaptchaKey);
            if (siteKey && typeof grecaptcha !== 'undefined') {
                e.preventDefault();
                grecaptcha.ready(() => grecaptcha.execute(siteKey, { action: 'forgot_password' })
                    .then(token => { document.getElementById('g_recaptcha_response').value = token; form.submit(); })
                    .catch(() => form.submit()));
            }
        });

        // Same four checks as the register form's checklist; each row turns green once met.
        const rules = {
            length: v => v.length >= 8,
            case: v => /[a-z]/.test(v) && /[A-Z]/.test(v),
            number: v => /[0-9]/.test(v),
            symbol: v => /[^A-Za-z0-9]/.test(v),
        };
        document.getElementById('password')?.addEventListener('input', (e) => {
            document.querySelectorAll('[data-rule]').forEach(row => {
                const ok = rules[row.dataset.rule](e.target.value);
                const icon = row.querySelector('ion-icon');
                row.classList.toggle('text-neutral-500', !ok);
                row.classList.toggle('text-emerald-600', ok);
                row.classList.toggle('font-bold', ok);
                icon.classList.toggle('text-neutral-400', !ok);
                icon.classList.toggle('text-emerald-500', ok);
                icon.setAttribute('name', ok ? 'checkmark-circle' : 'ellipse-outline');
            });
        });

        // After a link is sent, the button stays locked and counts down until another may be sent.
        (() => {
            let left = {{ $cooldown }};
            if (left <= 0) return;
            const btn = document.getElementById('password-submit-btn');
            const content = document.getElementById('password-btn-content');
            const timer = setInterval(() => {
                left--;
                content.textContent = left > 0 ? `Resend in ${left}s` : 'Send Reset Link';
                if (left <= 0) {
                    clearInterval(timer);
                    btn.disabled = false;
                }
            }, 1000);
        })();

        // Coming back with the browser's Back button restores a locked form from cache; reload it so the button is usable again.
        window.addEventListener('pageshow', (e) => {
            if (e.persisted) location.reload();
        });
    </script>
</body>
</html>
