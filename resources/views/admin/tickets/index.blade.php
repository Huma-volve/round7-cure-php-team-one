@extends('admin.master')
@section('title', __('tickets.title'))

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">{{ __('tickets.heading') }}</h1>
  <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">{{ __('tickets.filters.all_statuses') }}</option>
        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>
          {{ __('tickets.filters.open') }}
        </option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
          {{ __('tickets.filters.pending') }}
        </option>
        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>
          {{ __('tickets.filters.closed') }}
        </option>
      </select>
    </div>

    <div class="col-md-3">
      <select name="priority" class="form-select">
        <option value="">{{ __('tickets.filters.all_priorities') }}</option>
        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>
          {{ __('tickets.filters.low') }}
        </option>
        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>
          {{ __('tickets.filters.medium') }}
        </option>
        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>
          {{ __('tickets.filters.high') }}
        </option>
      </select>
    </div>

    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">
        {{ __('tickets.filters.filter') }}
      </button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>{{ __('tickets.table.number') }}</th>
          <th>{{ __('tickets.table.contact') }}</th>
          <th>{{ __('tickets.table.subject') }}</th>
          <th>{{ __('tickets.table.priority') }}</th>
          <th>{{ __('tickets.table.status') }}</th>
          <th>{{ __('tickets.table.assigned') }}</th>
          <th>{{ __('tickets.table.updated') }}</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($tickets as $ticket)
          <tr>
            <td>{{ ($tickets->currentPage() - 1) * $tickets->perPage() + $loop->iteration }}</td>
            <td>
              <div class="fw-bold">{{ $ticket->contact_name ?? $ticket->user?->name ?? '-' }}</div>
              <div class="text-muted small">{{ $ticket->contact_email ?? $ticket->user?->email ?? '-' }}</div>
            </td>
            <td>
              <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-decoration-none">
                {{ $ticket->subject }}
              </a>
            </td>
            <td>
              <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning text-dark' : 'secondary') }}">
                {{ __('tickets.priority.' . $ticket->priority) }}
              </span>
            </td>
            <td>
              <span class="badge bg-{{ $ticket->status === 'closed' ? 'secondary' : ($ticket->status === 'pending' ? 'warning text-dark' : 'success') }}">
                {{ __('tickets.status.' . $ticket->status) }}
              </span>
            </td>
            <td>{{ $ticket->assignedAdmin->name ?? '-' }}</td>
            <td>{{ $ticket->updated_at?->diffForHumans() }}</td>
            <td>
              <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                {{ __('tickets.table.view') }}
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">{{ __('tickets.table.no_results') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $tickets->appends(request()->query())->links() }}
</div>
@endsection
