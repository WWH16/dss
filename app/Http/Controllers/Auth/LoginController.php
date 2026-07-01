<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin(Request $request)
    {
        $error = $request->get('error');
        $success = $request->get('success');

        $allowedRoles = ['student', 'staff', 'admin'];
        $selectedRole = $request->get('role', 'student');

        if (!in_array($selectedRole, $allowedRoles)) {
            $selectedRole = 'student';
        }

        return view('auth.login', compact(
            'error',
            'success',
            'selectedRole'
        ));
    }

    public function login(Request $request)
    {
        $role = $request->role;
        $password = $request->password;

        if ($role === 'student') {
            $credentials = [
                'student_number' => $request->student_number,
                'password' => $password
            ];
        } else {
            $credentials = [
                'email' => $request->email,
                'password' => $password
            ];
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== $role) {
                Auth::logout();
                return redirect()->back()->with('error', 'Invalid role selected');
            }

            if ($role === 'admin') {
                return redirect('/admin/dashboard');
            }

            if ($role === 'student') {
                return redirect('/student/dashboard');
            }

            return redirect('/staff/dashboard');
        }

        return redirect()->back()->with('error', 'Invalid credentials');
    }
}