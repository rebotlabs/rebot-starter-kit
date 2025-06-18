<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
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
        ];
    }

    /**
     * Create a member with admin role.
     */
    public function admin(): static
    {
        return $this->afterCreating(function (Member $member) {
            $member->user->assignRole('admin');
        });
    }

    /**
     * Create a member with member role.
     */
    public function member(): static
    {
        return $this->afterCreating(function (Member $member) {
            $member->user->assignRole('member');
        });
    }
}
