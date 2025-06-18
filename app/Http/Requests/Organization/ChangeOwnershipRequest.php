<?php

declare(strict_types=1);

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class ChangeOwnershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        $organization = $this->route('organization');

        return $organization && $this->user()->id === $organization->owner_id;
    }

    public function rules(): array
    {
        return [
            'member_id' => 'required|exists:members,id',
        ];
    }
}
