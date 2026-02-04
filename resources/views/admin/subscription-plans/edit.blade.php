@extends('admin.layout-new')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <div class="d-flex align-items-center mb-2">
            <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-link p-0 me-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0">Edit Subscription Plan</h1>
        </div>
        <p class="text-muted">Update pricing plan details</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.subscription-plans.update', $subscriptionPlan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $subscriptionPlan->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price (IDR) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', $subscriptionPlan->price) }}" required min="0">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2">{{ old('description', $subscriptionPlan->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_accounts" class="form-label">Max WhatsApp Accounts</label>
                                <input type="number" class="form-control @error('max_accounts') is-invalid @enderror" 
                                       id="max_accounts" name="max_accounts" value="{{ old('max_accounts', $subscriptionPlan->max_accounts) }}" min="1">
                                <small class="text-muted">Leave empty for unlimited</small>
                                @error('max_accounts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_users" class="form-label">Max Users</label>
                                <input type="number" class="form-control @error('max_users') is-invalid @enderror" 
                                       id="max_users" name="max_users" value="{{ old('max_users', $subscriptionPlan->max_users) }}" min="1">
                                <small class="text-muted">Leave empty for unlimited</small>
                                @error('max_users')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Features <span class="text-danger">*</span></label>
                            <div id="features-container">
                                @foreach(old('features', $subscriptionPlan->features) as $index => $feature)
                                <div class="input-group mb-2 feature-item">
                                    <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                    <input type="text" class="form-control" name="features[]" value="{{ $feature }}" required>
                                    <button type="button" class="btn btn-outline-danger remove-feature" onclick="removeFeature(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addFeature()">
                                <i class="bi bi-plus-circle me-1"></i> Add Feature
                            </button>
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $subscriptionPlan->sort_order) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" 
                                   value="1" {{ old('is_popular', $subscriptionPlan->is_popular) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_popular">
                                Mark as Popular Plan
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', $subscriptionPlan->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (visible to users)
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update Plan
                            </button>
                            <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const newFeature = document.createElement('div');
    newFeature.className = 'input-group mb-2 feature-item';
    newFeature.innerHTML = `
        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
        <input type="text" class="form-control" name="features[]" placeholder="Enter feature" required>
        <button type="button" class="btn btn-outline-danger remove-feature" onclick="removeFeature(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(newFeature);
}

function removeFeature(button) {
    const container = document.getElementById('features-container');
    const featureItems = container.querySelectorAll('.feature-item');
    
    // Prevent removing the last feature
    if (featureItems.length > 1) {
        button.closest('.feature-item').remove();
    } else {
        alert('At least one feature is required');
    }
}
</script>
@endsection
