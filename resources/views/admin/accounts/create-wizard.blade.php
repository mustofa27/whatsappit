@extends('admin.layout-new')

@section('title', 'Setup WhatsApp Account')
@section('page-title', 'Setup WhatsApp Account')

@section('content')
<style>
    .setup-wizard {
        max-width: 900px;
        margin: 0 auto;
    }
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }
    .step-indicator::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 0;
    }
    .step-item {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .step-item.active .step-number {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .step-item.completed .step-number {
        background: #10b981;
        color: white;
    }
    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .step-item.active .step-label {
        color: #667eea;
        font-weight: 600;
    }
    .instruction-box {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .help-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .external-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }
    .external-link:hover {
        text-decoration: underline;
    }
</style>

<div class="container-fluid">
    <div class="setup-wizard">
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-item active" id="step-1-indicator">
                <div class="step-number">1</div>
                <div class="step-label">Meta Setup</div>
            </div>
            <div class="step-item" id="step-2-indicator">
                <div class="step-number">2</div>
                <div class="step-label">Get Credentials</div>
            </div>
            <div class="step-item" id="step-3-indicator">
                <div class="step-number">3</div>
                <div class="step-label">Configure</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.accounts.store') }}" id="wizardForm">
            @csrf

            @if(!auth()->user()->canCreateWhatsappAccount())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Account Limit Reached</strong> - You've reached your WhatsApp account limit ({{ auth()->user()->getMaxWhatsappAccounts() }}).
                    <a href="{{ route('subscription.index') }}" class="alert-link">Upgrade your subscription</a> to add more accounts.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-lock-fill" style="font-size: 3rem; color: #dc3545;"></i>
                        <h5 class="mt-3 mb-2">Account Creation Disabled</h5>
                        <p class="text-muted mb-3">You've reached your maximum number of WhatsApp accounts.</p>
                        <p class="mb-4">
                            <strong>Current Usage:</strong> {{ auth()->user()->getWhatsappAccountCount() }} of {{ auth()->user()->getMaxWhatsappAccounts() }} accounts
                        </p>
                        <a href="{{ route('subscription.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-up me-2"></i> Upgrade Subscription
                        </a>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Accounts
                    </a>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Account Usage: {{ auth()->user()->getWhatsappAccountCount() }} of {{ auth()->user()->getMaxWhatsappAccounts() }}
                    @if(auth()->user()->getRemainingAccountSlots() > 0)
                        <span class="ms-2">({{ auth()->user()->getRemainingAccountSlots() }} slot{{ auth()->user()->getRemainingAccountSlots() > 1 ? 's' : '' }} remaining)</span>
                    @endif
                </div>
            <div class="card shadow-sm mb-4 wizard-step" id="step-1" style="display: block;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-1-circle me-2"></i>Setup Meta WhatsApp Business Account</h5>
                </div>
                <div class="card-body">
                    <div class="instruction-box">
                        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Before You Start</h6>
                        <p class="mb-2">You need a Meta Business account with WhatsApp Business API access. If you don't have one:</p>
                        <ol class="mb-0">
                            <li>Go to <a href="https://business.facebook.com" target="_blank" class="external-link">Meta Business Suite <i class="bi bi-box-arrow-up-right"></i></a></li>
                            <li>Create a Business account (if you don't have one)</li>
                            <li>Add WhatsApp product from the dashboard</li>
                            <li>Register your phone number</li>
                        </ol>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Tip:</strong> Keep the Meta Business Suite open in another tab. You'll need to copy some values from there.
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Cancel Setup
                        </a>
                        <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                            Next: Get Credentials <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Get Credentials -->
            <div class="card shadow-sm mb-4 wizard-step" id="step-2" style="display: none;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-2-circle me-2"></i>Get Your API Credentials</h5>
                </div>
                <div class="card-body">
                    <div class="instruction-box">
                        <h6 class="fw-bold mb-3"><i class="bi bi-key me-2"></i>Step-by-Step Guide</h6>
                        
                        <div class="mb-4">
                            <strong>1. Get Phone Number ID:</strong>
                            <ul class="mt-2 mb-2">
                                <li>Go to <a href="https://business.facebook.com/wa/manage/phone-numbers/" target="_blank" class="external-link">WhatsApp Manager <i class="bi bi-box-arrow-up-right"></i></a></li>
                                <li>Click on your phone number</li>
                                <li>Copy the <strong>Phone number ID</strong> (long number)</li>
                            </ul>
                            <div class="alert alert-light mb-0">
                                <small class="text-muted">Example: 123456789012345</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <strong>2. Get WABA ID:</strong>
                            <ul class="mt-2 mb-2">
                                <li>In WhatsApp Manager, look at the URL</li>
                                <li>Find the number after <code>/wa/manage/home/?waba_id=</code></li>
                            </ul>
                            <div class="alert alert-light mb-0">
                                <small class="text-muted">Example URL: business.facebook.com/wa/manage/home/?waba_id=<strong>987654321</strong></small>
                            </div>
                        </div>

                        <div class="mb-0">
                            <strong>3. Generate Access Token:</strong>
                            <ul class="mt-2 mb-2">
                                <li>Go to <a href="https://business.facebook.com/settings/system-users" target="_blank" class="external-link">System Users <i class="bi bi-box-arrow-up-right"></i></a></li>
                                <li>Click "Add" to create a System User</li>
                                <li>Assign WhatsApp permissions</li>
                                <li>Generate a <strong>permanent token</strong></li>
                                <li>Copy the long token (starts with EAA)</li>
                            </ul>
                            <div class="alert alert-light mb-0">
                                <small class="text-muted">Example: EAAxxxxxxxxxxxxxxxx (very long)</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-shield-lock me-2"></i>
                        <strong>Security:</strong> Never share your access token publicly. It's like a password for your WhatsApp account.
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                            <i class="bi bi-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                            Next: Enter Details <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Configure Account -->
            <div class="card shadow-sm mb-4 wizard-step" id="step-3" style="display: none;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-3-circle me-2"></i>Configure Your WhatsApp Account</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            Account Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required
                               placeholder="e.g., Customer Service WA">
                        <small class="help-text">Give a friendly name to identify this account (e.g., "Sales Team", "Support Bot")</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone_number" class="form-label fw-bold">
                            Phone Number <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                               id="phone_number" name="phone_number" value="{{ old('phone_number') }}" 
                               placeholder="+628123456789" required>
                        <small class="help-text">Your WhatsApp Business phone number with country code (e.g., +62 for Indonesia)</small>
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone_number_id" class="form-label fw-bold">
                            Phone Number ID <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('phone_number_id') is-invalid @enderror" 
                               id="phone_number_id" name="phone_number_id" value="{{ old('phone_number_id') }}" 
                               placeholder="123456789012345" required>
                        <small class="help-text">Copy this from WhatsApp Manager (Step 2.1 above)</small>
                        @error('phone_number_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="waba_id" class="form-label fw-bold">
                            WhatsApp Business Account ID (WABA ID) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('waba_id') is-invalid @enderror" 
                               id="waba_id" name="waba_id" value="{{ old('waba_id') }}" 
                               placeholder="987654321098765" required>
                        <small class="help-text">Find this in the URL of WhatsApp Manager (Step 2.2 above)</small>
                        @error('waba_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="access_token" class="form-label fw-bold">
                            Permanent Access Token <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('access_token') is-invalid @enderror" 
                                  id="access_token" name="access_token" rows="4" 
                                  placeholder="EAAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>{{ old('access_token') }}</textarea>
                        <small class="help-text">
                            <i class="bi bi-shield-lock me-1"></i>
                            Paste the permanent token from System Users (Step 2.3 above). This is encrypted and stored securely.
                        </small>
                        @error('access_token')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                            <i class="bi bi-arrow-left me-1"></i> Previous
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Complete Setup
                        </button>
                    </div>
                </div>
            </div>
        </form>
        @endif

        <!-- Help Section -->
        <div class="card shadow-sm border-primary">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="bi bi-question-circle me-2"></i>Need Help?</h6>
                <p class="mb-2">Having trouble setting up? Here are some resources:</p>
                <ul class="mb-0">
                    <li><a href="/SETUP_META_WHATSAPP.md" target="_blank" class="external-link">Complete Setup Guide <i class="bi bi-file-text"></i></a></li>
                    <li><a href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started" target="_blank" class="external-link">Meta Official Documentation <i class="bi bi-box-arrow-up-right"></i></a></li>
                    <li>Contact support: support@whatsappit.com</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function nextStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.wizard-step').forEach(step => {
        step.style.display = 'none';
    });
    
    // Remove active class from all indicators
    document.querySelectorAll('.step-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Mark previous steps as completed
    for (let i = 1; i < stepNumber; i++) {
        document.getElementById('step-' + i + '-indicator').classList.add('completed');
    }
    
    // Show current step
    document.getElementById('step-' + stepNumber).style.display = 'block';
    document.getElementById('step-' + stepNumber + '-indicator').classList.add('active');
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(stepNumber) {
    nextStep(stepNumber);
}
</script>
@endsection
