<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    public function store(UploadedFile $file, string $directory = 'avatars'): string
    {
        // Generate a unique filename
        $filename = Str::random(40).'.'.$file->getClientOriginalExtension();

        // Store the file in the public disk
        $path = $file->storeAs($directory, $filename, 'public');

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function getUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function validateAvatar(UploadedFile $file): array
    {
        $errors = [];

        // Check file size (max 2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            $errors[] = __('ui.avatar.validation.max_size');
        }

        // Check file type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = __('ui.avatar.validation.invalid_type');
        }

        // Check image dimensions (optional - max 1000x1000)
        if ($file->getMimeType() && str_starts_with($file->getMimeType(), 'image/')) {
            $imageSize = getimagesize($file->getPathname());
            if ($imageSize && ($imageSize[0] > 1000 || $imageSize[1] > 1000)) {
                $errors[] = __('ui.avatar.validation.max_dimensions');
            }
        }

        return $errors;
    }
}
