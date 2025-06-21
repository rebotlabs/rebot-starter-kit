<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;

class SetupStripeWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:setup-webhooks {--url= : The webhook URL to use (defaults to APP_URL)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up Stripe webhook endpoints for Laravel Cashier';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Set Stripe API key
        Stripe::setApiKey(config('cashier.secret'));

        if (! config('cashier.secret')) {
            $this->error('STRIPE_SECRET environment variable is not set.');

            return 1;
        }

        // Get the webhook URL
        $baseUrl = $this->option('url') ?: config('app.url');
        $webhookUrl = rtrim($baseUrl, '/').'/stripe/webhook';

        // Get the events from config
        $events = config('cashier.webhook.events', []);

        if (empty($events)) {
            $this->error('No webhook events configured in cashier.webhook.events');

            return 1;
        }

        try {
            $this->info('Setting up Stripe webhook endpoint...');
            $this->info("Webhook URL: {$webhookUrl}");
            $this->info('Events: '.implode(', ', $events));

            // Create the webhook endpoint
            $endpoint = WebhookEndpoint::create([
                'url' => $webhookUrl,
                'enabled_events' => $events,
                'description' => 'Laravel Cashier Webhook for '.config('app.name'),
            ]);

            $this->info('✅ Webhook endpoint created successfully!');
            $this->line('');
            $this->line('Webhook Details:');
            $this->line("ID: {$endpoint->id}");
            $this->line("URL: {$endpoint->url}");
            $this->line("Secret: {$endpoint->secret}");
            $this->line('');
            $this->warn('⚠️  IMPORTANT: Add this webhook secret to your .env file:');
            $this->line("STRIPE_WEBHOOK_SECRET={$endpoint->secret}");
            $this->line('');
            $this->info('🎉 Webhook setup complete! Remember to update your .env file with the webhook secret.');

        } catch (\Exception $e) {
            $this->error('Failed to create webhook endpoint: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
