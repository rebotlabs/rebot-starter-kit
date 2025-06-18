<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Notifications\EmailVerificationOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class InvitationController extends Controller
{
    /**
     * Show the invitation handling page.
     */
    public function handle(Request $request, string $token)
    {
        $invitation = Invitation::where('accept_token', $token)
            ->where('status', 'pending')
            ->with('organization')
            ->firstOrFail();

        $existingUser = User::where('email', $invitation->email)->first();

        return Inertia::render('invitation/handle', [
            'invitation' => $invitation,
            'existingUser' => $existingUser ? true : false,
            'isAuthenticated' => Auth::check(),
            'currentUserEmail' => Auth::user()?->email,
        ]);
    }

    /**
     * Accept the invitation and create account if needed.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('accept_token', $token)
            ->where('status', 'pending')
            ->with('organization')
            ->firstOrFail();

        $user = User::where('email', $invitation->email)->first();

        // If user doesn't exist, create account
        if (! $user) {
            $request->validate([
                'name' => 'required|string|max:255',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            Auth::login($user);
        } else {
            // If user exists but not authenticated, require login
            if (! Auth::check() || Auth::user()->email !== $invitation->email) {
                return back()->withErrors([
                    'auth' => 'Please log in with the invited email address to accept this invitation.',
                ]);
            }
        }

        // Create membership if not already a member
        $existingMember = $invitation->organization->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $existingMember) {
            $invitation->organization->members()->create([
                'user_id' => $user->id,
            ]);

            // Assign role using spatie permissions
            $user->assignRole($invitation->role);
        }

        // Update invitation status
        $invitation->update(['status' => 'accepted']);

        // Set current organization if user doesn't have one
        if (! $user->currentOrganization) {
            $user->currentOrganization()->associate($invitation->organization)->save();
        }

        $otp = $user->createOneTimePassword(20);

        $user->notify(new EmailVerificationOtp($otp->password));

        return redirect()->route('organization.overview', $invitation->organization)
            ->with('message', 'Invitation accepted successfully!');
    }

    /**
     * Reject the invitation.
     */
    public function reject(Request $request, string $token)
    {
        $invitation = Invitation::where('accept_token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update(['status' => 'rejected']);

        return redirect()->route('home')
            ->with('message', 'Invitation rejected.');
    }

    /**
     * Login with existing account for invitation.
     */
    public function login(Request $request, string $token)
    {
        $invitation = Invitation::where('accept_token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($request->email !== $invitation->email) {
            return back()->withErrors([
                'email' => 'You must log in with the invited email address.',
            ]);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return back()->with('message', 'Logged in successfully. You can now accept the invitation.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
