@extends('admin.master')
@section('title', __('disputes.title'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('disputes.title') }}</h1>
        <a href="{{ route('admin.disputes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('disputes.back') }}
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('disputes.info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('disputes.id') }}:</strong></div>
                        <div class="col-md-8">#{{ $dispute->id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('disputes.type') }}:</strong></div>
                        <div class="col-md-8">{{ $type === 'payment' ? __('disputes.payment_dispute') : __('disputes.booking_dispute') }}</div>
                    </div>

                    @if($type === 'payment')
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>{{ __('disputes.reason') }}:</strong></div>
                            <div class="col-md-8">{{ $dispute->reason ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>{{ __('disputes.payment_id') }}:</strong></div>
                            <div class="col-md-8">
                                @if($dispute->payment)
                                    <a href="{{ route('admin.payments.show', $dispute->payment->id) }}">#{{ $dispute->payment->id }}</a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>{{ __('disputes.type') }}:</strong></div>
                            <div class="col-md-8">{{ $dispute->type ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>{{ __('disputes.booking_id') }}:</strong></div>
                            <div class="col-md-8">
                                <a href="{{ route('admin.bookings.show', $dispute->booking_id) }}">#{{ $dispute->booking_id }}</a>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('disputes.status') }}:</strong></div>
                        <div class="col-md-8">
                            <span class="badge badge-{{ $dispute->status == 'resolved' ? 'success' : ($dispute->status == 'rejected' ? 'danger' : 'warning') }}">
                                {{ __('disputes.' . $dispute->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('disputes.created_at') }}:</strong></div>
                        <div class="col-md-8">{{ $dispute->created_at->format('Y-m-d H:i') }}</div>
                    </div>

                    @if($dispute->resolution_notes)
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>{{ __('disputes.resolution_notes') }}:</strong></div>
                            <div class="col-md-8">{{ $dispute->resolution_notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if($type === 'payment' && $dispute->payment && $dispute->payment->booking)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('disputes.booking_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('disputes.doctor') }}:</strong></div>
                        <div class="col-md-8">{{ $dispute->payment->booking->doctor->user->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('disputes.patient') }}:</strong></div>
                        <div class="col-md-8">{{ $dispute->payment->booking->patient->user->name ?? '-' }}</div>
                    </div>
                </div>
            </div>
            @endif

            @if($type === 'booking' && $dispute->booking)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('disputes.booking_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('disputes.doctor') }}:</strong></div>
                        <div class="col-md-8">{{ $dispute->booking->doctor->user->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('disputes.patient') }}:</strong></div>
                        <div class="col-md-8">{{ $dispute->booking->patient->user->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('disputes.booking_date') }}:</strong></div>
                        <div class="col-md-8">{{ $dispute->booking->date_time ? $dispute->booking->date_time->format('Y-m-d H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
            @endif

            @if($notes && $notes->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('disputes.notes') }}</h6>
                </div>
                <div class="card-body">
                    @foreach($notes as $note)
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between">
                                <strong>{{ __('disputes.note') }} #{{ $note->id }}</strong>
                                <small>{{ \Carbon\Carbon::parse($note->created_at)->format('Y-m-d H:i') }}</small>
                            </div>
                            <p class="mb-0 mt-2">{{ $note->note }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if($dispute->status === 'pending')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('disputes.resolve_dispute') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.disputes.resolve', [$type, $dispute->id]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="action">{{ __('disputes.action') }}</label>
                            <select name="action" id="action" class="form-control" required>
                                <option value="resolve">{{ __('disputes.resolve') }}</option>
                                <option value="reject">{{ __('disputes.reject') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="resolution_notes">{{ __('disputes.resolution_notes') }}</label>
                            <textarea name="resolution_notes" id="resolution_notes" class="form-control" rows="4" required placeholder="{{ __('disputes.enter_resolution_notes') }}"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('disputes.save') }}</button>
                    </form>
                </div>
            </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('disputes.add_note') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.disputes.addNote', [$type, $dispute->id]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="note">{{ __('disputes.note') }}</label>
                            <textarea name="note" id="note" class="form-control" rows="3" required placeholder="{{ __('disputes.enter_note') }}"></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary">{{ __('disputes.add_note') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
