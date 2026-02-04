@extends('admin.layout-new')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">System Settings</h2>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        @foreach($settings as $group => $groupSettings)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">{{ ucfirst($group) }} Settings</h5>
            </div>
            <div class="card-body">
                @foreach($groupSettings as $setting)
                <div class="mb-3">
                    <label for="setting_{{ $setting->key }}" class="form-label fw-bold">
                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                    </label>
                    
                    @if($setting->type === 'boolean')
                        <select name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" class="form-select">
                            <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    @elseif($setting->type === 'integer')
                        <input 
                            type="number" 
                            name="settings[{{ $setting->key }}]" 
                            id="setting_{{ $setting->key }}" 
                            class="form-control" 
                            value="{{ $setting->value }}"
                        >
                    @else
                        <input 
                            type="text" 
                            name="settings[{{ $setting->key }}]" 
                            id="setting_{{ $setting->key }}" 
                            class="form-control" 
                            value="{{ $setting->value }}"
                        >
                    @endif
                    
                    @if($setting->description)
                        <small class="form-text text-muted">{{ $setting->description }}</small>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
