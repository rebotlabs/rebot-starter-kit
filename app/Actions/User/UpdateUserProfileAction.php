<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;

class UpdateUserProfileAction
{
    public function execute(User $user, array $data): User
    {
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}
