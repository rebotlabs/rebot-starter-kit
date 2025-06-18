<?php

declare(strict_types=1);

use App\Jobs\User\DeleteUserAccountJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('deletes the user account', function () {
    expect(User::where('id', $this->user->id)->exists())->toBeTrue();

    $job = new DeleteUserAccountJob($this->user);
    $job->handle();

    expect(User::where('id', $this->user->id)->exists())->toBeFalse();
});

it('logs out the user before deletion', function () {
    Auth::shouldReceive('logout')->once();

    $job = new DeleteUserAccountJob($this->user);
    $job->handle();

    expect(User::where('id', $this->user->id)->exists())->toBeFalse();
});
