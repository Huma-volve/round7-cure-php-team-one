@extends('admin.master')
@section('title' , __('disputes.title'))

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">{{ __('disputes.title') }}</h1>

  <ul class="nav nav-tabs mb-3" id="disputesTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="payments-tab" data-toggle="tab" href="#payments" role="tab">
        {{ __('disputes.payment_disputes') }}
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="bookings-tab" data-toggle="tab" href="#bookings" role="tab">
        {{ __('disputes.booking_disputes') }}
      </a>
    </li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade show active" id="payments" role="tabpanel">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>{{ __('disputes.table.id') }}</th>
              <th>{{ __('disputes.table.reason') }}</th>
              <th>{{ __('disputes.table.status') }}</th>
              <th>{{ __('disputes.table.booking') }}</th>
              <th>{{ __('disputes.table.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($paymentDisputes as $dispute)
              <tr>
                <td>{{ ($paymentDisputes->currentPage() - 1) * $paymentDisputes->perPage() + $loop->iteration }}</td>
                <td>{{ $dispute->reason ?? '-' }}</td>
                <td>
                  <span class="badge badge-{{ $dispute->status == 'resolved' ? 'success' : ($dispute->status == 'rejected' ? 'danger' : 'warning') }}">
                    {{ __('disputes.status.' . $dispute->status) }}
                  </span>
                </td>
                <td>
                  @if($dispute->payment && $dispute->payment->booking_id)
                    <a href="{{ route('admin.bookings.show', $dispute->payment->booking_id) }}">#{{ $dispute->payment->booking_id }}</a>
                  @else
                    -
                  @endif
                </td>
                <td>
                  <a href="{{ route('admin.disputes.show', ['payment', $dispute->id]) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i> {{ __('disputes.table.view') }}
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">{{ __('disputes.table.no_results') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $paymentDisputes->appends(request()->query())->links() }}
    </div>

    <div class="tab-pane fade" id="bookings" role="tabpanel">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>{{ __('disputes.table.id') }}</th>
              <th>{{ __('disputes.table.type') }}</th>
              <th>{{ __('disputes.table.status') }}</th>
              <th>{{ __('disputes.table.booking') }}</th>
              <th>{{ __('disputes.table.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bookingDisputes as $dispute)
              <tr>
                <td>{{ ($bookingDisputes->currentPage() - 1) * $bookingDisputes->perPage() + $loop->iteration }}</td>
                <td>{{ $dispute->type }}</td>
                <td>
                  <span class="badge badge-{{ $dispute->status == 'resolved' ? 'success' : ($dispute->status == 'rejected' ? 'danger' : 'warning') }}">
                    {{ __('disputes.status.' . $dispute->status) }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('admin.bookings.show', $dispute->booking_id) }}">#{{ $dispute->booking_id }}</a>
                </td>
                <td>
                  <a href="{{ route('admin.disputes.show', ['booking', $dispute->id]) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i> {{ __('disputes.table.view') }}
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">{{ __('disputes.table.no_results') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $bookingDisputes->appends(request()->query())->links() }}
    </div>
  </div>
</div>
@endsection
