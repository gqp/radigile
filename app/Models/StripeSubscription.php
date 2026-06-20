<?php

namespace App\Models;

use Laravel\Cashier\Subscription as CashierSubscription;

class StripeSubscription extends CashierSubscription
{
    protected $table = 'stripe_subscriptions';
}
