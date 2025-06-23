<?php

declare(strict_types=1);

use App\Actions\User\DeleteUserAccountAction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new DeleteUserAccountAction;
});

it('deletes the user account', function () {
    expect(User::where('id', $this->user->id)->exists())->toBeTrue();

    $this->action->execute($this->user);

    expect(User::where('id', $this->user->id)->exists())->toBeFalse();
});

it('logs out the user before deletion', function () {
    Auth::shouldReceive('logout')->once();

    $this->action->execute($this->user);

    expect(User::where('id', $this->user->id)->exists())->toBeFalse();
});
