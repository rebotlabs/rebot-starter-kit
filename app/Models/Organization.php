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
}
