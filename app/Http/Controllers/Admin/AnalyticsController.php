<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $accounts = WhatsappAccount::where('user_id', $userId)->get();
        $accountId = $request->input('account_id');

        $start = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->subDays(6)->startOfDay();

        $end = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $messagesQuery = WhatsappMessage::whereBetween('created_at', [$start, $end])
            ->whereHas('whatsappAccount', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        if ($accountId) {
            $messagesQuery->where('whatsapp_account_id', $accountId);
        }

        $incomingTotal = (clone $messagesQuery)->where('direction', 'incoming')->count();
        $outgoingTotal = (clone $messagesQuery)->where('direction', 'outgoing')->count();
        $deliveredTotal = (clone $messagesQuery)
            ->where('direction', 'outgoing')
            ->whereIn('status', ['delivered', 'read'])
            ->count();
        $failedTotal = (clone $messagesQuery)
            ->where('direction', 'outgoing')
            ->where('status', 'failed')
            ->count();
        $pendingTotal = (clone $messagesQuery)
            ->where('direction', 'outgoing')
            ->where('status', 'pending')
            ->count();

        $deliveryRate = $outgoingTotal > 0
            ? round(($deliveredTotal / $outgoingTotal) * 100, 1)
            : 0;

        $responseStats = $this->calculateResponseTime($messagesQuery);

        $costPerMessage = Setting::get('cost_per_message', 500);
        $estimatedCost = $outgoingTotal * $costPerMessage;

        $dailyCounts = (clone $messagesQuery)
            ->selectRaw("DATE(created_at) as date,
                SUM(CASE WHEN direction = 'outgoing' THEN 1 ELSE 0 END) as outgoing,
                SUM(CASE WHEN direction = 'incoming' THEN 1 ELSE 0 END) as incoming")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $outgoingData = [];
        $incomingData = [];

        $period = CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay());
        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $outgoingData[] = (int) ($dailyCounts->get($key)->outgoing ?? 0);
            $incomingData[] = (int) ($dailyCounts->get($key)->incoming ?? 0);
        }

        return view('admin.analytics.index', [
            'accounts' => $accounts,
            'accountId' => $accountId,
            'start' => $start,
            'end' => $end,
            'incomingTotal' => $incomingTotal,
            'outgoingTotal' => $outgoingTotal,
            'deliveredTotal' => $deliveredTotal,
            'failedTotal' => $failedTotal,
            'pendingTotal' => $pendingTotal,
            'deliveryRate' => $deliveryRate,
            'avgResponseTime' => $responseStats['avg_human'],
            'responseSamples' => $responseStats['samples'],
            'estimatedCost' => $estimatedCost,
            'costPerMessage' => $costPerMessage,
            'labels' => $labels,
            'outgoingData' => $outgoingData,
            'incomingData' => $incomingData,
        ]);
    }

    private function calculateResponseTime($messagesQuery): array
    {
        $messages = (clone $messagesQuery)
            ->whereIn('direction', ['incoming', 'outgoing'])
            ->orderBy('contact_number')
            ->orderBy('created_at')
            ->get(['contact_number', 'direction', 'created_at']);

        $lastIncoming = [];
        $totalSeconds = 0;
        $samples = 0;

        foreach ($messages as $message) {
            $contact = $message->contact_number;

            if ($message->direction === 'incoming') {
                $lastIncoming[$contact] = $message->created_at;
                continue;
            }

            if ($message->direction === 'outgoing' && isset($lastIncoming[$contact])) {
                $diff = $message->created_at->diffInSeconds($lastIncoming[$contact]);
                if ($diff >= 0) {
                    $totalSeconds += $diff;
                    $samples++;
                }
                unset($lastIncoming[$contact]);
            }
        }

        $avgSeconds = $samples > 0 ? (int) round($totalSeconds / $samples) : 0;

        return [
            'avg_seconds' => $avgSeconds,
            'avg_human' => $this->formatDuration($avgSeconds),
            'samples' => $samples,
        ];
    }

    private function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0 sec';
        }

        if ($seconds < 60) {
            return $seconds . ' sec';
        }

        if ($seconds < 3600) {
            return round($seconds / 60, 1) . ' min';
        }

        return round($seconds / 3600, 1) . ' hr';
    }
}
