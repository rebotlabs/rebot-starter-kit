<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'email' => fake()->unique()->email(),
            'role' => fake()->randomElement(['admin', 'member']),
            'accept_token' => Str::random(32),
            'reject_token' => Str::random(32),
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }
}
