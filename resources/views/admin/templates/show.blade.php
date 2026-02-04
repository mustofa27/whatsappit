@extends('admin.layout-new')

@section('title', 'Template Details')
@section('page-title', 'Template Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Template: {{ $template->name }}</h2>
        <div>
            @if($template->status === 'draft')
                <form action="{{ route('admin.templates.submit', $template) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-send me-1"></i> Submit for Approval
                    </button>
                </form>
                <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            @endif
            <form action="{{ route('admin.templates.duplicate', $template) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-info">
                    <i class="bi bi-files me-1"></i> Duplicate
                </button>
            </form>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Template Preview</h5>
                </div>
                <div class="card-body">
                    <div class="border rounded p-4 bg-light">
                        @if($template->header_content)
                            <div class="mb-3">
                                @if($template->header_type === 'TEXT')
                                    <h5 class="fw-bold">{{ $template->header_content }}</h5>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-image"></i> {{ $template->header_type }}: {{ $template->header_content }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mb-3" style="white-space: pre-wrap;">{{ $template->body_content }}</div>

                        @if($template->footer_content)
                            <div class="text-muted small">{{ $template->footer_content }}</div>
                        @endif

                        @if($template->buttons && count($template->buttons) > 0)
                            <hr>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($template->buttons as $button)
                                    <button class="btn btn-sm btn-outline-primary" disabled>
                                        {{ $button['text'] ?? 'Button' }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($template->variables && count($template->variables) > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Template Variables</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Placeholder</th>
                                        <th>Index</th>
                                        <th>Example Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($template->variables as $variable)
                                        <tr>
                                            <td><code>{{ $variable['placeholder'] }}</code></td>
                                            <td>{{ $variable['index'] }}</td>
                                            <td>
                                                <input type="text" 
                                                       class="form-control form-control-sm variable-input" 
                                                       data-index="{{ $variable['index'] }}"
                                                       placeholder="Sample value {{ $variable['index'] }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" onclick="generatePreview()">
                            <i class="bi bi-eye me-1"></i> Preview with Values
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Template Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            <span class="badge {{ $template->status_badge }}">{{ ucfirst($template->status) }}</span>
                        </dd>

                        <dt class="col-sm-5">Category:</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-info">{{ $template->category_label }}</span>
                        </dd>

                        <dt class="col-sm-5">Language:</dt>
                        <dd class="col-sm-7">{{ strtoupper($template->language) }}</dd>

                        <dt class="col-sm-5">Account:</dt>
                        <dd class="col-sm-7">{{ $template->whatsappAccount->name }}</dd>

                        <dt class="col-sm-5">Variables:</dt>
                        <dd class="col-sm-7">
                            @if($template->variables && count($template->variables) > 0)
                                <span class="badge bg-secondary">{{ count($template->variables) }}</span>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </dd>

                        @if($template->meta_template_id)
                            <dt class="col-sm-5">Meta ID:</dt>
                            <dd class="col-sm-7">
                                <code class="small">{{ $template->meta_template_id }}</code>
                            </dd>
                        @endif

                        <dt class="col-sm-5">Created:</dt>
                        <dd class="col-sm-7">
                            <small>{{ $template->created_at->format('Y-m-d H:i') }}</small>
                        </dd>

                        <dt class="col-sm-5">Updated:</dt>
                        <dd class="col-sm-7">
                            <small>{{ $template->updated_at->format('Y-m-d H:i') }}</small>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Usage Statistics</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Total Uses:</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-primary">{{ $template->usage_count }}</span>
                        </dd>

                        <dt class="col-sm-6">Last Used:</dt>
                        <dd class="col-sm-6">
                            @if($template->last_used_at)
                                <small>{{ $template->last_used_at->diffForHumans() }}</small>
                            @else
                                <small class="text-muted">Never</small>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            @if($template->status === 'draft')
                <div class="card shadow-sm border-danger">
                    <div class="card-header bg-danger text-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.templates.destroy', $template) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Delete this template?')">
                                    <i class="bi bi-trash me-1"></i> Delete Template
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function generatePreview() {
    const variables = {};
    document.querySelectorAll('.variable-input').forEach(input => {
        const index = input.getAttribute('data-index');
        const value = input.value || 'Sample' + index;
        variables[index] = value;
    });

    // Simple client-side preview (you can enhance this with AJAX)
    let body = `{{ $template->body_content }}`;
    let header = `{{ $template->header_content }}`;
    let footer = `{{ $template->footer_content }}`;

    Object.keys(variables).forEach(index => {
        const regex = new RegExp(`\\{\\{${index}\\}\\}`, 'g');
        body = body.replace(regex, variables[index]);
        if (header) header = header.replace(regex, variables[index]);
        if (footer) footer = footer.replace(regex, variables[index]);
    });

    alert(`Preview:\n\n${header ? header + '\n\n' : ''}${body}${footer ? '\n\n' + footer : ''}`);
}
</script>
@endsection
