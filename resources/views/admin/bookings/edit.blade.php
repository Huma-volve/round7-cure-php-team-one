@extends('admin.master')
@section('title', __('bookings.Edit Booking'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('bookings.Edit Booking') }}</h1>
        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('bookings.Back') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('bookings.Edit Booking Details') }}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="date_time">{{ __('bookings.Booking Date and Time') }}</label>
                    <input type="datetime-local" class="form-control @error('date_time') is-invalid @enderror"
                           id="date_time" name="date_time"
                           value="{{ old('date_time', $booking->date_time ? $booking->date_time->format('Y-m-d\TH:i') : '') }}" required>
                    @error('date_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">{{ __('bookings.Status') }}</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>{{ __('bookings.Pending') }}</option>
                        <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>{{ __('bookings.Confirmed') }}</option>
                        <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>{{ __('bookings.Cancelled') }}</option>
                        <option value="rescheduled" {{ old('status', $booking->status) == 'rescheduled' ? 'selected' : '' }}>{{ __('bookings.Rescheduled') }}</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price">{{ __('bookings.Price') }}</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                           id="price" name="price" value="{{ old('price', $booking->price) }}">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('bookings.Save Changes') }}</button>
                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary">{{ __('bookings.Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
