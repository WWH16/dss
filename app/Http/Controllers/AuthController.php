<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.login', [
            'activeTab' => 'register',
            'selectedRole' => 'student',
            'error' => session('error'),
            'success' => session('success'),
        ]);
    }

    public function register(Request $request)
    {
        $rules = [
            'role'                 => 'required|in:student,staff',
            'name'                 => 'required|string|max:255',
            'email'                => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'             => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'course'               => 'required_if:role,student|nullable|string|max:255',
            'year_level'           => 'required_if:role,student|nullable|string|max:255',
            'student_number'       => 'required_if:role,student|nullable|string|max:255|unique:users,student_number',
            'g_recaptcha_response' => [new Recaptcha('register')],
        ];

        // Students must use an official ISU Cauayan email (format: name_cyn@isu.edu.ph)
        if ($request->role === 'student') {
            $rules['email'][] = 'regex:/^[a-zA-Z0-9.+\-]+_cyn@isu\.edu\.ph$/i';
        }

        $request->validate($rules, [
            'name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'Students must use their official ISU Cauayan email address (e.g. jdelacruz_cyn@isu.edu.ph).',
            'course.required_if' => 'Please select your academic course.',
            'year_level.required_if' => 'Please select your year level.',
            'student_number.required_if' => 'Please enter your student number.',
            'student_number.unique' => 'This student number is already registered.',
            'password.required' => 'Please create a password.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $userData = [
            'role'           => $request->role,
            'name'           => trim($request->name),
            'email'          => trim($request->email),
            'course'         => $request->role === 'student' ? $request->course : null,
            'year_level'     => $request->role === 'student' ? $request->year_level : null,
            'student_number' => $request->role === 'student' ? $request->student_number : null,
            'stall_id'       => null,
            'password'       => Hash::make($request->password),
        ];

        // For students: DO NOT create User yet. Send OTP and save pending payload in EmailVerification table.
        if ($request->role === 'student') {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $verification = \App\Models\EmailVerification::create([
                'email'      => $userData['email'],
                'otp'        => $otp,
                'payload'    => $userData,
                'expires_at' => now()->addMinutes(15),
            ]);

            try {
                \Illuminate\Support\Facades\Mail::to($userData['email'])
                    ->send(new \App\Mail\OtpMail($otp, $userData['name'], $userData['email']));
            } catch (\Throwable $e) {
                $verification->delete();
                \Illuminate\Support\Facades\Log::error('Failed to send OTP email: ' . $e->getMessage());

                return back()->withInput()->withErrors([
                    'email' => 'Unable to send verification email. Please check your email configuration or try again.',
                ]);
            }

            session(['otp_email' => $userData['email']]);

            return redirect()->route('otp.show', ['email' => $userData['email']]);
        }

        // Staff/Admin: create user directly
        User::create($userData);

        $message = $request->role === 'staff'
            ? 'Staff account created successfully! An administrator will assign your food stall.'
            : 'Account created successfully! You may now log in.';

        return redirect('/login')->with('success', $message);
    }
}