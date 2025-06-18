<?php

declare(strict_types=1);

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class SwitchOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $organization = $this->route('organization');
        $user = $this->user();

        if (! $organization || ! $user) {
            return false;
        }

        // User must be a member of this organization or the owner
        return $organization->members()->where('user_id', $user->id)->exists() ||
               $organization->owner_id === $user->id;
    }

    public function rules(): array
    {
        return [];
    }
}
