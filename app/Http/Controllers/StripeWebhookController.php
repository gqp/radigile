<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends CashierWebhookController
{
    protected function handleCustomerSubscriptionCreated(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionCreated($payload);
        $this->syncToAppSubscription($payload['data']['object']);
        return $response;
    }

    protected function handleCustomerSubscriptionUpdated(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionUpdated($payload);
        $this->syncToAppSubscription($payload['data']['object']);
        return $response;
    }

    protected function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);
        $stripeSubscription = $payload['data']['object'];

        $user = User::where('stripe_id', $stripeSubscription['customer'])->first();

        if ($user) {
            Subscription::where('user_id', $user->id)->update(['is_active' => false]);
        }

        return $response;
    }

    protected function handleInvoicePaymentFailed(array $payload): Response
    {
        $invoice = $payload['data']['object'];
        $user = User::where('stripe_id', $invoice['customer'])->first();

        if ($user) {
            Subscription::where('user_id', $user->id)->update(['is_active' => false]);
        }

        return new Response('Webhook handled.', 200);
    }

    private function syncToAppSubscription(array $stripeSubscription): void
    {
        $user = User::where('stripe_id', $stripeSubscription['customer'])->first();

        if (!$user) {
            return;
        }

        $stripePriceId = $stripeSubscription['items']['data'][0]['price']['id'] ?? null;
        $plan = $stripePriceId ? Plan::where('stripe_price_id', $stripePriceId)->first() : null;

        if (!$plan) {
            return;
        }

        $isActive = in_array($stripeSubscription['status'], ['active', 'trialing']);
        $endsAt = isset($stripeSubscription['current_period_end'])
            ? \Carbon\Carbon::createFromTimestamp($stripeSubscription['current_period_end'])
            : null;

        $existing = Subscription::where('user_id', $user->id)->first();

        if ($existing) {
            $existing->update([
                'plan_id' => $plan->id,
                'is_active' => $isActive,
                'ends_at' => $endsAt,
            ]);
        } else {
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => $endsAt,
                'is_active' => $isActive,
            ]);
        }
    }
}
