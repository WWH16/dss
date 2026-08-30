<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email | ISU Cauayan Canteen Evaluation System</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Sora:wght@700;800&display=swap" rel="stylesheet">

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <style>
        /* OTP digit input group */
        .otp-input {
            width: 3rem;
            height: 3.25rem;
            text-align: center;
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            border: 1px solid #d4d4d4;
            border-radius: 4px;
            background: #fff;
            color: #171717;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
            caret-color: transparent;
        }
        .otp-input:focus {
            border-color: var(--color-brand-600, #16a34a);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-brand-600, #16a34a) 15%, transparent);
        }
        .otp-input.filled {
            border-color: var(--color-brand-600, #16a34a);
            background: color-mix(in srgb, var(--color-brand-600, #16a34a) 6%, white);
        }
        .otp-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgb(239 68 68 / 0.12);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%       { transform: translateX(-6px); }
            40%       { transform: translateX(6px); }
            60%       { transform: translateX(-4px); }
            80%       { transform: translateX(4px); }
        }
        .shake { animation: shake 0.35s ease-out; }

        @keyframes countdown-pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.5; }
        }
        .countdown-active { animation: countdown-pulse 1s ease-in-out infinite; }

        @media (prefers-reduced-motion: reduce) {
            .shake, .countdown-active { animation: none; }
        }
    </style>
</head>
<body class="bg-neutral-50/50 min-h-screen flex items-center justify-center px-4 relative antialiased">

    {{-- Back link --}}
    <div class="absolute top-6 left-6 z-50">
        <a href="{{ url('/login') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-[4px] border border-neutral-200 bg-white text-neutral-600 hover:text-neutral-900 hover:border-neutral-300 shadow-sm transition-all" aria-label="Go back to login">
            <ion-icon name="arrow-back-outline" class="text-xl leading-none"></ion-icon>
        </a>
    </div>

    <div class="w-full max-w-md bg-white rounded-[4px] border border-neutral-200/60 p-6 md:p-8 shadow-sm my-12 relative z-10">

        {{-- Header --}}
        <div class="text-center mb-6">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('assets/images/isu_logo.png') }}" alt="ISU Logo" class="w-14 h-14 object-contain">
            </div>
            <h1 class="text-lg font-display font-bold text-neutral-900 tracking-tight">Check your ISU email</h1>
            <p class="text-neutral-500 text-xs mt-1 font-medium">We sent a 6-digit verification code to</p>
            <p class="text-neutral-800 text-sm font-bold mt-0.5 tracking-tight">
                {{ \Illuminate\Support\Str::mask($email, '*', 1, strpos($email, '@') - 2) }}
            </p>
            <p class="text-[11px] text-neutral-400 mt-2 font-medium">
                Can't find it? Check your <span class="font-bold text-neutral-500">Spam</span> or <span class="font-bold text-neutral-500">Junk</span> folder.
            </p>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-[4px] text-xs font-semibold flex items-center gap-2">
                <ion-icon name="checkmark-circle" class="text-lg leading-none text-emerald-600 shrink-0"></ion-icon>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3.5 bg-red-50 border border-red-100 text-red-800 rounded-[4px] text-xs font-semibold flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-lg leading-none text-red-600 shrink-0"></ion-icon>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3.5 bg-red-50 border border-red-100 text-red-800 rounded-[4px] text-xs font-semibold flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-lg leading-none text-red-600 shrink-0"></ion-icon>
                {{ $errors->first() }}
            </div>
        @endif

        {{-- OTP Form --}}
        <form method="POST" action="{{ route('otp.verify') }}" id="otp-form" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-neutral-700 mb-3 text-center">Enter verification code</label>

                {{-- 6 individual digit boxes --}}
                <div class="flex justify-center gap-2" id="otp-boxes" role="group" aria-label="6-digit verification code">
                    @for($i = 1; $i <= 6; $i++)
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="otp-input"
                            id="otp-{{ $i }}"
                            aria-label="Digit {{ $i }}"
                            autocomplete="{{ $i === 1 ? 'one-time-code' : 'off' }}"
                        >
                    @endfor
                </div>

                {{-- Hidden combined field sent to server --}}
                <input type="hidden" name="otp" id="otp-hidden">
            </div>

            {{-- Expiry note --}}
            <p class="text-center text-[11px] text-neutral-400 font-medium -mt-1">
                This code expires in <span id="countdown" class="font-bold text-neutral-600">15:00</span>
            </p>

            <button
                type="submit"
                id="verify-btn"
                class="btn btn-primary w-full py-2.5 font-bold tracking-wide rounded-[4px] border-0 cursor-pointer flex items-center justify-center gap-2"
                disabled
            >
                <ion-icon id="verify-loader" name="hourglass-outline" class="text-lg leading-none btn-hourglass" aria-hidden="true" style="display:none;"></ion-icon>
                <span id="verify-btn-text">Verify Email</span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="flex items-center gap-3 my-4">
            <div class="flex-1 h-px bg-neutral-200"></div>
            <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider">or</span>
            <div class="flex-1 h-px bg-neutral-200"></div>
        </div>

        {{-- Resend --}}
        <form method="POST" action="{{ route('otp.resend') }}" id="resend-form">
            @csrf
            <button
                type="submit"
                id="resend-btn"
                class="w-full py-2 text-sm font-semibold text-neutral-500 hover:text-neutral-800 transition-colors rounded-[4px] border border-neutral-200 bg-white hover:border-neutral-300 disabled:opacity-40 disabled:cursor-not-allowed"
                disabled
            >
                <span id="resend-label">Resend code</span>
                <span id="resend-countdown" class="text-xs font-medium text-neutral-400 ml-1 hidden">(wait <span id="resend-secs">60</span>s)</span>
            </button>
        </form>

        <p class="text-center text-[11px] text-neutral-400 mt-4">
            Wrong email?
            <a href="{{ url('/login?tab=register') }}" class="text-brand-700 font-semibold hover:underline">Go back and register again</a>
        </p>



    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputs     = Array.from(document.querySelectorAll('.otp-input'));
        const hidden     = document.getElementById('otp-hidden');
        const verifyBtn  = document.getElementById('verify-btn');
        const otpBoxes   = document.getElementById('otp-boxes');
        const otpForm    = document.getElementById('otp-form');
        const resendBtn  = document.getElementById('resend-btn');
        const resendCountdown = document.getElementById('resend-countdown');
        const resendSecs = document.getElementById('resend-secs');
        const resendLabel = document.getElementById('resend-label');

        // ── OTP Input Logic ──────────────────────────────────────────────
        function syncHidden() {
            hidden.value = inputs.map(i => i.value).join('');
        }

        function isComplete() {
            return inputs.every(i => i.value.length === 1);
        }

        function updateVerifyBtn() {
            verifyBtn.disabled = !isComplete();
        }

        inputs.forEach((input, idx) => {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    if (!input.value && idx > 0) {
                        inputs[idx - 1].value = '';
                        inputs[idx - 1].classList.remove('filled');
                        inputs[idx - 1].focus();
                    }
                    input.classList.remove('filled');
                    syncHidden();
                    updateVerifyBtn();
                }
            });

            input.addEventListener('input', (e) => {
                const val = e.target.value.replace(/\D/g, '').slice(-1);
                input.value = val;
                input.classList.toggle('filled', val.length === 1);
                syncHidden();
                updateVerifyBtn();

                if (val && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
                pasted.split('').forEach((ch, i) => {
                    if (inputs[i]) {
                        inputs[i].value = ch;
                        inputs[i].classList.add('filled');
                    }
                });
                syncHidden();
                updateVerifyBtn();
                const next = inputs[Math.min(pasted.length, 5)];
                if (next) next.focus();
            });

            input.addEventListener('focus', () => {
                input.classList.remove('error');
            });
        });

        // Focus first box on load
        inputs[0].focus();

        // Shake on server error
        @if($errors->any())
            otpBoxes.classList.add('shake');
            inputs.forEach(i => i.classList.add('error'));
            setTimeout(() => otpBoxes.classList.remove('shake'), 400);
        @endif

        // ── Submit loader ─────────────────────────────────────────────────
        otpForm.addEventListener('submit', () => {
            document.getElementById('verify-loader').style.display = '';
            document.getElementById('verify-btn-text').textContent = 'Verifying…';
            verifyBtn.disabled = true;
        });

        // ── 15-minute expiry countdown ────────────────────────────────────
        let totalSeconds = {{ $expiresSeconds ?? 900 }};
        const countdownEl = document.getElementById('countdown');

        function updateExpiryDisplay() {
            if (totalSeconds <= 0) {
                countdownEl.textContent = 'Expired';
                countdownEl.classList.add('text-red-500');
                verifyBtn.disabled = true;
                return false;
            }
            const m = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
            const s = String(totalSeconds % 60).padStart(2, '0');
            countdownEl.textContent = `${m}:${s}`;

            if (totalSeconds <= 60) countdownEl.classList.add('countdown-active', 'text-red-500');
            return true;
        }

        if (updateExpiryDisplay()) {
            const expiryTimer = setInterval(() => {
                totalSeconds--;
                if (!updateExpiryDisplay()) {
                    clearInterval(expiryTimer);
                }
            }, 1000);
        }

        // ── Resend 60-second cooldown ─────────────────────────────────────
        let cooldown = {{ $resendCooldown ?? 0 }};

        function startResendCooldown() {
            if (cooldown <= 0) {
                resendBtn.disabled = false;
                resendLabel.classList.remove('hidden');
                resendCountdown.classList.add('hidden');
                return;
            }

            resendBtn.disabled = true;
            resendLabel.classList.add('hidden');
            resendCountdown.classList.remove('hidden');
            resendSecs.textContent = cooldown;

            const resendTimer = setInterval(() => {
                cooldown--;
                resendSecs.textContent = cooldown;
                if (cooldown <= 0) {
                    clearInterval(resendTimer);
                    resendBtn.disabled = false;
                    resendLabel.classList.remove('hidden');
                    resendCountdown.classList.add('hidden');
                }
            }, 1000);
        }

        startResendCooldown();

        document.getElementById('resend-form').addEventListener('submit', () => {
            cooldown = 60;
            startResendCooldown();
        });
    });
    </script>
</body>
</html>
