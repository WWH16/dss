<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authentication | ISU Canteen DSS</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    {{-- Tailwind v4 + app CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Fonts: Plus Jakarta Sans + Sora + Material Symbols --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Sora:wght@700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
</head>
<body class="bg-neutral-50/50 min-h-screen flex items-center justify-center px-4 relative antialiased">

    <!-- Standalone Back Navigation Arrow -->
    <div class="absolute top-6 left-6 z-50">
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-[4px] border border-neutral-200 bg-white text-neutral-600 hover:text-neutral-900 hover:border-neutral-300 shadow-sm transition-all" aria-label="Go back">
            <span class="material-symbols-outlined text-xl leading-none">arrow_back</span>
        </a>
    </div>

    <!-- Unified Form Card -->
    <div class="w-full max-w-md bg-white rounded-[4px] border border-neutral-200/60 p-6 md:p-8 shadow-sm my-12 relative z-10">

        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('assets/images/isu_logo.png') }}" alt="ISU Logo" class="w-14 h-14 object-contain">
            </div>
            <h1 class="text-xl font-display font-bold text-neutral-900 tracking-tight" id="auth-heading">Access Platform</h1>
            <p class="text-neutral-500 text-xs mt-1" id="auth-subheading">Sign in or register to get started</p>
        </div>

        {{-- Laravel Errors/Success Alerts --}}
        @if($success)
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-[4px] text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-lg leading-none">check_circle</span>
                {{ $success }}
            </div>
        @endif

        @if($error)
            <div class="mb-4 p-4 bg-red-50 border border-red-100 text-red-800 rounded-[4px] text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-lg leading-none">error</span>
                {{ $error }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-100 text-red-800 rounded-[4px] text-xs font-semibold">
                <div class="flex items-center gap-2 mb-2 font-bold">
                    <span class="material-symbols-outlined text-lg leading-none">error</span>
                    <span>Submission Errors</span>
                </div>
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $errorItem)
                        <li>{{ $errorItem }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tab Switcher -->
        <div class="grid grid-cols-2 border border-neutral-200 p-1 rounded-[4px] bg-neutral-100/80 mb-6">
            <button type="button" id="tab-login-btn" onclick="switchTab('login')" class="py-2 text-sm font-semibold rounded-[4px] transition-all focus:outline-none cursor-pointer">Login</button>
            <button type="button" id="tab-register-btn" onclick="switchTab('register')" class="py-2 text-sm font-semibold rounded-[4px] transition-all focus:outline-none cursor-pointer">Register</button>
        </div>

        {{-- ── LOGIN FORM BLOCK ────────────────────────────────────────────── --}}
        <div id="login-form-block">
            <form action="{{ url('/login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="login_role" class="block text-xs font-semibold text-neutral-700 mb-1.5">Role</label>
                    <select id="login_role" name="role" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" onchange="toggleLoginFields()">
                        <option value="student" {{ $selectedRole=='student'?'selected':'' }}>Student</option>
                        <option value="staff" {{ $selectedRole=='staff'?'selected':'' }}>Staff</option>
                        <option value="admin" {{ $selectedRole=='admin'?'selected':'' }}>Admin</option>
                    </select>
                </div>

                <div id="login_student_number_field">
                    <label for="login_student_number" class="block text-xs font-semibold text-neutral-700 mb-1.5">Student Number</label>
                    <input type="text" id="login_student_number" name="student_number" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" autocomplete="username">
                </div>

                <div id="login_email_field">
                    <label for="login_email" class="block text-xs font-semibold text-neutral-700 mb-1.5">Email</label>
                    <input type="email" id="login_email" name="email" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" autocomplete="username email">
                </div>

                <div>
                    <label for="login_password" class="block text-xs font-semibold text-neutral-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="login_password" name="password" class="w-full pl-4 pr-11 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" autocomplete="current-password" required>
                        <button type="button" onclick="togglePasswordVisibility('login_password', 'login_password_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password visibility">
                            <span id="login_password_icon" class="material-symbols-outlined text-lg leading-none">visibility</span>
                        </button>
                    </div>
                </div>

                <button type="submit" id="login-submit-btn" class="btn btn-primary w-full py-2.5 font-bold tracking-wide mt-2 rounded-[4px] border-0 cursor-pointer relative flex items-center justify-center">
                    <span id="login-btn-content" class="flex items-center justify-center gap-2">
                        Login
                    </span>
                    <span id="login-btn-loader" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>

        {{-- ── REGISTER FORM BLOCK ─────────────────────────────────────────── --}}
        <div id="register-form-block" style="display: none;">
            <form method="POST" action="{{ url('/register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="register_role" class="block text-xs font-semibold text-neutral-700 mb-1.5">Role</label>
                    <select id="register_role" name="role" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" onchange="toggleRegisterFields()">
                        <option value="student">Student</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div>
                    <label for="register_name" class="block text-xs font-semibold text-neutral-700 mb-1.5">Full Name</label>
                    <input type="text" id="register_name" name="name" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" autocomplete="name" required>
                </div>

                <div>
                    <label for="register_email" class="block text-xs font-semibold text-neutral-700 mb-1.5">Email</label>
                    <input type="email" id="register_email" name="email" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" autocomplete="email" required>
                </div>

                {{-- STUDENT FIELDS --}}
                <div id="register_student_fields" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="register_course" class="block text-xs font-semibold text-neutral-700 mb-1.5">Course</label>
                            <select id="register_course" name="course" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800">
                                <option value="">Select course</option>
                                <option value="BSIT">BSIT</option>
                                <option value="BSCS">BSCS</option>
                                <option value="BSHM">BSHM</option>
                            </select>
                        </div>

                        <div>
                            <label for="register_year" class="block text-xs font-semibold text-neutral-700 mb-1.5">Year</label>
                            <select id="register_year" name="year_level" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800">
                                <option value="">Select year</option>
                                <option value="1st year">1st year</option>
                                <option value="2nd year">2nd year</option>
                                <option value="3rd year">3rd year</option>
                                <option value="4th year">4th year</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="register_student_number" class="block text-xs font-semibold text-neutral-700 mb-1.5">Student Number</label>
                        <input type="text" id="register_student_number" name="student_number" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" autocomplete="username">
                    </div>
                </div>

                {{-- STAFF FIELD --}}
                <div id="register_staff_field" style="display:none;">
                    <div>
                        <label for="register_stall_name" class="block text-xs font-semibold text-neutral-700 mb-1.5">Stall Number / Name</label>
                        <input type="text" id="register_stall_name" name="stall_name" class="w-full px-4 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800">
                    </div>
                </div>

                {{-- PASSWORDS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="register_password" class="block text-xs font-semibold text-neutral-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input type="password" id="register_password" name="password" class="w-full pl-4 pr-11 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" autocomplete="new-password" required>
                            <button type="button" onclick="togglePasswordVisibility('register_password', 'register_password_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password visibility">
                                <span id="register_password_icon" class="material-symbols-outlined text-lg leading-none">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="register_password_confirmation" class="block text-xs font-semibold text-neutral-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="register_password_confirmation" name="password_confirmation" class="w-full pl-4 pr-11 py-2.5 bg-white border border-neutral-300 rounded-[4px] text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 font-medium text-neutral-800" autocomplete="new-password" required>
                            <button type="button" onclick="togglePasswordVisibility('register_password_confirmation', 'register_password_confirm_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password confirmation visibility">
                                <span id="register_password_confirm_icon" class="material-symbols-outlined text-lg leading-none">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" id="register-submit-btn" class="btn btn-primary w-full py-2.5 font-bold tracking-wide mt-2 rounded-[4px] border-0 cursor-pointer relative flex items-center justify-center">
                    <span id="register-btn-content" class="flex items-center justify-center gap-2">
                        Register
                    </span>
                    <span id="register-btn-loader" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>

    </div>

    <script>
        function switchTab(tab) {
            const loginBtn = document.getElementById('tab-login-btn');
            const registerBtn = document.getElementById('tab-register-btn');
            const loginBlock = document.getElementById('login-form-block');
            const registerBlock = document.getElementById('register-form-block');
            const heading = document.getElementById('auth-heading');
            const subheading = document.getElementById('auth-subheading');

            if (tab === 'login') {
                loginBtn.className = "py-2 text-sm font-semibold rounded-[4px] bg-white text-neutral-900 shadow-sm transition-all focus:outline-none cursor-pointer";
                registerBtn.className = "py-2 text-sm font-semibold rounded-[4px] text-neutral-500 hover:text-neutral-800 transition-all focus:outline-none cursor-pointer";
                loginBlock.style.display = 'block';
                registerBlock.style.display = 'none';
                heading.textContent = "Login";
                subheading.textContent = "Access your evaluation dashboard";
            } else {
                loginBtn.className = "py-2 text-sm font-semibold rounded-[4px] text-neutral-500 hover:text-neutral-800 transition-all focus:outline-none cursor-pointer";
                registerBtn.className = "py-2 text-sm font-semibold rounded-[4px] bg-white text-neutral-900 shadow-sm transition-all focus:outline-none cursor-pointer";
                loginBlock.style.display = 'none';
                registerBlock.style.display = 'block';
                heading.textContent = "Register";
                subheading.textContent = "Create your account for student or staff access";
            }
        }

        function toggleLoginFields() {
            const role = document.getElementById('login_role').value;
            document.getElementById('login_student_number_field').style.display =
                role === 'student' ? 'block' : 'none';

            document.getElementById('login_email_field').style.display =
                role === 'student' ? 'none' : 'block';
        }

        function toggleRegisterFields() {
            const role = document.getElementById('register_role').value;
            document.getElementById('register_student_fields').style.display =
                (role === 'student') ? 'block' : 'none';

            document.getElementById('register_staff_field').style.display =
                (role === 'staff') ? 'block' : 'none';
        }

        function togglePasswordVisibility(fieldId, iconId) {
            const pwdInput = document.getElementById(fieldId);
            const pwdIcon = document.getElementById(iconId);
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                pwdIcon.textContent = 'visibility_off';
            } else {
                pwdInput.type = 'password';
                pwdIcon.textContent = 'visibility';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const initialTab = "{{ $activeTab ?? 'login' }}";
            switchTab(initialTab);
            toggleLoginFields();
            toggleRegisterFields();

            // Intercept form submissions
            const loginForm = document.querySelector('#login-form-block form');
            const registerForm = document.querySelector('#register-form-block form');

            if (loginForm) {
                loginForm.addEventListener('submit', (e) => {
                    handleFormSubmit(loginForm, 'login-submit-btn', 'login-btn-content', 'login-btn-loader');
                });
            }

            if (registerForm) {
                registerForm.addEventListener('submit', (e) => {
                    handleFormSubmit(registerForm, 'register-submit-btn', 'register-btn-content', 'register-btn-loader');
                });
            }
        });

        function handleFormSubmit(formElement, btnId, contentId, loaderId) {
            const btn = document.getElementById(btnId);
            const content = document.getElementById(contentId);
            const loader = document.getElementById(loaderId);

            // Double submit check
            if (formElement.dataset.submitting === 'true') {
                return;
            }
            formElement.dataset.submitting = 'true';

            // Show loading state
            content.classList.add('hidden');
            loader.classList.remove('hidden');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            // Visual locking feedback (pointer-events-none prevents clicks without disabling inputs)
            formElement.classList.add('pointer-events-none', 'opacity-70');
        }
    </script>
</body>
</html>