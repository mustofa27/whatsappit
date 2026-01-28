@extends('admin.layout-new')

@section('title', 'Message Details')
@section('page-title', 'Message Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Message Information</h5>
                    @if($message->status == 'sent')
                    <span class="badge bg-success">Sent</span>
                    @elseif($message->status == 'failed')
                    <span class="badge bg-danger">Failed</span>
                    @else
                    <span class="badge bg-warning">{{ ucfirst($message->status) }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="200">WhatsApp Account:</th>
                                <td>
                                    <a href="{{ route('admin.accounts.show', $message->whatsappAccount) }}">
                                        {{ $message->whatsappAccount->name }}
                                    </a>
                                    <br><small class="text-muted">{{ $message->whatsappAccount->phone_number }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Recipient:</th>
                                <td>{{ $message->recipient_number }}</td>
                            </tr>
                            <tr>
                                <th>Message:</th>
                                <td>{{ $message->message ?: '-' }}</td>
                            </tr>
                            @if($message->media_url)
                            <tr>
                                <th>Media:</th>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($message->media_type) }}</span>
                                    <br>
                                    @if($message->media_type == 'image')
                                    <img src="{{ $message->media_url }}" alt="Media" class="img-thumbnail mt-2" style="max-width: 300px;">
                                    @else
                                    <a href="{{ $message->media_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bi bi-download me-2"></i> Download Media
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($message->status == 'sent')
                                    <span class="badge bg-success">Sent</span>
                                    @elseif($message->status == 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                    @else
                                    <span class="badge bg-warning">{{ ucfirst($message->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($message->error_message)
                            <tr>
                                <th>Error:</th>
                                <td class="text-danger">{{ $message->error_message }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Sent At:</th>
                                <td>{{ $message->sent_at ? $message->sent_at->format('d M Y, H:i:s') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $message->created_at->format('d M Y, H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4">
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Back to Messages
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
