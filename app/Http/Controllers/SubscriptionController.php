<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\PaypoolService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class SubscriptionController extends BaseController
{
    protected PaypoolService $paypool;

    public function __construct(PaypoolService $paypool)
    {
        $this->middleware('auth');
        $this->paypool = $paypool;
    }

    /**
     * Show subscription plans for checkout
     */
    public function index()
    {
        $user = auth()->user();
        $currentSubscription = $user->activeSubscription;
        $plans = SubscriptionPlan::active()->ordered()->get();

        return view('subscription.index', compact('plans', 'currentSubscription'));
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $user = auth()->user();

        // Check if user already has active subscription
        if ($user->hasActiveSubscription()) {
            return redirect()->route('subscription.index')
                ->with('error', 'You already have an active subscription.');
        }

        // Create subscription record
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'pending',
        ]);

        // Create payment via Paypool
        $result = $this->paypool->createPayment($subscription, [
            'success_url' => route('subscription.success'),
            'failure_url' => route('subscription.failed'),
        ]);

        if ($result['success']) {
            return redirect()->away($result['invoice_url']);
        }

        $subscription->delete();

        return redirect()->route('subscription.index')
            ->with('error', 'Failed to create payment. Please try again.');
    }

    /**
     * Payment success callback
     */
    public function success(Request $request)
    {
        return view('subscription.success');
    }

    /**
     * Payment failed callback
     */
    public function failed(Request $request)
    {
        return view('subscription.failed');
    }

    /**
     * Show user's subscription details
     */
    public function show()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return redirect()->route('subscription.index')
                ->with('info', 'You do not have an active subscription.');
        }

        $payments = $subscription->payments()
            ->latest()
            ->paginate(10);

        return view('subscription.show', compact('subscription', 'payments'));
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return redirect()->route('subscription.index')
                ->with('error', 'No active subscription found.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'cancel_reason' => $request->input('reason'),
        ]);

        return redirect()->route('subscription.show')
            ->with('success', 'Subscription canceled successfully. You can use it until ' . $subscription->expires_at->format('d M Y'));
    }
}
