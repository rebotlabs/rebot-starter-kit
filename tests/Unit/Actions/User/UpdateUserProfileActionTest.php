<?php

declare(strict_types=1);

use App\Actions\User\UpdateUserProfileAction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'email_verified_at' => now(),
    ]);

    $this->action = new UpdateUserProfileAction;
});

it('updates user profile with provided data', function () {
    $data = [
        'name' => 'Jane Smith',
    ];

    $result = $this->action->execute($this->user, $data);

    expect($result)->toBeInstanceOf(User::class);
    expect($this->user->fresh()->name)->toBe('Jane Smith');
    expect($this->user->fresh()->email)->toBe('john@example.com');
});

it('resets email verification when email is changed', function () {
    $data = [
        'email' => 'newemail@example.com',
    ];

    $this->action->execute($this->user, $data);

    $this->user->refresh();
    expect($this->user->email)->toBe('newemail@example.com');
    expect($this->user->email_verified_at)->toBeNull();
});

it('preserves email verification when email is not changed', function () {
    $originalVerifiedAt = $this->user->email_verified_at;
    $data = [
        'name' => 'Updated Name',
    ];

    $this->action->execute($this->user, $data);

    $this->user->refresh();
    expect($this->user->email_verified_at)->toEqual($originalVerifiedAt);
});

it('updates multiple fields at once', function () {
    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $this->action->execute($this->user, $data);

    $this->user->refresh();
    expect($this->user->name)->toBe('Updated Name');
    expect($this->user->email)->toBe('updated@example.com');
    expect($this->user->email_verified_at)->toBeNull();
});

it('returns the updated user instance', function () {
    $data = ['name' => 'New Name'];

    $result = $this->action->execute($this->user, $data);

    expect($result)->toBeInstanceOf(User::class);
    expect($result->id)->toBe($this->user->id);
    expect($result->name)->toBe('New Name');
});
