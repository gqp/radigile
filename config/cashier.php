<?php

return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],
    'model' => App\Models\User::class,
    'subscription_model' => App\Models\StripeSubscription::class,
    'subscription_item_model' => Laravel\Cashier\SubscriptionItem::class,
    'currency' => env('CASHIER_CURRENCY', 'usd'),
    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),
    'payment_model' => Laravel\Cashier\Payment::class,
    'logger' => null,
    'payment_notification' => null,
    'payment_urls' => [
        'confirmation' => '/stripe/payment/{payment}',
        'success' => env('STRIPE_PAYMENT_SUCCESS_URL', '/user/dashboard'),
    ],
];
