<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $organization = $this->route('organization');

        return $organization && $organization->currentUserCanManage();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => __('ui.logo.validation.required'),
            'logo.image' => __('ui.logo.validation.must_be_image'),
            'logo.mimes' => __('ui.logo.validation.invalid_type'),
            'logo.max' => __('ui.logo.validation.max_size'),
        ];
    }
}
