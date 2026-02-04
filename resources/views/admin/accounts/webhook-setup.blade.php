@extends('admin.layout-new')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <div class="d-flex align-items-center mb-2">
            <a href="{{ route('admin.accounts.index') }}" class="btn btn-link p-0 me-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0">Webhook Setup - {{ $account->account_name }}</h1>
        </div>
        <p class="text-muted">Configure webhook in Meta Business Manager to receive incoming messages</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Webhook Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Your Webhook Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Webhook URL</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="webhookUrl" value="{{ $webhookUrl }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('webhookUrl')">
                                <i class="bi bi-copy"></i> Copy
                            </button>
                        </div>
                        <small class="text-muted">This is the endpoint where Meta will send incoming messages and status updates</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Verify Token</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="verifyToken" value="{{ $verifyToken }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('verifyToken')">
                                <i class="bi bi-copy"></i> Copy
                            </button>
                        </div>
                        <small class="text-muted">This token uniquely identifies this WhatsApp account. Meta will send it with every webhook request for verification</small>
                    </div>

                    <div class="mb-0">
                        <form action="{{ route('admin.accounts.webhook-regenerate', $account) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning" onclick="return confirm('This will invalidate the current token. Make sure to update Meta Business Manager with the new token.')">
                                <i class="bi bi-arrow-repeat me-1"></i> Regenerate Token
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Step-by-Step Setup -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Setup Steps in Meta Business Manager</h5>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">
                            <strong>Go to Meta Business Manager</strong>
                            <p class="text-muted mb-2">Visit <a href="https://business.facebook.com/" target="_blank">business.facebook.com</a> and log in</p>
                        </li>
                        
                        <li class="list-group-item">
                            <strong>Select Your App</strong>
                            <p class="text-muted mb-2">Go to Apps → Your App → Settings → Basic</p>
                        </li>

                        <li class="list-group-item">
                            <strong>Configure WhatsApp Product</strong>
                            <p class="text-muted mb-2">In the left menu, find "WhatsApp" and click on it</p>
                        </li>

                        <li class="list-group-item">
                            <strong>Go to Configuration</strong>
                            <p class="text-muted mb-2">In WhatsApp settings, look for "Configuration" or "Webhooks" section</p>
                        </li>

                        <li class="list-group-item">
                            <strong>Set Webhook URL</strong>
                            <p class="text-muted mb-2">
                                Click "Edit" and paste this URL:
                                <br>
                                <code class="bg-light p-2 d-block mt-2">{{ $webhookUrl }}</code>
                            </p>
                        </li>

                        <li class="list-group-item">
                            <strong>Set Verify Token</strong>
                            <p class="text-muted mb-2">
                                In the same configuration, paste this verify token:
                                <br>
                                <code class="bg-light p-2 d-block mt-2">{{ $verifyToken }}</code>
                            </p>
                        </li>

                        <li class="list-group-item">
                            <strong>Subscribe to Webhook Events</strong>
                            <p class="text-muted mb-2">Select which events you want to receive:</p>
                            <ul class="mb-0">
                                <li><strong>messages</strong> - Receive incoming messages</li>
                                <li><strong>message_status</strong> - Track message delivery status</li>
                                <li><strong>message_template_status_update</strong> - Template approvals</li>
                                <li><strong>phone_number_quality_update</strong> - Phone number quality changes</li>
                            </ul>
                        </li>

                        <li class="list-group-item">
                            <strong>Save Configuration</strong>
                            <p class="text-muted mb-2">Click "Save" to apply the webhook configuration</p>
                        </li>

                        <li class="list-group-item">
                            <strong>Verification</strong>
                            <p class="text-muted mb-2">Meta will send a test webhook request to verify your endpoint. Our system will automatically respond with the verify token</p>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Visual Guide -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-images me-2"></i>Visual Guide</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Pro Tip:</strong> After setting up the webhook, Meta will send a verification request to your URL. 
                        Our system will automatically validate using your verify token and respond with "200 OK". 
                        This usually takes a few seconds to complete.
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Important Notes</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Your webhook URL must be publicly accessible (HTTPS only)</li>
                        <li>The verify token is unique to this WhatsApp account for security</li>
                        <li>If you regenerate the token, you MUST update Meta Business Manager immediately</li>
                        <li>Incoming messages will only be processed if the verify token matches</li>
                        <li>Keep your verify token secret - never share it publicly</li>
                        <li>Webhooks typically start working within 1-5 minutes after configuration</li>
                    </ul>
                </div>
            </div>

            <!-- Troubleshooting -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-bug me-2"></i>Troubleshooting</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="troubleshootingAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    Webhook verification failing in Meta
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>Make sure the Webhook URL is exactly as shown above (copy-paste recommended)</li>
                                        <li>Verify the token is correctly entered in Meta Business Manager</li>
                                        <li>Check that your server is running and accessible from the internet</li>
                                        <li>Ensure HTTPS is enabled (not HTTP)</li>
                                        <li>Check your firewall/server logs for any connection errors</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    Not receiving incoming messages
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>Confirm webhook is set up correctly in Meta (green checkmark)</li>
                                        <li>Verify that "messages" event is selected in webhook subscriptions</li>
                                        <li>Check that this WhatsApp Business Account has incoming message permissions</li>
                                        <li>Send a test message from another WhatsApp account to this number</li>
                                        <li>Check application logs for any errors</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                    401 Unauthorized or Token mismatch errors
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>The verify token in Meta must exactly match the token shown above</li>
                                        <li>If you regenerated the token, the old one will no longer work</li>
                                        <li>Check for extra spaces or typos in the token</li>
                                        <li>Consider regenerating a new token and updating Meta again</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Account Name:</dt>
                        <dd class="col-sm-7">{{ $account->account_name }}</dd>
                        
                        <dt class="col-sm-5">Phone ID:</dt>
                        <dd class="col-sm-7"><code>{{ substr($account->phone_number_id, 0, 6) }}...{{ substr($account->phone_number_id, -4) }}</code></dd>
                        
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @if($account->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Links</h5>
                </div>
                <div class="card-body">
                    <a href="https://business.facebook.com/" class="btn btn-outline-primary w-100 mb-2" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Meta Business Manager
                    </a>
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left me-1"></i> Back to Accounts
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.value;
    
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
        
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    });
}
</script>
@endsection
