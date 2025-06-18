<?php

declare(strict_types=1);

namespace App\Jobs\Invitation;

use App\Models\Invitation;
use App\Models\User;
use App\Notifications\EmailVerificationOtpNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AcceptInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $token,
        private ?array $userData = null
    ) {}

    public function handle(): array
    {
        $invitation = Invitation::where('accept_token', $this->token)
            ->where('status', 'pending')
            ->with('organization')
            ->firstOrFail();

        $user = User::where('email', $invitation->email)->first();

        if (! $user && $this->userData) {
            $user = User::create([
                'name' => $this->userData['name'],
                'email' => $invitation->email,
                'password' => Hash::make($this->userData['password']),
                'email_verified_at' => now(),
            ]);

            Auth::login($user);
        }

        $existingMember = $invitation->organization->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $existingMember) {
            $invitation->organization->members()->create([
                'user_id' => $user->id,
            ]);

            $user->assignRole($invitation->role);
        }

        // Delete the invitation after successful acceptance
        $invitation->delete();

        if (! $user->currentOrganization) {
            $user->currentOrganization()->associate($invitation->organization)->save();
        }

        $otp = $user->createOneTimePassword(20);
        $user->notify(new EmailVerificationOtpNotification($otp->password));

        return [
            'success' => true,
            'organization' => $invitation->organization,
            'user' => $user,
        ];
    }
}
