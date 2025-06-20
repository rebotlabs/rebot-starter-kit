<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => __('ui.avatar.validation.required'),
            'avatar.image' => __('ui.avatar.validation.must_be_image'),
            'avatar.mimes' => __('ui.avatar.validation.invalid_type'),
            'avatar.max' => __('ui.avatar.validation.max_size'),
        ];
    }
}
