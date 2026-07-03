@extends('layouts.app')

@section('title', 'Login | ISU Canteen DSS')

@section('content')
<div class="py-16 md:py-24 bg-neutral-50/50 min-h-[calc(100vh-8rem)] flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-white rounded-2xl border border-neutral-200/60 p-6 md:p-8 shadow-sm">

    <div class="text-center mb-6">
      <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Login</h1>
      <p class="text-neutral-500 text-xs mt-1">Access your evaluation dashboard</p>
    </div>

    @if($success)
      <div class="mb-4 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2">
        <span class="material-symbols-outlined text-lg leading-none">check_circle</span>
        {{ $success }}
      </div>
    @endif

    @if($error)
      <div class="mb-4 p-4 bg-red-50 border border-red-100 text-red-800 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2">
        <span class="material-symbols-outlined text-lg leading-none">error</span>
        {{ $error }}
      </div>
    @endif

    <form action="{{ url('/login') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Role</label>
        <select id="role" name="role" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800" onchange="toggleLoginFields()">
          <option value="student" {{ $selectedRole=='student'?'selected':'' }}>Student</option>
          <option value="staff" {{ $selectedRole=='staff'?'selected':'' }}>Staff</option>
          <option value="admin" {{ $selectedRole=='admin'?'selected':'' }}>Admin</option>
        </select>
      </div>

      <div id="student_number_field">
        <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Student Number</label>
        <input type="text" name="student_number" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800">
      </div>

      <div id="email_field">
        <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Email</label>
        <input type="email" name="email" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800">
      </div>

      <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1.5">Password</label>
        <input type="password" name="password" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800" required>
      </div>

      <button type="submit" class="btn btn-primary w-full py-2.5 font-bold tracking-wide mt-2">
        Login
      </button>
    </form>

  </div>
</div>
@endsection

@section('scripts')
<script>
  function toggleLoginFields() {
    const role = document.getElementById('role').value;
    document.getElementById('student_number_field').style.display =
      role === 'student' ? 'block' : 'none';

    document.getElementById('email_field').style.display =
      role === 'student' ? 'none' : 'block';
  }

  document.addEventListener('DOMContentLoaded', toggleLoginFields);
</script>
@endsection