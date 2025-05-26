<?php

namespace App\Models;

use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'team_id',
        'user_id',
        'email',
        'role',
        'accept_token',
        'reject_token',
        'status',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
