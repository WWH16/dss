<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function showForgot(Request $request)
    {
        // Seconds left before the send button unlocks again, matching the broker's own resend throttle.
        $cooldown = max(0, config('auth.passwords.users.throttle') - (now()->timestamp - $request->session()->get('reset_link_sent_at', 0)));

        return view('auth.password', ['cooldown' => $cooldown]);
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_THROTTLED) {
            return back()->withInput()->withErrors(['email' => __($status)]);
        }

        // Same reply whether or not the email is registered, so the form cannot be used to find accounts.
        $request->session()->put('reset_link_sent_at', now()->timestamp);

        return back()->withInput($request->only('email'))->with('success', 'If that email belongs to an account, a reset link is on its way.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('auth.password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // The model's 'hashed' cast hashes the password. A new remember_token also logs out every
                // "Remember me" device, as on a normal password change.
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => str()->random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
        }

        return redirect('/login')->with('success', 'Your password has been reset. You can log in now.');
    }
}
