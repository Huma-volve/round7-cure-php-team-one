@extends('admin.master')
@section('title', __('tickets.title'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('tickets.details_title', ['id' => $ticket->id]) }}</h1>
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('tickets.back') }}
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    {{ __('tickets.details_card') }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('tickets.fields.subject') }}</small>
                        <span class="fw-bold">{{ $ticket->subject }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('tickets.fields.contact_name') }}</small>
                        <span class="fw-bold">{{ $ticket->contact_name ?? $ticket->user?->name ?? '-' }}</span>
                        <div class="text-muted">{{ $ticket->contact_email ?? $ticket->user?->email ?? '-' }}</div>
                        @if($ticket->contact_phone)
                            <div class="text-muted">{{ $ticket->contact_phone }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('tickets.fields.priority') }}</small>
                        <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning text-dark' : 'secondary') }}">
                            {{ __('tickets.priority.' . $ticket->priority) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('tickets.fields.status') }}</small>
                        <span class="badge bg-{{ $ticket->status === 'closed' ? 'secondary' : ($ticket->status === 'pending' ? 'warning text-dark' : 'success') }}">
                            {{ __('tickets.status.' . $ticket->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('tickets.fields.assigned') }}</small>
                        <span>{{ $ticket->assignedAdmin->name ?? '-' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('tickets.fields.source') }}</small>
                        <span class="text-uppercase">{{ $ticket->source }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('tickets.fields.created_at') }}</small>
                        <span>{{ $ticket->created_at?->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    {{ __('tickets.status_form.title') }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="status">{{ __('tickets.fields.status') }}</label>
                            <select name="status" id="status" class="form-select">
                                @foreach(['open', 'pending', 'closed'] as $status)
                                    <option value="{{ $status }}" {{ $ticket->status === $status ? 'selected' : '' }}>
                                        {{ __('tickets.status.' . $status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="assigned_admin_id">{{ __('tickets.fields.assigned') }}</label>
                            <select name="assigned_admin_id" id="assigned_admin_id" class="form-select">
                                <option value="">{{ __('tickets.status_form.unassigned') }}</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ $ticket->assigned_admin_id === $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">{{ __('tickets.status_form.submit') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    {{ __('tickets.thread_title') }}
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y:auto;">
                    @forelse($ticket->messages->sortBy('created_at') as $message)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold">
                                    {{ $message->sender_type === 'admin' ? ($message->sender->name ?? __('tickets.labels.admin')) : ($ticket->contact_name ?? $ticket->user?->name ?? __('tickets.labels.customer')) }}
                                </div>
                                <div class="text-muted small">{{ $message->created_at?->diffForHumans() }}</div>
                            </div>
                            <p class="mb-0 mt-2">{{ $message->message }}</p>
                        </div>
                        <hr>
                    @empty
                        <p class="text-muted mb-0">{{ __('tickets.no_messages') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    {{ __('tickets.reply_form.title') }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">
                        @csrf
                        <div class="form-group">
                            <label for="message">{{ __('tickets.reply_form.message') }}</label>
                            <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="reply_status">{{ __('tickets.reply_form.status') }}</label>
                            <select name="status" id="reply_status" class="form-select">
                                <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>
                                    {{ __('tickets.status.pending') }}
                                </option>
                                <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>
                                    {{ __('tickets.status.open') }}
                                </option>
                                <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>
                                    {{ __('tickets.status.closed') }}
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">{{ __('tickets.reply_form.submit') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

