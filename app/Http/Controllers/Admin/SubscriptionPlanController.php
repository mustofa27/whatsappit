<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::ordered()->get();
        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'required|array|min:1',
            'features.*' => 'required|string',
            'max_accounts' => 'nullable|integer|min:1',
            'max_users' => 'nullable|integer|min:1',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Filter empty features
        $validated['features'] = array_filter($validated['features']);

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'required|array|min:1',
            'features.*' => 'required|string',
            'max_accounts' => 'nullable|integer|min:1',
            'max_users' => 'nullable|integer|min:1',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Filter empty features
        $validated['features'] = array_filter($validated['features']);

        // If marking this plan as popular, unmark others
        if ($request->boolean('is_popular')) {
            SubscriptionPlan::where('id', '!=', $subscriptionPlan->id)
                ->update(['is_popular' => false]);
        }

        $subscriptionPlan->update($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    public function toggleStatus(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update(['is_active' => !$subscriptionPlan->is_active]);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Plan status updated successfully.');
    }
}
