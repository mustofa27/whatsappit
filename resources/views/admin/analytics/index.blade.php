@extends('admin.layout-new')

@section('title', 'Analytics')
@section('page-title', 'Analytics & Reporting')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Analytics & Reporting</h2>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.analytics.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Account</label>
                    <select class="form-select" name="account_id">
                        <option value="">All</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ (string)$accountId === (string)$acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $start->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $end->format('Y-m-d') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-1"></i> Apply
                    </button>
                    <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Outgoing Messages</h6>
                            <h3 class="mb-0">{{ $outgoingTotal }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-send fs-1"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">Delivered: {{ $deliveredTotal }} | Failed: {{ $failedTotal }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Incoming Messages</h6>
                            <h3 class="mb-0">{{ $incomingTotal }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-inbox fs-1"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">Pending: {{ $pendingTotal }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Delivery Rate</h6>
                            <h3 class="mb-0">{{ $deliveryRate }}%</h3>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-graph-up fs-1"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">Delivered + Read / Outgoing</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Avg Response Time</h6>
                            <h3 class="mb-0">{{ $avgResponseTime }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-clock-history fs-1"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">{{ $responseSamples }} samples</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Messages Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="messagesChart" height="110"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Estimated Cost</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Outgoing x Cost</h6>
                            <h3 class="mb-0">Rp {{ number_format($estimatedCost, 0, ',', '.') }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-cash-stack fs-1"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">Rp {{ number_format($costPerMessage, 0, ',', '.') }} per outgoing message (estimate)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const labels = @json($labels);
    const outgoingData = @json($outgoingData);
    const incomingData = @json($incomingData);

    const ctx = document.getElementById('messagesChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Outgoing',
                    data: outgoingData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.15)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Incoming',
                    data: incomingData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.15)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endsection
