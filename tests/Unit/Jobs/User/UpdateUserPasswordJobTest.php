<?php

declare(strict_types=1);

use App\Jobs\User\UpdateUserPasswordJob;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create(['password' => Hash::make('old-password')]);
});

it('updates user password with hashed value', function () {
    $newPassword = 'new-password-123';

    $job = new UpdateUserPasswordJob($this->user, $newPassword);
    $job->handle();

    $this->user->refresh();
    expect(Hash::check($newPassword, $this->user->password))->toBeTrue();
    expect(Hash::check('old-password', $this->user->password))->toBeFalse();
});

it('properly hashes the password', function () {
    $plainPassword = 'test-password-123';

    $job = new UpdateUserPasswordJob($this->user, $plainPassword);
    $job->handle();

    $this->user->refresh();
    expect($this->user->password)->not->toBe($plainPassword);
    expect(Hash::check($plainPassword, $this->user->password))->toBeTrue();
});
