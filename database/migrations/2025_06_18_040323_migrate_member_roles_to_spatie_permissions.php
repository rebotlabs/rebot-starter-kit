<?php

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $memberRole = Role::firstOrCreate(['name' => 'member']);

        // Migrate existing member roles to spatie permissions
        Member::with('user')->get()->each(function (Member $member) use ($adminRole, $memberRole) {
            $user = $member->user;
            if ($user) {
                // Assign role based on the member's current role
                $role = $member->role === 'admin' ? $adminRole : $memberRole;

                if (! $user->hasRole($role->name)) {
                    $user->assignRole($role);
                }
            }
        });
    }

    public function down(): void
    {
        // Remove all roles from users
        User::with('roles')->get()->each(function (User $user) {
            $user->syncRoles([]);
        });
    }
};
