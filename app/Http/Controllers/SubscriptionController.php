<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class SubscriptionController extends Controller
{
    /**
     * Show all plans for the admin to manage.
     */
    public function indexPlans(): \Illuminate\View\View
    {
        $plans = Plan::all();
        return view('dashboard.admin.subscriptions.plans.index', compact('plans'));
    }

    /**
     * Show all subscriptions for the admin to manage.
     */
    public function indexSubscriptions(): \Illuminate\View\View
    {
        $subscriptions = Subscription::with(['user', 'plan'])->get();
        return view('dashboard.admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form to create a new plan.
     */
    public function createPlan(): \Illuminate\View\View
    {
        // Fetch all available roles
        $roles = Role::all();

        return view('dashboard.admin.subscriptions.plans.create', compact('roles'));

    }

    public function editPlan(Plan $plan): \Illuminate\View\View
    {
        return view('dashboard.admin.subscriptions.plans.edit', compact('plan'));
    }


    /**
     * Store a new plan in the database.
     */
    public function storePlan(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|string|in:free,monthly,yearly,lifetime',
            'is_active' => 'required|boolean',
            'stripe_price_id' => 'nullable|string|max:255',
        ]);

        Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('message', 'Plan created successfully!');
    }

    public function updatePlan(Request $request, Plan $plan): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|string|max:20',
            'is_active' => 'required|boolean',
            'stripe_price_id' => 'nullable|string|max:255',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('message', 'Plan updated successfully!');
    }

    public function destroyPlan(Plan $plan): \Illuminate\Http\RedirectResponse
    {
        // Check if the plan has associated subscriptions
        if ($plan->subscriptions()->exists()) {
            return redirect()->route('admin.plans.index')
                ->with('error', 'Cannot delete plan. Subscriptions are associated with it.');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('message', 'Plan deleted successfully!');
    }

    public function editSubscription(Subscription $subscription): \Illuminate\View\View
    {
        $users = User::all(); // Fetch all users
        $plans = Plan::all(); // Fetch all plans
        return view('dashboard.admin.subscriptions.edit', compact('subscription', 'plans','users'));
    }

    /**
     * Show the form to create a new subscription.
     */
    public function createSubscription(): \Illuminate\View\View
    {
        $users = User::all(); // Fetch all users
        $plans = Plan::all(); // Fetch all plans
        return view('dashboard.admin.subscriptions.create', compact('users', 'plans'));
    }

    /**
     * Store a new subscription in the database.
     */
    public function storeSubscription(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'plan_id'   => 'required|exists:plans,id',
            'starts_at' => 'required|date',
            'ends_at'   => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'required|boolean',
        ]);

        Subscription::create($validated);

        return redirect()->route('admin.subscriptions.index')->with('message', 'Subscription created successfully!');
    }

    public function updateSubscription(Request $request, Subscription $subscription): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'plan_id'   => 'required|exists:plans,id',
            'starts_at' => 'required|date',
            'ends_at'   => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'required|boolean',
        ]);

        $subscription->update($validated);

        return redirect()->route('admin.subscriptions.index')->with('message', 'Subscription updated successfully!');
    }

    public function destroySubscription(Subscription $subscription): \Illuminate\Http\RedirectResponse
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('message', 'Subscription deleted successfully!');
    }

    public function subscribeToFreePlan(): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        if ($user->activeSubscription()) {
            return redirect()->route('user.dashboard')
                ->with('info', 'You already have an active subscription.');
        }

        $freePlan = Plan::where('interval', 'free')->where('is_active', true)->first();

        if (!$freePlan) {
            return redirect()->route('user.dashboard')
                ->with('error', 'The free plan is not available at this time.');
        }

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
            'starts_at' => now(),
            'ends_at' => null,
            'is_active' => true,
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', 'You are now on the free plan.');
    }

    public function accessFeature(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription();

        if (!$subscription) {
            return response()->json(['access' => false, 'message' => 'No active subscription.'], 403);
        }

        return response()->json(['access' => true, 'plan' => $subscription->plan->name]);
    }

    public function checkFreeTier(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        return response()->json(['on_free_tier' => $user->onFreeTier()]);
    }

}
