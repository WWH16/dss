<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    /**
     * Show the OTP verification page.
     * Passes the exact remaining expiry and resend cooldown seconds.
     */
    public function show(Request $request)
    {
        $email = $request->query('email') ?? session('otp_email');

        if (!$email) {
            return redirect('/login')->with('error', 'Session expired. Please register again.');
        }

        // If account is already created & verified, redirect to login directly
        if (User::where('email', $email)->whereNotNull('email_verified_at')->exists()) {
            session()->forget('otp_email');
            return redirect('/login')->with('success', 'Your email address is already verified! You may now log in.');
        }

        $record = EmailVerification::where('email', $email)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$record) {
            return redirect('/login')->with('error', 'No pending verification found. Please register again.');
        }

        session(['otp_email' => $email]);

        $presetOtp = $request->query('code');
        $expiredMessage = null;

        // Check if latest OTP is expired
        if ($record->expires_at->isPast()) {
            $expiresSeconds = 0;
            $presetOtp = null; // Do not pre-fill expired code
            session()->flash('error', 'Your verification code has expired (valid for 15 minutes). Please click "Resend code" below.');
        } else {
            // Exact remaining seconds before code expires
            $expiresSeconds = max(0, (int) now()->diffInSeconds($record->expires_at, false));

            // Check if preset code from link matches the active code in DB
            if ($presetOtp && $presetOtp !== $record->otp) {
                $presetOtp = null; // Do not pre-fill outdated code from an old email
                session()->flash('error', 'This verification link is outdated. Please check your inbox for the newest code or click "Resend code".');
            }
        }

        // Exact remaining seconds before resend is allowed (60s throttle)
        $secondsPassed  = (int) $record->created_at->diffInSeconds(now());
        $resendCooldown = max(0, 60 - $secondsPassed);

        return view('auth.verify-otp', [
            'email'          => $email,
            'expiresSeconds' => $expiresSeconds,
            'resendCooldown' => $resendCooldown,
            'presetOtp'      => $presetOtp,
        ]);
    }

    /**
     * Verify the submitted OTP and create the User account.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = session('otp_email');

        if (!$email) {
            return redirect('/login')->with('error', 'Session expired. Please register again.');
        }

        if (User::where('email', $email)->whereNotNull('email_verified_at')->exists()) {
            session()->forget('otp_email');
            return redirect('/login')->with('success', 'Your email address is already verified! You may now log in.');
        }

        $record = EmailVerification::where('email', $email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$record) {
            return back()->with('error', 'Invalid verification code. Please check your code and try again.');
        }

        // Mark OTP as used
        $record->update(['used' => true]);

        // Create the user now if payload exists
        if ($record->payload) {
            $userData = $record->payload;
            $userData['email_verified_at'] = now();

            // Prevent duplicate creation if user already exists
            if (!User::where('email', $email)->exists()) {
                User::create($userData);
            }
        } else {
            // Fallback for existing user records
            User::where('email', $email)->update([
                'email_verified_at' => now(),
            ]);
        }

        session()->forget('otp_email');

        return redirect('/login')->with('success', 'Email verified and account created! You may now log in.');
    }

    /**
     * Resend a fresh OTP to the email in session.
     */
    public function resend(Request $request)
    {
        $email = session('otp_email');

        if (!$email) {
            return redirect('/login')->with('error', 'Session expired. Please register again.');
        }

        if (User::where('email', $email)->whereNotNull('email_verified_at')->exists()) {
            session()->forget('otp_email');
            return redirect('/login')->with('success', 'Your email address is already verified! You may now log in.');
        }

        // Throttle: check if a valid OTP was sent in the last 60 seconds
        $recent = EmailVerification::where('email', $email)
            ->where('used', false)
            ->where('created_at', '>', now()->subSeconds(60))
            ->exists();

        if ($recent) {
            return back()->with('error', 'Please wait 60 seconds before requesting a new code.');
        }

        // Retrieve pending payload from previous verification record
        $lastRecord = EmailVerification::where('email', $email)->latest()->first();
        $payload    = $lastRecord ? $lastRecord->payload : null;
        $studentName = $payload['name'] ?? 'Student';

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerification::create([
            'email'      => $email,
            'otp'        => $otp,
            'payload'    => $payload,
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($email)->send(new OtpMail($otp, $studentName, $email));

        return back()->with('success', 'A new verification code has been sent to your email.');
    }
}
