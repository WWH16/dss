@extends('layouts.app')

@section('title', 'Register | ISU Canteen DSS')

@section('content')
<div class="py-16 md:py-24 bg-neutral-50/50 min-h-[calc(100vh-8rem)] flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl border border-neutral-200/60 p-6 md:p-8 shadow-sm">

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Register</h1>
            <p class="text-neutral-500 text-xs mt-1">Create your account for student or staff access</p>
        </div>

        {{-- ERROR HANDLING --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-100 text-red-800 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-lg leading-none">error</span>
                <ul class="mb-0 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ url('/register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Role</label>
                <select id="role" name="role" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800" onchange="toggleRegisterFields()">
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Full Name</label>
                <input type="text" name="name" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800" required>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800" required>
            </div>

            {{-- STUDENT FIELDS --}}
            <div id="student_fields" class="space-y-4">

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Course</label>
                    <select name="course" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800">
                        <option value="">Select your course</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSHM">BSHM</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Year</label>
                    <select name="year_level" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800">
                        <option value="">Select year</option>
                        <option value="1st year">1st year</option>
                        <option value="2nd year">2nd year</option>
                        <option value="3rd year">3rd year</option>
                        <option value="4th year">4th year</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Student Number</label>
                    <input type="text" name="student_number" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800">
                </div>

            </div>

            {{-- STAFF FIELD --}}
            <div id="staff_field" style="display:none;">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Stall Number / Name</label>
                    <input type="text" name="stall_name" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800" required>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800" required>
            </div>

            <button class="btn btn-primary w-full py-2.5 font-bold tracking-wide mt-2">Register</button>
        </form>

    </div>
</div>
@endsection

@section('scripts')
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
@endsection