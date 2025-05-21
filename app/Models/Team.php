<?php

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\Billable;

class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
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

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
