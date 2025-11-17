@extends('admin.master')
@section('title' , __('bookings.title'))

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">الحجوزات</h1>
  <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value=""> {{__('bookings.title')}}</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}> {{__('bookings.pending')}}</option>
        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{__('bookings.confirmed')}}</option>
        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{__('bookings.cancelled')}}</option>
        <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}> {{__('bookings.rescheduled')}}</option>
      </select>
    </div>
    <div class="col-md-3">
      <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div class="col-md-3">
      <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">{{__('bookings.filter')}}</button>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>{{__('bookings.doctor')}}</th>
          <th>{{__('bookings.patient')}}</th>
          <th>{{__('bookings.time')}}</th>
          <th>{{__('bookings.status')}}</th>
          <th>{{__('bookings.actions')}}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bookings as $booking)
          <tr>
            <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}</td>
            <td>{{ $booking->doctor->user->name ?? '-' }}</td>
            <td>{{ $booking->patient->user->name ?? '-' }}</td>
            <td>{{ $booking->date_time ? $booking->date_time->format('Y-m-d H:i') : '-' }}</td>
            <td>
              <span class="badge badge-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                {{ $booking->status }}
              </span>
            </td>
            <td>
              <div class="btn-group" role="group">
                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                  <i class="fas fa-eye"></i> {{__('bookings.show')}}
                </a>
                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-sm btn-primary">
                  <i class="fas fa-edit"></i> {{__('bookings.edit')}}
                </a>
                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('bookings.confirm_delete') }}');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> {{__('bookings.delete')}}
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center">{{__('bookings.no_results')}}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $bookings->appends(request()->query())->links() }}
</div>
@endsection

