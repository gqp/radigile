<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription();

        $availablePlans = Plan::where('is_active', true)
            ->where('interval', '!=', 'free')
            ->orderBy('price')
            ->get();

        return view('dashboard.user.billing.index', compact('subscription', 'availablePlans'));
    }

    public function checkout(Plan $plan): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        if ($plan->interval === 'free' || !$plan->stripe_price_id) {
            return redirect()->route('user.dashboard')->with('error', 'Please use the free plan sign-up instead.');
        }

        if (!$plan->is_active) {
            return redirect()->route('user.dashboard')->with('error', 'This plan is not currently available.');
        }

        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        $session = $user->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.cancel'),
                'metadata' => ['plan_id' => $plan->id],
            ]);

        return redirect($session->url);
    }

    public function success(Request $request): \Illuminate\View\View
    {
        return view('dashboard.user.billing.success');
    }

    public function cancel(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('user.dashboard')->with('info', 'Checkout was cancelled. You can upgrade anytime.');
    }

    public function portal(): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        if (!$user->stripe_id) {
            return redirect()->route('user.dashboard')->with('error', 'No billing account found.');
        }

        return $user->redirectToBillingPortal(route('user.dashboard'));
    }
}
