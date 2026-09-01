<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authentication | Decision Support System: ISU Cauayan Canteen Client Evaluation System</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    {{-- Tailwind v4 + app CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Fonts: Plus Jakarta Sans + Sora --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Sora:wght@700;800&display=swap" rel="stylesheet">

    {{-- Ionicons (iOS Icon System) --}}
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        @keyframes icon-pop {
            0% { transform: scale(0.5); opacity: 0.5; }
            40% { transform: scale(1.3); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-icon-pop {
            animation: icon-pop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
    </style>
</head>
<body class="bg-neutral-50/50 min-h-screen flex items-center justify-center px-4 relative antialiased">


    <!-- Standalone Back Navigation Arrow -->
    <div class="absolute top-6 left-6 z-50">
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-[4px] border border-neutral-200 bg-white text-neutral-600 hover:text-neutral-900 hover:border-neutral-300 shadow-sm transition-all" aria-label="Go back">
            <ion-icon name="arrow-back-outline" class="text-xl leading-none"></ion-icon>
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
                <ion-icon name="checkmark-circle" class="text-lg leading-none text-emerald-600"></ion-icon>
                {{ $success }}
            </div>
        @endif

        @if($error)
            <div class="mb-4 p-4 bg-red-50 border border-red-100 text-red-800 rounded-[4px] text-xs font-semibold flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-lg leading-none text-red-600"></ion-icon>
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
                            <ion-icon id="login_password_icon" name="eye-outline" class="text-lg leading-none"></ion-icon>
                        </button>
                    </div>
                </div>

                <button type="submit" id="login-submit-btn" class="btn btn-primary w-full py-2.5 font-bold tracking-wide mt-2 rounded-[4px] border-0 cursor-pointer relative flex items-center justify-center gap-2">
                    <ion-icon id="login-btn-loader" name="hourglass-outline" class="text-lg leading-none btn-hourglass" aria-hidden="true" style="display:none;"></ion-icon>
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
                        <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><ion-icon name="alert-circle" class="text-sm leading-none"></ion-icon> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="register_name" class="block text-xs font-semibold text-neutral-700 mb-1.5">Full Name</label>
                    <input type="text" id="register_name" name="name" value="{{ old('name') }}" placeholder="e.g. Juan Dela Cruz" class="w-full px-4 py-2.5 bg-white border @error('name') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="name" required>
                    @error('name')
                        <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><ion-icon name="alert-circle" class="text-sm leading-none"></ion-icon> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="register_email_prefix" class="block text-xs font-semibold text-neutral-700 mb-1.5">Email</label>

                    {{-- Student: split input (prefix + @isu.edu.ph locked) --}}
                    {{-- Student: split input with locked _cyn@isu.edu.ph suffix --}}
                    <div id="register_email_student" class="flex rounded-[4px] overflow-hidden border @error('email') border-red-500 @else border-neutral-300 @enderror focus-within:border-brand-600 focus-within:ring-2 focus-within:ring-brand-600/15 transition-all">
                        <input
                            type="text"
                            id="register_email_prefix"
                            name="email_prefix"
                            value="{{ old('email_prefix', old('email') ? explode('_cyn@', old('email'))[0] : '') }}"
                            placeholder="yourname"
                            class="flex-1 min-w-0 px-4 py-2.5 text-sm font-medium text-neutral-800 placeholder:text-neutral-400 bg-white focus:outline-none"
                            autocomplete="off"
                        >
                        <span class="flex items-center px-3 bg-neutral-100 border-l border-neutral-300 text-sm font-semibold text-neutral-500 whitespace-nowrap select-none">
                            _cyn@isu.edu.ph
                        </span>
                    </div>

                    {{-- Hidden full email sent to server --}}
                    <input type="hidden" name="email" id="register_email_hidden">

                    {{-- Staff/Admin: plain email input --}}
                    <div id="register_email_other" style="display: none;">
                        <input
                            type="email"
                            id="register_email_plain"
                            name="email_other"
                            value="{{ old('email_other') }}"
                            placeholder="e.g. user@example.com"
                            class="w-full px-4 py-2.5 bg-white border @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400"
                            autocomplete="email"
                        >
                    </div>

                    @error('email')
                        <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><ion-icon name="alert-circle" class="text-sm leading-none"></ion-icon> {{ $message }}</p>
                    @enderror

                    <p id="register_email_hint" class="text-[11px] text-neutral-400 mt-1.5 font-medium">
                        Use your official ISU email. A verification code will be sent after registration.
                    </p>
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
                                <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><ion-icon name="alert-circle" class="text-sm leading-none"></ion-icon> {{ $message }}</p>
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
                                <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><ion-icon name="alert-circle" class="text-sm leading-none"></ion-icon> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="register_student_number" class="block text-xs font-semibold text-neutral-700 mb-1.5">Student Number</label>
                        <input type="text" id="register_student_number" name="student_number" value="{{ old('student_number') }}" placeholder="26-12345" class="w-full px-4 py-2.5 bg-white border @error('student_number') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="username">
                        @error('student_number')
                            <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><ion-icon name="alert-circle" class="text-sm leading-none"></ion-icon> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- STAFF ONBOARDING INFO --}}
                <div id="register_staff_field" style="display:none;" class="p-3 bg-neutral-50 border border-neutral-200 rounded-[4px] text-xs text-neutral-600 space-y-1">
                    <div class="flex items-center gap-1.5 font-bold text-neutral-800 text-[11px] uppercase tracking-wider">
                        <ion-icon name="shield-checkmark" class="text-brand-700 text-sm" aria-hidden="true"></ion-icon>
                        <span>Staff Security Verification</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-neutral-500">
                        Once registered, your account will be verified and assigned to your designated food stall by an Administrator.
                    </p>
                </div>

                {{-- PASSWORDS --}}
                <div class="space-y-4">
                    <div class="relative">
                        <label for="register_password" class="block text-xs font-semibold text-neutral-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" id="register_password" name="password" placeholder="••••••••" class="w-full pl-4 pr-11 py-2.5 bg-white border @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="new-password" required>
                            <button type="button" onclick="togglePasswordVisibility('register_password', 'register_password_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password visibility">
                                <ion-icon id="register_password_icon" name="eye-outline" class="text-lg leading-none"></ion-icon>
                            </button>
                        </div>

                        {{-- PASSWORD CRITERIA CHECKLIST (Structured Layout) --}}
                        <div id="password-criteria-box" class="mt-4">
                            
                            {{-- Segmented Progress Bar (Full Width) --}}
                            <div class="flex gap-1 w-full mb-0.5">
                                <div class="flex-1 h-[4px] bg-neutral-200 rounded-full overflow-hidden">
                                    <div id="pwd-bar-1" class="h-full w-0 transition-all duration-300 ease-out"></div>
                                </div>
                                <div class="flex-1 h-[4px] bg-neutral-200 rounded-full overflow-hidden">
                                    <div id="pwd-bar-2" class="h-full w-0 transition-all duration-300 ease-out"></div>
                                </div>
                                <div class="flex-1 h-[4px] bg-neutral-200 rounded-full overflow-hidden">
                                    <div id="pwd-bar-3" class="h-full w-0 transition-all duration-300 ease-out"></div>
                                </div>
                                <div class="flex-1 h-[4px] bg-neutral-200 rounded-full overflow-hidden">
                                    <div id="pwd-bar-4" class="h-full w-0 transition-all duration-300 ease-out"></div>
                                </div>
                            </div>
                            
                            {{-- Strength Text (Lower Right, No Label) --}}
                            <div class="mb-0.5 text-right">
                                <span id="pwd-strength-text" class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest transition-colors duration-300">NONE</span>
                            </div>

                            {{-- Checklist Items (Spaced 2x2 Grid) --}}
                            <div class="grid grid-cols-2 gap-x-4 gap-y-0 font-medium text-neutral-500">
                                <div id="req-length" class="flex items-center gap-1 transition-colors duration-300">
                                    <ion-icon name="ellipse-outline" class="text-[12px] leading-none text-neutral-400 req-icon transition-colors duration-300"></ion-icon>
                                    <span class="text-[9px] whitespace-nowrap">8+ characters</span>
                                </div>
                                <div id="req-case" class="flex items-center gap-1 transition-colors duration-300">
                                    <ion-icon name="ellipse-outline" class="text-[12px] leading-none text-neutral-400 req-icon transition-colors duration-300"></ion-icon>
                                    <span class="text-[9px] whitespace-nowrap">Upper & lowercase</span>
                                </div>
                                <div id="req-number" class="flex items-center gap-1 transition-colors duration-300">
                                    <ion-icon name="ellipse-outline" class="text-[12px] leading-none text-neutral-400 req-icon transition-colors duration-300"></ion-icon>
                                    <span class="text-[9px] whitespace-nowrap">One number</span>
                                </div>
                                <div id="req-symbol" class="flex items-center gap-1 transition-colors duration-300">
                                    <ion-icon name="ellipse-outline" class="text-[12px] leading-none text-neutral-400 req-icon transition-colors duration-300"></ion-icon>
                                    <span class="text-[9px] whitespace-nowrap">One symbol</span>
                                </div>
                            </div>
                        </div>

                        @error('password')
                            <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1"><ion-icon name="alert-circle" class="text-sm leading-none"></ion-icon> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="register_password_confirmation" class="block text-xs font-semibold text-neutral-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" id="register_password_confirmation" name="password_confirmation" placeholder="••••••••" class="w-full pl-4 pr-11 py-2.5 bg-white border @error('password_confirmation') border-red-500 focus:border-red-500 focus:ring-red-500/15 @else border-neutral-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 @enderror rounded-[4px] text-sm focus:outline-none font-medium text-neutral-800 placeholder:text-neutral-400" autocomplete="new-password" required>
                            <button type="button" onclick="togglePasswordVisibility('register_password_confirmation', 'register_password_confirm_icon')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Toggle password confirmation visibility">
                                <ion-icon id="register_password_confirm_icon" name="eye-outline" class="text-lg leading-none"></ion-icon>
                            </button>
                        </div>
                    </div>
                </div>


                <button type="submit" id="register-submit-btn" class="btn btn-primary w-full py-2.5 font-bold tracking-wide mt-2 rounded-[4px] border-0 cursor-pointer relative flex items-center justify-center gap-2">
                    <ion-icon id="register-btn-loader" name="hourglass-outline" class="text-lg leading-none btn-hourglass" aria-hidden="true" style="display:none;"></ion-icon>
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
            const isStudent = role === 'student';
            const isStaff = role === 'staff';

            // Toggle student fields visibility and required state
            document.getElementById('register_student_fields').style.display = isStudent ? 'block' : 'none';
            document.getElementById('register_course').required = isStudent;
            document.getElementById('register_year').required = isStudent;
            document.getElementById('register_student_number').required = isStudent;

            // Toggle email input style (split for students, plain for staff/admin)
            document.getElementById('register_email_student').style.display = isStudent ? 'flex' : 'none';
            document.getElementById('register_email_other').style.display = isStudent ? 'none' : 'block';
            document.getElementById('register_email_hint').style.display = isStudent ? 'block' : 'none';
            document.getElementById('register_email_plain').required = !isStudent;

            // Toggle staff fields visibility
            document.getElementById('register_staff_field').style.display = isStaff ? 'block' : 'none';
        }

        function togglePasswordVisibility(fieldId, iconId) {
            const pwdInput = document.getElementById(fieldId);
            const pwdIcon = document.getElementById(iconId);
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                pwdIcon.setAttribute('name', 'eye-off-outline');
            } else {
                pwdInput.type = 'password';
                pwdIcon.setAttribute('name', 'eye-outline');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            let initialTab = "{{ $activeTab ?? 'login' }}";
            @if($errors->any() && ($errors->has('name') || $errors->has('email') || $errors->has('student_number') || $errors->has('stall_id') || $errors->has('password') || $errors->has('course') || $errors->has('year_level')))
                initialTab = 'register';
            @endif
            switchTab(initialTab);
            toggleLoginFields();
            toggleRegisterFields();

            // Dynamic Password Validation Checklist
            const passwordInput = document.getElementById('register_password');
            const reqs = {
                length: { el: document.getElementById('req-length'), icon: document.querySelector('#req-length .req-icon'), check: (val) => val.length >= 8, state: null },
                case: { el: document.getElementById('req-case'), icon: document.querySelector('#req-case .req-icon'), check: (val) => /[a-z]/.test(val) && /[A-Z]/.test(val), state: null },
                number: { el: document.getElementById('req-number'), icon: document.querySelector('#req-number .req-icon'), check: (val) => /[0-9]/.test(val), state: null },
                symbol: { el: document.getElementById('req-symbol'), icon: document.querySelector('#req-symbol .req-icon'), check: (val) => /[^A-Za-z0-9]/.test(val), state: null }
            };

            const bar1 = document.getElementById('pwd-bar-1');
            const bar2 = document.getElementById('pwd-bar-2');
            const bar3 = document.getElementById('pwd-bar-3');
            const bar4 = document.getElementById('pwd-bar-4');
            const strengthText = document.getElementById('pwd-strength-text');

            function evaluatePassword() {
                const val = passwordInput.value;
                const isEmpty = val.length === 0;
                let validCount = 0;

                for (const key in reqs) {
                    const req = reqs[key];
                    const isValid = req.check(val);
                    if (isValid) validCount++;
                    
                    const newState = isEmpty ? 'empty' : (isValid ? 'valid' : 'invalid');

                    // Only update DOM if the visual state needs to change
                    if (req.state !== newState) {
                        req.state = newState;
                        
                        // Remove animation class before re-applying to allow re-triggering if needed
                        req.icon.classList.remove('animate-icon-pop');
                        
                        if (newState === 'empty') {
                            req.el.className = "flex items-center gap-1 text-neutral-500 transition-colors duration-300";
                            req.icon.className = "text-[12px] leading-none text-neutral-400 req-icon transition-colors duration-300";
                            req.icon.setAttribute('name', 'ellipse-outline');
                        } else if (newState === 'valid') {
                            req.el.className = "flex items-center gap-1 text-emerald-600 font-bold transition-colors duration-300";
                            // Add pop animation when it becomes valid
                            req.icon.className = "text-[12px] leading-none text-emerald-500 req-icon transition-colors duration-300 animate-icon-pop";
                            req.icon.setAttribute('name', 'checkmark-circle');
                        } else {
                            req.el.className = "flex items-center gap-1 text-neutral-500 transition-colors duration-300";
                            req.icon.className = "text-[12px] leading-none text-neutral-400 req-icon transition-colors duration-300";
                            req.icon.setAttribute('name', 'ellipse-outline');
                        }
                    }
                }

                // Update segmented strength meter with width animations and synchronized colors
                let barColor = 'bg-red-500'; // Default
                if (validCount === 1) barColor = 'bg-red-500';
                else if (validCount === 2) barColor = 'bg-orange-500';
                else if (validCount === 3) barColor = 'bg-amber-500';
                else if (validCount >= 4) barColor = 'bg-emerald-500';

                if (isEmpty) {
                    bar1.className = "h-full w-0 transition-all duration-300 ease-out bg-neutral-200";
                    bar2.className = "h-full w-0 transition-all duration-300 ease-out bg-neutral-200";
                    bar3.className = "h-full w-0 transition-all duration-300 ease-out bg-neutral-200";
                    bar4.className = "h-full w-0 transition-all duration-300 ease-out bg-neutral-200";
                    strengthText.textContent = "NONE";
                    strengthText.className = "text-[10px] font-bold text-neutral-400 uppercase tracking-widest transition-colors duration-300";
                } else {
                    // Update widths based on how many are valid, all using the synchronized barColor
                    bar1.className = `h-full transition-all duration-300 ease-out ${validCount >= 1 ? 'w-full ' + barColor : 'w-0 ' + barColor}`;
                    bar2.className = `h-full transition-all duration-300 ease-out ${validCount >= 2 ? 'w-full ' + barColor : 'w-0 ' + barColor}`;
                    bar3.className = `h-full transition-all duration-300 ease-out ${validCount >= 3 ? 'w-full ' + barColor : 'w-0 ' + barColor}`;
                    bar4.className = `h-full transition-all duration-300 ease-out ${validCount >= 4 ? 'w-full ' + barColor : 'w-0 ' + barColor}`;
                    
                    if (validCount === 1) {
                        strengthText.textContent = "WEAK";
                        strengthText.className = "text-[10px] font-bold text-red-600 uppercase tracking-widest transition-colors duration-300";
                    } else if (validCount === 2) {
                        strengthText.textContent = "FAIR";
                        strengthText.className = "text-[10px] font-bold text-orange-600 uppercase tracking-widest transition-colors duration-300";
                    } else if (validCount === 3) {
                        strengthText.textContent = "GOOD";
                        strengthText.className = "text-[10px] font-bold text-amber-600 uppercase tracking-widest transition-colors duration-300";
                    } else if (validCount === 4) {
                        strengthText.textContent = "STRONG";
                        strengthText.className = "text-[10px] font-bold text-emerald-600 uppercase tracking-widest transition-colors duration-300";
                    } else {
                        strengthText.textContent = "WEAK";
                        strengthText.className = "text-[10px] font-bold text-red-600 uppercase tracking-widest transition-colors duration-300";
                    }
                }
            }

            if (passwordInput) {
                passwordInput.addEventListener('input', evaluatePassword);
                // Run on load in case browser pre-filled password
                evaluatePassword();
            }

            // Student Email Prefix Input Auto-Sanitization (strip pasted domain suffix)
            const emailPrefixInput = document.getElementById('register_email_prefix');
            if (emailPrefixInput) {
                emailPrefixInput.addEventListener('input', (e) => {
                    let val = e.target.value.trim().toLowerCase();
                    // If user pastes full email or domain, clean it automatically
                    val = val.replace(/_cyn@isu\.edu\.ph$/i, '')
                             .replace(/@isu\.edu\.ph$/i, '')
                             .replace(/@.*$/, '')
                             .replace(/\s+/g, '');
                    e.target.value = val;
                });
            }

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
                    const role = document.getElementById('register_role').value;
                    const hiddenEmail = document.getElementById('register_email_hidden');
                    if (role === 'student') {
                        const prefix = document.getElementById('register_email_prefix').value.trim();
                        hiddenEmail.value = prefix + '_cyn@isu.edu.ph';
                    } else {
                        hiddenEmail.value = document.getElementById('register_email_plain').value.trim();
                    }

                    if (!handleFormSubmit(registerForm, 'register-submit-btn', 'register-btn-content', 'register-btn-loader', 'Registering…')) {
                        e.preventDefault();
                    }
                });
            }
        });

        function handleFormSubmit(formElement, btnId, contentId, loaderId, loadingText) {
            // Check HTML5 validity first before locking
            if (formElement.checkValidity && !formElement.checkValidity()) {
                return true; // Let browser trigger native error tooltips
            }

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