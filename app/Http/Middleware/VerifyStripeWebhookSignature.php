<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature as CashierVerifyWebhookSignature;

class VerifyStripeWebhookSignature extends CashierVerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        return parent::handle($request, $next);
    }
}
