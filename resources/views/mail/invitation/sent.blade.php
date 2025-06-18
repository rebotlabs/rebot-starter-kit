<x-mail::message>
# You have been invited!

Hello {{ $invitation->user?->name ?? '' }},

You have been invited to join the organization **{{ $invitation->organization->name }}**
 on **{{ config('app.name') }}**.

<x-mail::button :url="$invitationUrl">
View Invitation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
