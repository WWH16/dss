<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Recaptcha;
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
        $request->validate([
            'email' => 'required|email',
            'g_recaptcha_response' => [new Recaptcha('forgot_password')],
        ]);

        // Unknown emails are told so: the register form already reveals which emails are taken, so hiding it
        // here protected nothing. reCAPTCHA and the route throttle slow down anyone checking emails in bulk.
        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->withInput($request->only('email'))->withErrors(['email' => $status === Password::INVALID_USER
                ? "We couldn't find an account with that email. Make sure it's the email you registered with."
                : __($status)]);
        }

        $request->session()->put('reset_link_sent_at', now()->timestamp);

        return back()->withInput($request->only('email'))->with('success', "A reset link is on its way. Check your Spam folder if you don't see it.");
    }

    public function showReset(Request $request, string $token)
    {
        // Laravel keeps one link per email, so a used, expired, or replaced link is caught here, before the
        // user types a new password, and the page offers a new link instead of the form.
        $email = (string) $request->query('email');
        $user = $email !== '' ? Password::broker()->getUser(['email' => $email]) : null;
        $expired = ! $user || ! Password::broker()->tokenExists($user, $token);

        return view('auth.password', ['token' => $token, 'email' => $email, 'expired' => $expired]);
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

        // A link that expired or was replaced while the form was open goes back to the link page,
        // which now shows the "request a new link" message.
        if ($status === Password::INVALID_TOKEN) {
            return redirect()->route('password.reset', ['token' => $request->token, 'email' => $request->email]);
        }

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
        }

        return redirect('/login')->with('success', 'Your password has been reset. You can log in now.');
    }
}
