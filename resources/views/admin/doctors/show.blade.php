@extends('admin.master')
@section('title' , 'Patient Details')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Patient Details</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Patient Information</h6>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $patient->user->name ?? '' }}</p>
            <p><strong>Email:</strong> {{ $patient->user->email ?? '' }}</p>
            <p><strong>Phone:</strong> {{ $patient->user->mobile ?? '' }}</p>
            <p><strong>Gender:</strong> {{ $patient->gender ?? '' }}</p>
            <p><strong>Birthdate:</strong> {{ $patient->birthdate->format('Y-m-d')  ?? '' }}</p>
            <p><strong>Medical Notes:</strong> {{ $patient->medical_notes ?? '' }}</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Booking Information</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Doctor Name</th>
                        <th>Payment Method</th>
                        <th>Price</th>
                        <th>Booking Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                        @foreach($bookings as $index => $booking)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $booking->doctor->user->name }}</td>
                                <td>{{ $booking->price }}</td>
                                <td>{{ $booking->payment_method }}</td>
                                <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                                <td>{{ ucfirst($booking->status) }}</td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
