@component('mail::message')
# Get token to reset password


{{-- {{ $token }} --}}
copy the following token and use it as parameter in end point
''api/change-password/"
to change your password.
this token is only valid for 15 minutes


<p> <b>{{ $token }}</b> </p>

Thanks<br>
{{ config('app.name') }}
@endcomponent
