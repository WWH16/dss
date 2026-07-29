<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.login', [
            'activeTab' => 'register',
            'selectedRole' => 'student',
            'error' => session('error'),
            'success' => session('success')
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'role' => 'required|in:student,staff,admin',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed',
            'course' => 'required_if:role,student|nullable|string|max:255',
            'year_level' => 'required_if:role,student|nullable|string|max:255',
            'student_number' => 'required_if:role,student|nullable|string|max:255|unique:users,student_number',
            'stall_name' => 'required_if:role,staff|nullable|string|max:255',
        ]);

        User::create([
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'course' => $request->role === 'student' ? $request->course : null,
            'year_level' => $request->role === 'student' ? $request->year_level : null,
            'student_number' => $request->role === 'student' ? $request->student_number : null,
            'stall_name' => $request->role === 'staff' ? $request->stall_name : null,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Account created successfully');
    }
}