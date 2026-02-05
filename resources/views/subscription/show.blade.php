@extends('admin.layout-new')

@section('title', 'My Subscription')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">My Subscription</h2>
        <a href="{{ route('subscription.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Plans
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <!-- Subscription Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Subscription Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Plan Name</label>
                            <h5>{{ $subscription->plan->name }}</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status</label>
                            <h5>
                                @if($subscription->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($subscription->status === 'canceled')
                                    <span class="badge bg-warning">Canceled</span>
                                @elseif($subscription->status === 'expired')
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($subscription->status) }}</span>
                                @endif
                            </h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Started On</label>
                            <h5>{{ $subscription->started_at ? $subscription->started_at->format('d M Y') : 'N/A' }}</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Expires On</label>
                            <h5>{{ $subscription->expires_at ? $subscription->expires_at->format('d M Y') : 'N/A' }}</h5>
                        </div>
                        @if($subscription->payment_method)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Payment Method</label>
                            <h5>{{ ucfirst($subscription->payment_method) }}</h5>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Days Remaining</label>
                            <h5 class="{{ $subscription->daysRemaining() <= 7 ? 'text-danger' : 'text-success' }}">
                                {{ $subscription->daysRemaining() }} days
                            </h5>
                        </div>
                    </div>

                    @if($subscription->status === 'active')
                        <hr>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle me-1"></i> Cancel Subscription
                        </button>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Payment History</h5>
                </div>
                <div class="card-body p-0">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Transaction ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                            <td>{{ $payment->formattedAmount() }}</td>
                                            <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                                            <td>
                                                @if($payment->status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($payment->status === 'pending')
                                                    @if($payment->expires_at && $payment->expires_at->isFuture())
                                                        <span class="badge bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-danger">Expired</span>
                                                    @endif
                                                @elseif($payment->status === 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                @elseif($payment->status === 'expired')
                                                    <span class="badge bg-secondary">Expired</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <code class="small text-truncate" style="max-width: 120px;">{{ $payment->transaction_id ? substr($payment->transaction_id, 0, 15) . '...' : 'N/A' }}</code>
                                                    @if(($payment->status === 'pending' && $payment->expires_at && $payment->expires_at->isFuture()) || $payment->status === 'failed' || $payment->status === 'expired')
                                                        @if($payment->checkout_url)
                                                            <a href="{{ $payment->checkout_url }}" target="_blank" class="btn btn-sm btn-primary" title="Retry Payment">
                                                                <i class="bi bi-arrow-repeat"></i> Retry
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt display-1 text-muted"></i>
                            <p class="text-muted mt-3">No payment history available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Plan Features -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Plan Features</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($subscription->plan->features as $feature)
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                {{ $feature }}
                            </li>
                        @endforeach
                        
                        @if($subscription->plan->max_accounts)
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Up to {{ $subscription->plan->max_accounts }} WhatsApp accounts
                            </li>
                        @endif
                        
                        @if($subscription->plan->max_users)
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Up to {{ $subscription->plan->max_users }} team members
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Subscription Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('subscription.cancel') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel your subscription?</p>
                    <p class="text-muted small">You can continue using the service until {{ $subscription->expires_at->format('d M Y') }}.</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Reason for cancellation (optional)</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Tell us why you're canceling..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Subscription</button>
                    <button type="submit" class="btn btn-danger">Yes, Cancel Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
