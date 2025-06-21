<?php

use App\Models\Organization;
use App\Models\User;

describe('Stripe Webhooks', function () {
    it('webhook endpoint exists and is accessible', function () {
        $response = $this->post('/stripe/webhook', [], [
            'Content-Type' => 'application/json',
        ]);

        // Should return 400 or 422 due to missing/invalid signature, not 404
        expect($response->status())->not->toBe(404);
    });

    it('webhook rejects invalid signature', function () {
        $payload = json_encode([
            'type' => 'invoice.payment_succeeded',
            'data' => [
                'object' => [
                    'id' => 'in_test123',
                    'customer' => 'cus_test123',
                ],
            ],
        ]);

        $response = $this->post('/stripe/webhook', [], [
            'Content-Type' => 'application/json',
            'Stripe-Signature' => 'invalid_signature',
        ]);

        // Should be rejected due to invalid signature
        expect($response->status())->toBeIn([400, 401, 403]);
    });

    it('can find billable entities by Stripe customer ID', function () {
        // Create a user with Stripe ID
        $user = User::factory()->create([
            'stripe_id' => 'cus_user123',
        ]);

        // Create an organization with Stripe ID
        $organization = Organization::factory()->create([
            'stripe_id' => 'cus_org123',
        ]);

        // Test user lookup
        $foundUser = User::where('stripe_id', 'cus_user123')->first();
        expect($foundUser)->not->toBeNull();
        expect($foundUser->id)->toBe($user->id);

        // Test organization lookup
        $foundOrg = Organization::where('stripe_id', 'cus_org123')->first();
        expect($foundOrg)->not->toBeNull();
        expect($foundOrg->id)->toBe($organization->id);
    });

    it('webhook route is properly configured', function () {
        $routes = app('router')->getRoutes();
        $webhookRoute = null;

        foreach ($routes as $route) {
            if ($route->uri() === 'stripe/webhook' && in_array('POST', $route->methods())) {
                $webhookRoute = $route;
                break;
            }
        }

        expect($webhookRoute)->not->toBeNull();
        expect($route->getActionName())->toBe('App\Http\Controllers\Stripe\WebhookController@handleWebhook');
    });

    it('has proper cashier configuration', function () {
        // Check that webhook events are configured
        $events = config('cashier.webhook.events');
        expect($events)->toBeArray();
        expect($events)->not->toBeEmpty();

        // Check that essential events are included
        $essentialEvents = [
            'customer.subscription.created',
            'customer.subscription.updated',
            'invoice.payment_succeeded',
            'invoice.payment_failed',
        ];

        foreach ($essentialEvents as $event) {
            expect($events)->toContain($event);
        }

        // Check webhook tolerance is set
        $tolerance = config('cashier.webhook.tolerance');
        expect($tolerance)->toBeNumeric();
        expect($tolerance)->toBeGreaterThan(0);
    });
});
