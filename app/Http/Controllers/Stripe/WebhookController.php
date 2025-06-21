<?php

namespace App\Http\Controllers\Stripe;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class WebhookController extends CashierWebhookController
{
    /**
     * Handle invoice payment succeeded.
     */
    public function handleInvoicePaymentSucceeded(array $payload): Response
    {
        // Handle successful payment
        $invoice = $payload['data']['object'];

        if ($customerId = $invoice['customer'] ?? null) {
            $billable = $this->getBillableEntity($customerId);

            if ($billable) {
                // You can add custom logic here, such as:
                // - Sending confirmation emails
                // - Updating internal billing records
                // - Triggering business logic

                \Log::info('Invoice payment succeeded', [
                    'customer_id' => $customerId,
                    'invoice_id' => $invoice['id'],
                    'amount_paid' => $invoice['amount_paid'],
                ]);
            }
        }

        return new Response('Webhook handled', 200);
    }

    /**
     * Handle invoice payment failed.
     */
    public function handleInvoicePaymentFailed(array $payload): Response
    {
        $invoice = $payload['data']['object'];

        if ($customerId = $invoice['customer'] ?? null) {
            $billable = $this->getBillableEntity($customerId);

            if ($billable) {
                // Handle payment failure
                // You might want to:
                // - Send notification emails
                // - Update subscription status
                // - Trigger retry logic

                \Log::warning('Invoice payment failed', [
                    'customer_id' => $customerId,
                    'invoice_id' => $invoice['id'],
                    'amount_due' => $invoice['amount_due'],
                    'attempt_count' => $invoice['attempt_count'],
                ]);
            }
        }

        return new Response('Webhook handled', 200);
    }

    /**
     * Handle customer subscription created.
     */
    public function handleCustomerSubscriptionCreated(array $payload): Response
    {
        $subscription = $payload['data']['object'];

        if ($customerId = $subscription['customer'] ?? null) {
            $billable = $this->getBillableEntity($customerId);

            if ($billable) {
                \Log::info('Subscription created', [
                    'customer_id' => $customerId,
                    'subscription_id' => $subscription['id'],
                    'status' => $subscription['status'],
                ]);
            }
        }

        return new Response('Webhook handled', 200);
    }

    /**
     * Handle customer subscription updated.
     */
    public function handleCustomerSubscriptionUpdated(array $payload): Response
    {
        $subscription = $payload['data']['object'];

        if ($customerId = $subscription['customer'] ?? null) {
            $billable = $this->getBillableEntity($customerId);

            if ($billable) {
                \Log::info('Subscription updated', [
                    'customer_id' => $customerId,
                    'subscription_id' => $subscription['id'],
                    'status' => $subscription['status'],
                ]);
            }
        }

        return new Response('Webhook handled', 200);
    }

    /**
     * Handle customer subscription deleted.
     */
    public function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $subscription = $payload['data']['object'];

        if ($customerId = $subscription['customer'] ?? null) {
            $billable = $this->getBillableEntity($customerId);

            if ($billable) {
                \Log::info('Subscription deleted', [
                    'customer_id' => $customerId,
                    'subscription_id' => $subscription['id'],
                ]);
            }
        }

        return new Response('Webhook handled', 200);
    }

    /**
     * Handle payment method attached.
     */
    public function handlePaymentMethodAttached(array $payload): Response
    {
        $paymentMethod = $payload['data']['object'];

        if ($customerId = $paymentMethod['customer'] ?? null) {
            $billable = $this->getBillableEntity($customerId);

            if ($billable) {
                \Log::info('Payment method attached', [
                    'customer_id' => $customerId,
                    'payment_method_id' => $paymentMethod['id'],
                    'type' => $paymentMethod['type'],
                ]);
            }
        }

        return new Response('Webhook handled', 200);
    }

    /**
     * Get the billable entity (User or Organization) by Stripe customer ID.
     */
    protected function getBillableEntity(string $customerId)
    {
        // Check if it's an organization
        $organization = Organization::where('stripe_id', $customerId)->first();
        if ($organization) {
            return $organization;
        }

        // Check if it's a user
        $user = User::where('stripe_id', $customerId)->first();
        if ($user) {
            return $user;
        }

        return null;
    }
}
