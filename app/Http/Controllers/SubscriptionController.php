<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

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
        return view('dashboard.admin.subscriptions.plans.create');
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
            'interval' => 'required|string|max:20', // Examples: free, monthly, yearly
            'is_active' => 'required|boolean',
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
            'interval' => 'required|string|max:20', // Examples: free, monthly, yearly
            'is_active' => 'required|boolean',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('message', 'Plan updated successfully!');
    }

}
