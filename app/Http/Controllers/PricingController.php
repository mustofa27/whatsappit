<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(): View
    {
        // Get active plans from database
        $plans = SubscriptionPlan::active()->ordered()->get();

        $metaPricing = [
            'marketing' => 1200,
            'utility' => 700,
            'service' => 500,
            'free_tier' => 1000,
        ];

        return view('pricing.index', compact('plans', 'metaPricing'));
    }

    public function calculator(Request $request)
    {
        $conversations = (int) $request->input('conversations', 0);
        $category = $request->input('category', 'utility');

        $metaPricing = [
            'marketing' => 1200,
            'utility' => 700,
            'service' => 500,
        ];

        $freeTier = 1000;
        $billableConversations = max(0, $conversations - $freeTier);
        $costPerConversation = $metaPricing[$category] ?? 700;
        $metaCost = $billableConversations * $costPerConversation;

        return response()->json([
            'conversations' => $conversations,
            'free_conversations' => min($conversations, $freeTier),
            'billable_conversations' => $billableConversations,
            'cost_per_conversation' => $costPerConversation,
            'meta_cost' => $metaCost,
            'meta_cost_formatted' => 'Rp ' . number_format($metaCost, 0, ',', '.'),
        ]);
    }
}
