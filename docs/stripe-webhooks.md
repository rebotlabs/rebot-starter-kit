# Stripe Webhooks Setup Guide

This guide explains how to set up Stripe webhooks for Laravel Cashier in your application.

## Overview

Webhooks allow Stripe to notify your application when events happen in your Stripe account, such as:
- Successful payments
- Failed payment attempts
- Subscription changes
- Customer updates
- Invoice events

## Quick Setup

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
# Stripe Configuration
STRIPE_KEY=pk_test_your_stripe_publishable_key_here
STRIPE_SECRET=sk_test_your_stripe_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
STRIPE_WEBHOOK_TOLERANCE=300

# Cashier Configuration
CASHIER_CURRENCY=usd
CASHIER_CURRENCY_LOCALE=en
```

### 2. Automatic Webhook Setup

Use the provided Artisan command to automatically create webhook endpoints in Stripe:

```bash
php artisan stripe:setup-webhooks
```

Or specify a custom URL:

```bash
php artisan stripe:setup-webhooks --url=https://yourdomain.com
```

This command will:
- Create a webhook endpoint in your Stripe dashboard
- Configure it with the required events
- Display the webhook secret for your `.env` file

### 3. Manual Setup (Alternative)

If you prefer to set up webhooks manually:

1. Go to your [Stripe Dashboard](https://dashboard.stripe.com/webhooks)
2. Click "Add endpoint"
3. Set the endpoint URL to: `https://yourdomain.com/stripe/webhook`
4. Select the following events:
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `customer.subscription.trial_will_end`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `invoice.upcoming`
   - `payment_method.attached`
   - `payment_method.detached`
   - `setup_intent.succeeded`
   - And others as configured in `config/cashier.php`

5. Copy the webhook secret and add it to your `.env` file as `STRIPE_WEBHOOK_SECRET`

## Webhook Events Handled

The application handles the following webhook events:

### Customer Events
- `customer.subscription.created` - New subscription created
- `customer.subscription.updated` - Subscription modified
- `customer.subscription.deleted` - Subscription cancelled
- `customer.subscription.trial_will_end` - Trial ending soon

### Invoice Events
- `invoice.payment_succeeded` - Payment successful
- `invoice.payment_failed` - Payment failed
- `invoice.upcoming` - Invoice will be created soon
- `invoice.created` - New invoice created
- `invoice.finalized` - Invoice finalized

### Payment Events
- `payment_method.attached` - Payment method added
- `payment_method.detached` - Payment method removed
- `payment_intent.succeeded` - Payment successful
- `payment_intent.payment_failed` - Payment failed

### Setup Events
- `setup_intent.succeeded` - Setup completed successfully
- `setup_intent.setup_failed` - Setup failed

## Webhook Controller

The webhook controller (`App\Http\Controllers\Stripe\WebhookController`) extends Laravel Cashier's base webhook controller and provides custom handling for specific events.

### Custom Event Handlers

You can add custom logic for specific events by adding methods to the webhook controller:

```php
public function handleInvoicePaymentSucceeded(array $payload): Response
{
    $invoice = $payload['data']['object'];
    
    // Custom logic here
    // - Send confirmation emails
    // - Update internal records
    // - Trigger business processes
    
    return new Response('Webhook handled', 200);
}
```

### Event Method Naming

Event methods follow the pattern: `handle{EventName}` where the event name is converted to StudlyCase:
- `invoice.payment_succeeded` → `handleInvoicePaymentSucceeded`
- `customer.subscription.created` → `handleCustomerSubscriptionCreated`

## Security

### Webhook Signature Verification

The application automatically verifies webhook signatures using the `VerifyStripeWebhookSignature` middleware to ensure requests are genuinely from Stripe.

### CSRF Protection

Webhook routes are excluded from CSRF protection as they come from external sources.

## Testing Webhooks

### Local Development

For local development, you can use:

1. **Stripe CLI** (Recommended):
   ```bash
   stripe listen --forward-to localhost:8000/stripe/webhook
   ```

2. **ngrok**:
   ```bash
   ngrok http 8000
   # Use the HTTPS URL for your webhook endpoint
   ```

### Webhook Testing in Stripe Dashboard

You can test webhooks directly from the Stripe Dashboard:
1. Go to your webhook endpoint
2. Click "Send test webhook"
3. Select an event to test
4. Review the response

## Monitoring and Debugging

### Logs

Webhook events are logged for debugging purposes. Check your application logs:

```bash
tail -f storage/logs/laravel.log
```

### Failed Webhooks

Stripe will retry failed webhooks. You can see retry attempts in your Stripe Dashboard under the specific webhook endpoint.

### Webhook History

View all webhook attempts in your Stripe Dashboard to debug issues.

## Troubleshooting

### Common Issues

1. **404 on webhook URL**
   - Ensure the route is properly registered
   - Check your `APP_URL` configuration

2. **401/403 errors**
   - Verify the webhook secret is correctly set
   - Ensure the middleware is properly applied

3. **Events not being processed**
   - Check that the events are enabled in your webhook configuration
   - Verify the event handler methods exist

4. **Timeout issues**
   - Ensure webhook handlers complete quickly
   - Move heavy processing to queued jobs

### Debugging Steps

1. Check webhook endpoint status in Stripe Dashboard
2. Review application logs for errors
3. Verify environment variables are set correctly
4. Test webhook signature verification
5. Ensure database connectivity for billable entities

## Best Practices

1. **Keep handlers fast** - Webhook handlers should complete quickly
2. **Use queues** - For heavy processing, dispatch queued jobs
3. **Be idempotent** - Handle duplicate webhook calls gracefully
4. **Log events** - Keep detailed logs for debugging
5. **Monitor failures** - Set up alerts for webhook failures
6. **Test thoroughly** - Test all webhook scenarios in staging

## Production Considerations

1. **SSL Certificate** - Ensure your webhook URL uses HTTPS
2. **Rate Limiting** - Consider rate limiting for webhook endpoints
3. **Monitoring** - Set up monitoring for webhook failures
4. **Backup Processing** - Have backup processes for critical events
5. **Documentation** - Keep webhook logic well documented

## Support

For issues with Stripe webhooks:
1. Check Stripe's webhook documentation
2. Review Laravel Cashier documentation
3. Check the webhook logs in Stripe Dashboard
4. Review application logs for errors
