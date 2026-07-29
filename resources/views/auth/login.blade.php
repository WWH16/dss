<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authentication | Decision Support System: ISU Cauayan Canteen Client Evaluation System</title>
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
            <h1 class="text-lg font-display font-bold text-neutral-900 tracking-tight" id="auth-heading">ISU Cauayan Canteen Client Evaluation System</h1>
            <p class="text-neutral-500 text-xs mt-1 font-medium" id="auth-subheading">Decision Support System Portal</p>
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
                        <option value="student" {{ old('role', $selectedRole) == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="staff" {{ old('role', $selectedRole) == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role', $selectedRole) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div id="login_student_number_field">
                    <label for="login_student_number" class="block text-xs font-semibold text-neutral-700 mb-1.5">Student Number</label>
                    <input type="text" id="login_student_number" name="student_number" value="{{ old('student_number') }}" placeholder="26-12345" class="w-full px-4 py-2.5 bg-white border @if($error) border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @endif rounded-[4px] text-sm font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="username">
                </div>

                <div id="login_email_field">
                    <label for="login_email" class="block text-xs font-semibold text-neutral-700 mb-1.5">Email</label>
                    <input type="email" id="login_email" name="email" value="{{ old('email') }}" placeholder="e.g. user@example.com" class="w-full px-4 py-2.5 bg-white border @if($error) border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @endif rounded-[4px] text-sm font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="username email">
                </div>

                <div>
                    <label for="login_password" class="block text-xs font-semibold text-neutral-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="login_password" name="password" placeholder="••••••••" class="w-full pl-4 pr-11 py-2.5 bg-white border @if($error) border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @endif rounded-[4px] text-sm font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="current-password" required>
                        <button type="button" onclick="togglePasswordVisibility('login_password', 'login_password_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password visibility">
                            <span id="login_password_icon" class="material-symbols-outlined text-lg leading-none">visibility</span>
                        </button>
                    </div>
                </div>

                <button type="submit" id="login-submit-btn" class="btn btn-primary w-full py-2.5 font-bold tracking-wide mt-2 rounded-[4px] border-0 cursor-pointer relative flex items-center justify-center gap-2">
                    <span id="login-btn-loader" class="material-symbols-outlined text-lg leading-none btn-hourglass" aria-hidden="true" style="display:none;">hourglass_top</span>
                    <span id="login-btn-content">Login</span>
                </button>
            </form>
        </div>

        {{-- ── REGISTER FORM BLOCK ─────────────────────────────────────────── --}}
        <div id="register-form-block" style="display: none;">
            <form method="POST" action="{{ url('/register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="register_role" class="block text-xs font-semibold text-neutral-700 mb-1.5">Role</label>
                    <select id="register_role" name="role" class="w-full px-4 py-2.5 bg-white border @error('role') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800" onchange="toggleRegisterFields()">
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm leading-none">error</span> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="register_name" class="block text-xs font-semibold text-neutral-700 mb-1.5">Full Name</label>
                    <input type="text" id="register_name" name="name" value="{{ old('name') }}" placeholder="e.g. Juan Dela Cruz" class="w-full px-4 py-2.5 bg-white border @error('name') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="name" required>
                    @error('name')
                        <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm leading-none">error</span> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="register_email" class="block text-xs font-semibold text-neutral-700 mb-1.5">Email</label>
                    <input type="email" id="register_email" name="email" value="{{ old('email') }}" placeholder="e.g. student@example.com" class="w-full px-4 py-2.5 bg-white border @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="email" required>
                    @error('email')
                        <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm leading-none">error</span> {{ $message }}</p>
                    @enderror
                </div>

                {{-- STUDENT FIELDS --}}
                <div id="register_student_fields" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="register_course" class="block text-xs font-semibold text-neutral-700 mb-1.5">Course</label>
                            <select id="register_course" name="course" class="w-full px-4 py-2.5 bg-white border @error('course') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800">
                                <option value="">Select course</option>
                                <option value="BSIT" {{ old('course') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                                <option value="BSCS" {{ old('course') == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                                <option value="BSHM" {{ old('course') == 'BSHM' ? 'selected' : '' }}>BSHM</option>
                            </select>
                            @error('course')
                                <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm leading-none">error</span> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="register_year" class="block text-xs font-semibold text-neutral-700 mb-1.5">Year</label>
                            <select id="register_year" name="year_level" class="w-full px-4 py-2.5 bg-white border @error('year_level') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800">
                                <option value="">Select year</option>
                                <option value="1st year" {{ old('year_level') == '1st year' ? 'selected' : '' }}>1st year</option>
                                <option value="2nd year" {{ old('year_level') == '2nd year' ? 'selected' : '' }}>2nd year</option>
                                <option value="3rd year" {{ old('year_level') == '3rd year' ? 'selected' : '' }}>3rd year</option>
                                <option value="4th year" {{ old('year_level') == '4th year' ? 'selected' : '' }}>4th year</option>
                            </select>
                            @error('year_level')
                                <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm leading-none">error</span> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="register_student_number" class="block text-xs font-semibold text-neutral-700 mb-1.5">Student Number</label>
                        <input type="text" id="register_student_number" name="student_number" value="{{ old('student_number') }}" placeholder="26-12345" class="w-full px-4 py-2.5 bg-white border @error('student_number') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="username">
                        @error('student_number')
                            <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm leading-none">error</span> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- STAFF FIELD --}}
                <div id="register_staff_field" style="display:none;">
                    <div>
                        <label for="register_stall_name" class="block text-xs font-semibold text-neutral-700 mb-1.5">Stall Number / Name</label>
                        <input type="text" id="register_stall_name" name="stall_name" value="{{ old('stall_name') }}" placeholder="e.g. Stall #1 - Food Hub" class="w-full px-4 py-2.5 bg-white border @error('stall_name') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400">
                        @error('stall_name')
                            <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm leading-none">error</span> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- PASSWORDS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="register_password" class="block text-xs font-semibold text-neutral-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input type="password" id="register_password" name="password" placeholder="••••••••" class="w-full pl-4 pr-11 py-2.5 bg-white border @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="new-password" required>
                            <button type="button" onclick="togglePasswordVisibility('register_password', 'register_password_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password visibility">
                                <span id="register_password_icon" class="material-symbols-outlined text-lg leading-none">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm leading-none">error</span> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="register_password_confirmation" class="block text-xs font-semibold text-neutral-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="register_password_confirmation" name="password_confirmation" placeholder="••••••••" class="w-full pl-4 pr-11 py-2.5 bg-white border @error('password_confirmation') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="new-password" required>
                            <button type="button" onclick="togglePasswordVisibility('register_password_confirmation', 'register_password_confirm_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password confirmation visibility">
                                <span id="register_password_confirm_icon" class="material-symbols-outlined text-lg leading-none">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" id="register-submit-btn" class="btn btn-primary w-full py-2.5 font-bold tracking-wide mt-2 rounded-[4px] border-0 cursor-pointer relative flex items-center justify-center gap-2">
                    <span id="register-btn-loader" class="material-symbols-outlined text-lg leading-none btn-hourglass" aria-hidden="true" style="display:none;">hourglass_top</span>
                    <span id="register-btn-content">Register</span>
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
            let initialTab = "{{ $activeTab ?? 'login' }}";
            @if($errors->any() && ($errors->has('name') || $errors->has('email') || $errors->has('student_number') || $errors->has('stall_name') || $errors->has('password') || $errors->has('course') || $errors->has('year_level')))
                initialTab = 'register';
            @endif
            switchTab(initialTab);
            toggleLoginFields();
            toggleRegisterFields();

            // Intercept form submissions
            const loginForm = document.querySelector('#login-form-block form');
            const registerForm = document.querySelector('#register-form-block form');

            if (loginForm) {
                loginForm.addEventListener('submit', (e) => {
                    if (!handleFormSubmit(loginForm, 'login-submit-btn', 'login-btn-content', 'login-btn-loader', 'Logging in…')) {
                        e.preventDefault();
                    }
                });
            }

            if (registerForm) {
                registerForm.addEventListener('submit', (e) => {
                    if (!handleFormSubmit(registerForm, 'register-submit-btn', 'register-btn-content', 'register-btn-loader', 'Registering…')) {
                        e.preventDefault();
                    }
                });
            }
        });

        function handleFormSubmit(formElement, btnId, contentId, loaderId, loadingText) {
            const btn = document.getElementById(btnId);
            const content = document.getElementById(contentId);
            const loader = document.getElementById(loaderId);

            // Block double submit
            if (formElement.dataset.submitting === 'true') {
                return false;
            }
            formElement.dataset.submitting = 'true';

            // Swap button text to loading label
            content.textContent = loadingText;

            // Show hourglass icon
            loader.style.display = '';

            // Disable button
            btn.disabled = true;
            btn.classList.add('btn-loading');

            // Dim form inputs to signal locked state
            formElement.classList.add('pointer-events-none');
            formElement.style.opacity = '0.6';

            return true;
        }
    </script>
</body>
</html>