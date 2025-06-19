<x-mail::message>
# {{ __('mail.invitation.you_have_been_invited') }}

@if($invitation->user?->name)
{{ __('mail.invitation.hello_with_name', ['name' => $invitation->user->name]) }}
@else
{{ __('mail.invitation.hello_without_name') }}
@endif

{{ __('mail.invitation.invited_to_join', ['organization' => $invitation->organization->name, 'app_name' => config('app.name')]) }}

<x-mail::button :url="$invitationUrl">
{{ __('mail.invitation.view_invitation') }}
</x-mail::button>

{{ __('mail.salutation.thanks') }}<br>
{{ config('app.name') }}
</x-mail::message>
