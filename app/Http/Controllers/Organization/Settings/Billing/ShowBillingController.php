<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\Billing;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Inertia\Inertia;
use Inertia\Response;

class ShowBillingController extends Controller
{
    public function __invoke(Organization $organization): Response
    {
        $subscription = null;
        $paymentMethods = collect();
        $defaultPaymentMethod = null;
        $invoices = collect();
        $billingPortalUrl = null;

        try {
            $subscription = $organization->subscription('default');
            $paymentMethods = $organization->paymentMethods();
            $defaultPaymentMethod = $organization->defaultPaymentMethod();

            if ($organization->hasStripeId()) {
                $invoices = collect($organization->invoices(true, [
                    'limit' => 10,
                ]));
                $billingPortalUrl = $organization->billingPortalUrl(
                    route('organization.settings.billing', $organization)
                );
            }
        } catch (\Exception $e) {
            // If there are any Stripe API issues, we'll just show empty billing data
            // This allows the page to load even if Stripe is not configured or has issues
        }

        return Inertia::render('organization/settings/billing', [
            'subscription' => $subscription ? [
                'name' => $subscription->name,
                'stripe_status' => $subscription->stripe_status,
                'stripe_price' => $subscription->stripe_price,
                'quantity' => $subscription->quantity,
                'trial_ends_at' => $subscription->trial_ends_at?->toISOString(),
                'ends_at' => $subscription->ends_at?->toISOString(),
                'created_at' => $subscription->created_at->toISOString(),
                'on_trial' => $subscription->onTrial(),
                'canceled' => $subscription->canceled(),
                'on_grace_period' => $subscription->onGracePeriod(),
                'recurring' => $subscription->recurring(),
            ] : null,
            'paymentMethods' => $paymentMethods->map(function ($paymentMethod) {
                return [
                    'id' => $paymentMethod->id,
                    'type' => $paymentMethod->type,
                    'card' => $paymentMethod->card ? [
                        'brand' => $paymentMethod->card->brand,
                        'last4' => $paymentMethod->card->last4,
                        'exp_month' => $paymentMethod->card->exp_month,
                        'exp_year' => $paymentMethod->card->exp_year,
                    ] : null,
                ];
            }),
            'defaultPaymentMethod' => $defaultPaymentMethod ? [
                'id' => $defaultPaymentMethod->id,
                'type' => $defaultPaymentMethod->type,
                'card' => $defaultPaymentMethod->card ? [
                    'brand' => $defaultPaymentMethod->card->brand,
                    'last4' => $defaultPaymentMethod->card->last4,
                    'exp_month' => $defaultPaymentMethod->card->exp_month,
                    'exp_year' => $defaultPaymentMethod->card->exp_year,
                ] : null,
            ] : null,
            'invoices' => $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'date' => $invoice->date()->toISOString(),
                    'total' => $invoice->total(),
                    'hosted_invoice_url' => $invoice->hosted_invoice_url,
                    'invoice_pdf' => $invoice->invoice_pdf,
                ];
            }),
            'billingPortalUrl' => $billingPortalUrl,
        ]);
    }
}
