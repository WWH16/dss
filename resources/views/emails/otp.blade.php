{{-- CHANGED: rebuilt on Laravel's mail template, the same look as the password reset email
     (ISU seal header, green theme in resources/views/vendor/mail). The wording is kept; the "ISU Cauayan DSS Portal"
     brand line and "Automated Email" footer give way to the template's seal and footer, and a sign-off and a
     copy-this-link line are added as in the reset email. --}}
<x-mail::message>
# Verify your email address

Hi {{ $studentName }}, click the button below to verify your institutional email address instantly, or copy your 6-digit code:

<x-mail::button :url="$verificationUrl">
Verify Email Address
</x-mail::button>

Or enter code manually:

<x-mail::panel>
<span style="font-size: 28px; font-weight: bold; letter-spacing: 8px;">{{ $otp }}</span>
</x-mail::panel>

Code valid for 15 minutes. Do not share with anyone.

If you didn't create an account, you can safely ignore this email.

Regards,<br>
{{ config('app.name') }}

<x-slot:subcopy>
If you're having trouble clicking the "Verify Email Address" button, copy and paste this URL into your web browser: <span class="break-all">[{{ $verificationUrl }}]({{ $verificationUrl }})</span>
</x-slot:subcopy>
</x-mail::message>
