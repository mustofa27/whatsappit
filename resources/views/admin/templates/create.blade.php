@extends('admin.layout-new')

@section('title', 'Create Template')
@section('page-title', 'Create Message Template')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Create Message Template</h2>
        <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('admin.templates.store') }}" id="templateForm">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="whatsapp_account_id" class="form-label">WhatsApp Account <span class="text-danger">*</span></label>
                                    <select class="form-select @error('whatsapp_account_id') is-invalid @enderror" 
                                            id="whatsapp_account_id" name="whatsapp_account_id" required>
                                        <option value="">Select Account</option>
                                        @forelse($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('whatsapp_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No accounts available</option>
                                        @endforelse
                                    </select>
                                    @error('whatsapp_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           placeholder="e.g., welcome_message"
                                           required>
                                    <small class="text-muted">Use lowercase, underscores only</small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="MARKETING" {{ old('category') === 'MARKETING' ? 'selected' : '' }}>Marketing</option>
                                        <option value="UTILITY" {{ old('category') === 'UTILITY' ? 'selected' : '' }}>Utility</option>
                                        <option value="AUTHENTICATION" {{ old('category') === 'AUTHENTICATION' ? 'selected' : '' }}>Authentication</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="language" class="form-label">Language <span class="text-danger">*</span></label>
                                    <select class="form-select @error('language') is-invalid @enderror" 
                                            id="language" name="language" required>
                                        <option value="en" {{ old('language', 'en') === 'en' ? 'selected' : '' }}>English</option>
                                        <option value="id" {{ old('language') === 'id' ? 'selected' : '' }}>Indonesian</option>
                                        <option value="es" {{ old('language') === 'es' ? 'selected' : '' }}>Spanish</option>
                                        <option value="pt_BR" {{ old('language') === 'pt_BR' ? 'selected' : '' }}>Portuguese (Brazil)</option>
                                    </select>
                                    @error('language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Template Content</h5>
                    </div>
                    <div class="card-body">
                        <!-- Header -->
                        <div class="mb-3">
                            <label for="header_type" class="form-label">Header Type (Optional)</label>
                            <select class="form-select @error('header_type') is-invalid @enderror" 
                                    id="header_type" name="header_type">
                                <option value="">None</option>
                                <option value="TEXT" {{ old('header_type') === 'TEXT' ? 'selected' : '' }}>Text</option>
                                <option value="IMAGE" {{ old('header_type') === 'IMAGE' ? 'selected' : '' }}>Image</option>
                                <option value="VIDEO" {{ old('header_type') === 'VIDEO' ? 'selected' : '' }}>Video</option>
                                <option value="DOCUMENT" {{ old('header_type') === 'DOCUMENT' ? 'selected' : '' }}>Document</option>
                            </select>
                            @error('header_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="headerContentDiv" style="display: none;">
                            <label for="header_content" class="form-label">Header Content</label>
                            <input type="text" 
                                   class="form-control @error('header_content') is-invalid @enderror" 
                                   id="header_content" 
                                   name="header_content" 
                                   value="{{ old('header_content') }}"
                                   placeholder="Header text or media URL">
                            <small class="text-muted">For TEXT: Enter text (60 chars max). Use @{{1}}, @{{2}} for variables. For media: Enter URL</small>
                            @error('header_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Body -->
                        <div class="mb-3">
                            <label for="body_content" class="form-label">Body Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('body_content') is-invalid @enderror" 
                                      id="body_content" 
                                      name="body_content" 
                                      rows="5" 
                                      required>{{ old('body_content') }}</textarea>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                <strong>Use double curly braces for variables:</strong> @{{1}}, @{{2}}, @{{3}}, etc. Max 1024 characters.
                            </small>
                            @error('body_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Footer -->
                        <div class="mb-3">
                            <label for="footer_content" class="form-label">Footer Content (Optional)</label>
                            <input type="text" 
                                   class="form-control @error('footer_content') is-invalid @enderror" 
                                   id="footer_content" 
                                   name="footer_content" 
                                   value="{{ old('footer_content') }}"
                                   placeholder="Footer text"
                                   maxlength="60">
                            <small class="text-muted">Max 60 characters. Can use variables: @{{1}}, @{{2}}, etc.</small>
                            @error('footer_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Create Template
                    </button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card shadow-sm bg-light mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-1"></i> Template Guidelines</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>Template names must be unique and use lowercase letters, numbers, and underscores only</li>
                            <li>Variables are numbered: {{1}}, {{2}}, {{3}}, etc.</li>
                            <li>Body content is required and limited to 1024 characters</li>
                            <li>Headers are optional and limited to 60 characters for text</li>
                            <li>Footers are optional and limited to 60 characters</li>
                            <li>Templates must be approved by Meta before use</li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-eye me-1"></i> Live Preview</h6>
                    </div>
                    <div class="card-body" id="previewArea">
                        <div class="border rounded p-3 bg-white">
                            <div id="previewHeader" class="fw-bold mb-2" style="display: none;"></div>
                            <div id="previewBody" class="text-muted">Type content to preview...</div>
                            <div id="previewFooter" class="text-muted small mt-2" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-stars me-1"></i> Variable Examples</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2">Body with variables:</p>
                        <code class="small">Hello @{{1}}, your order @{{2}} is ready!</code>
                        <p class="small mb-0 mt-2">Will render as:</p>
                        <small class="text-muted">Hello John, your order #12345 is ready!</small>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Show/hide header content based on type
document.getElementById('header_type').addEventListener('change', function() {
    const headerContentDiv = document.getElementById('headerContentDiv');
    const headerContentInput = document.getElementById('header_content');
    
    if (this.value) {
        headerContentDiv.style.display = 'block';
        headerContentInput.placeholder = this.value === 'TEXT' 
            ? 'Header text (max 60 chars)' 
            : 'Media URL';
    } else {
        headerContentDiv.style.display = 'none';
        headerContentInput.value = '';
    }
    updatePreview();
});

// Live preview
function updatePreview() {
    const header = document.getElementById('header_content').value;
    const body = document.getElementById('body_content').value;
    const footer = document.getElementById('footer_content').value;
    const headerType = document.getElementById('header_type').value;

    const previewHeader = document.getElementById('previewHeader');
    const previewBody = document.getElementById('previewBody');
    const previewFooter = document.getElementById('previewFooter');

    if (headerType === 'TEXT' && header) {
        previewHeader.textContent = header;
        previewHeader.style.display = 'block';
    } else if (headerType && header) {
        previewHeader.textContent = `[${headerType}]`;
        previewHeader.style.display = 'block';
    } else {
        previewHeader.style.display = 'none';
    }

    previewBody.textContent = body || 'Type content to preview...';
    previewBody.className = body ? '' : 'text-muted';

    if (footer) {
        previewFooter.textContent = footer;
        previewFooter.style.display = 'block';
    } else {
        previewFooter.style.display = 'none';
    }
}

document.getElementById('header_content').addEventListener('input', updatePreview);
document.getElementById('body_content').addEventListener('input', updatePreview);
document.getElementById('footer_content').addEventListener('input', updatePreview);

// Initialize
if (document.getElementById('header_type').value) {
    document.getElementById('headerContentDiv').style.display = 'block';
}
updatePreview();
</script>
@endsection
