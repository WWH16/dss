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
            'role' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        User::create([
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'course' => $request->course,
            'year_level' => $request->year_level,
            'student_number' => $request->student_number,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Account created successfully');
    }
}