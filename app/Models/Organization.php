<?php

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;

class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use Billable, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'owner_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the current user's role in this organization
     */
    public function getCurrentUserRole(): ?string
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        // Check if user is the owner
        if ($this->owner_id === $user->id) {
            return 'owner';
        }

        // Check if user is a member and get their role from spatie permissions
        $member = $this->members()->where('user_id', $user->id)->first();
        if (! $member) {
            return null;
        }

        // Get the user's roles and return the first one (assuming single role per organization context)
        $roles = $user->getRoleNames();

        return $roles->first() ?? 'member';
    }

    /**
     * Check if current user can manage organization settings
     */
    public function currentUserCanManage(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Check if user is the owner
        if ($this->owner_id === $user->id) {
            return true;
        }

        // Check if user is an admin member using spatie permissions
        $member = $this->members()->where('user_id', $user->id)->first();
        if (! $member) {
            return false;
        }

        return $user->hasRole('admin');
    }
}
