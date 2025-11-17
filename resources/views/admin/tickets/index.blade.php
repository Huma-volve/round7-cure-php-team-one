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
    <table class="table table-striped">
      <thead>
        <tr>
          <th>{{ __('tickets.table.number') }}</th>
          <th>{{ __('tickets.table.subject') }}</th>
          <th>{{ __('tickets.table.priority') }}</th>
          <th>{{ __('tickets.table.status') }}</th>
          <th>{{ __('tickets.table.assigned') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tickets as $ticket)
          <tr>
            <td>{{ ($tickets->currentPage() - 1) * $tickets->perPage() + $loop->iteration }}</td>
            <td>{{ $ticket->subject }}</td>
            <td>{{ $ticket->priority }}</td>
            <td>{{ $ticket->status }}</td>
            <td>{{ $ticket->assignedAdmin->name ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">{{ __('tickets.table.no_results') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $tickets->appends(request()->query())->links() }}
</div>
@endsection
