<x-mail::message>
    # You have been invited!

    Hello {{ $invitation->user?->name ?? '' }},

    You have been invited to join the team **{{ $invitation->team->name }}** on **{{ config('app.name') }}**.

    <x-mail::button :url="$acceptUrl">
        Accept Invitation
    </x-mail::button>

    <x-mail::button color="red" :url="$rejectUrl">
        Reject Invitation
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
