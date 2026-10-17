@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
{{-- CHANGED: the ISU seal alone replaces Laravel's logo and the app name. It is embedded in the email as cid:isu-logo
     (see User::sendPasswordResetNotification), so it shows without the app being reachable from the internet.
     A Laravel-styled email that does not embed it shows nothing here, as alt is empty. --}}
<img src="cid:isu-logo" class="logo" alt="">
</a>
</td>
</tr>
