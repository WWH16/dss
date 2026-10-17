{{-- ADDED: role change notice on the shared Laravel mail template, same look as emails/password-changed. --}}
<x-mail::message>
# Your role was changed

Hi {{ $name }}, an administrator changed your role on the canteen evaluation system from **{{ $oldRoleLabel }}** to **{{ $newRoleLabel }}** on {{ $changedAtLabel }} (Philippine time).

The next time you sign in, choose your new role on the login page.

If you think this change is a mistake, contact the canteen system administrator.

Regards,<br>
{{ config('app.name') }}
</x-mail::message>
