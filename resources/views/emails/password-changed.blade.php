{{-- CHANGED: rebuilt on Laravel's mail template, the same look as the password reset email
     (ISU seal header, green theme in resources/views/vendor/mail). The wording is kept; the "ISU Cauayan DSS Portal"
     brand line and "Automated Email" footer give way to the template's seal and footer, and a sign-off is added
     as in the reset email. --}}
<x-mail::message>
# Your password was changed

Hi {{ $studentName }}, the password for your canteen evaluation account was changed on {{ $changedAtLabel }} (Philippine time).

If you made this change, you don't need to do anything.

If you did not change your password, contact the canteen system administrator right away so they can secure your account.

Regards,<br>
{{ config('app.name') }}
</x-mail::message>
