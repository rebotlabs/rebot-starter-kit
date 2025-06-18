<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class LeaveOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $organization = $this->route('organization');
        $user = $this->user();

        if (! $organization || ! $user) {
            return false;
        }

        // User must be a member of the organization (owners are also members)
        return $organization->members()->where('user_id', $user->id)->exists() ||
               $organization->owner_id === $user->id;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'current_password'],
        ];
    }
}
