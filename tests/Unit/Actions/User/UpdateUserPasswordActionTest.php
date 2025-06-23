<?php

declare(strict_types=1);

use App\Actions\User\UpdateUserPasswordAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create(['password' => Hash::make('old-password')]);
    $this->action = new UpdateUserPasswordAction;
});

it('updates user password with hashed value', function () {
    $newPassword = 'new-password-123';

    $this->action->execute($this->user, $newPassword);

    $this->user->refresh();
    expect(Hash::check($newPassword, $this->user->password))->toBeTrue();
    expect(Hash::check('old-password', $this->user->password))->toBeFalse();
});

it('properly hashes the password', function () {
    $plainPassword = 'test-password-123';

    $this->action->execute($this->user, $plainPassword);

    $this->user->refresh();
    expect($this->user->password)->not->toBe($plainPassword);
    expect(Hash::check($plainPassword, $this->user->password))->toBeTrue();
});
