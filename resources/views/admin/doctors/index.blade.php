@extends('admin.master')
@section('title' , 'Doctor')

@section('content')
          <h1 class="h3 mb-2 text-gray-800">Tables</h1>
                    <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
                        For more information about DataTables, please visit the <a target="_blank"
                            href="https://datatables.net">official DataTables documentation</a>.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Birthdate</th>
                                    <th>Booking Date</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                                    </thead>

                                    <tbody>
                                           @foreach($patients as $index => $booking)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $booking->patient->user->name }}</td>
                                        <td>{{ $booking->patient->user->email }}</td>
                                        <td>{{ $booking->patient->user->phone }}</td>
                                        <td>{{ $booking->patient->gender }}</td>
                                        <td>{{ $booking->patient->birthdate }}</td>
                                        <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                                        <td>{{ ucfirst($booking->status) }}</td>
                                         <td>
                                        <a href="{{ route('doctor.patients.show', $booking->patient_id) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </td>
                                    </tr>
                            @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
@endsection
