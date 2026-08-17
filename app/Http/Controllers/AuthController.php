<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegister()
    {
        $stalls = \Illuminate\Support\Facades\DB::table('stalls')->where('is_active', true)->orderBy('name')->get();

        return view('auth.login', [
            'activeTab' => 'register',
            'selectedRole' => 'student',
            'error' => session('error'),
            'success' => session('success'),
            'stalls' => $stalls,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'role' => 'required|in:student,staff,admin',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'course' => 'required_if:role,student|nullable|string|max:255',
            'year_level' => 'required_if:role,student|nullable|string|max:255',
            'student_number' => 'required_if:role,student|nullable|string|max:255|unique:users,student_number',
        ]);

        User::create([
            'role' => $request->role,
            'name' => trim($request->name),
            'email' => trim($request->email),
            'course' => $request->role === 'student' ? $request->course : null,
            'year_level' => $request->role === 'student' ? $request->year_level : null,
            'student_number' => $request->role === 'student' ? $request->student_number : null,
            'stall_id' => null, // Staff must be assigned by Admin for security
            'stall_name' => null,
            'password' => Hash::make($request->password),
        ]);

        $message = $request->role === 'staff'
            ? 'Staff account created successfully! An administrator will assign your food stall.'
            : 'Account created successfully! You may now log in.';

        return redirect('/login')->with('success', $message);
    }
}