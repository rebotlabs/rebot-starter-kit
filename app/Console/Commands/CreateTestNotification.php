<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\InvitationSent;
use Illuminate\Console\Command;

class CreateTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test {user_id?} {--type=invitation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test notification for a user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->argument('user_id');
        $type = $this->option('type');

        if (! $userId) {
            $user = User::first();
            if (! $user) {
                $this->error('No users found in the database. Please create a user first.');

                return 1;
            }
        } else {
            $user = User::find($userId);
            if (! $user) {
                $this->error("User with ID {$userId} not found.");

                return 1;
            }
        }

        // Create a test notification based on type
        switch ($type) {
            case 'invitation':
                $user->notify(new InvitationSent([
                    'organization' => 'Test Organization',
                    'invited_by' => 'Test User',
                ]));
                break;
            default:
                // Create a simple database notification
                $user->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\\Notifications\\TestNotification',
                    'data' => [
                        'title' => 'Test Notification',
                        'message' => 'This is a test notification to verify the notification system.',
                    ],
                    'read_at' => null,
                ]);
                break;
        }

        $this->info("Test notification created for user: {$user->email}");

        return 0;
    }
}
