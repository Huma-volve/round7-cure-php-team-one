@extends('admin.master')
@section('title', __('doctor_show.Doctor Details') )

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> {{ __('doctor_show.Doctor Details') }}  </h1>
        <div>
            <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> {{ __('doctor_show.Edit') }}
            </a>
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('doctor_show.Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('doctor_show.Doctor Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('doctor_show.Name') }}:</strong></div>
                        <div class="col-md-8">{{ $doctor->user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>   {{ __('doctor_show.Email') }}:</strong></div>
                        <div class="col-md-8">{{ $doctor->user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>   {{ __('doctor_show.Mobile') }}:</strong></div>
                        <div class="col-md-8">{{ $doctor->user->mobile ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('doctor_show.Specialty') }}:</strong></div>
                        <div class="col-md-8">{{ $doctor->specialty->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>   {{ __('doctor_show.License Number') }}:</strong></div>
                        <div class="col-md-8">{{ $doctor->license_number }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>  {{ __('doctor_show.Session Price') }}:</strong></div>
                        <div class="col-md-8">{{ $doctor->session_price }} EGP</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>  {{ __('doctor_show.Consultation Type') }}:</strong></div>
                        <div class="col-md-8">
                            @if($doctor->consultation == 'clinic')
                                <span class="badge badge-info">في العيادة (Clinic)</span>
                            @elseif($doctor->consultation == 'home')
                                <span class="badge badge-info">زيارة منزلية (Home)</span>
                            @elseif($doctor->consultation == 'both')
                                <span class="badge badge-info">الاثنان معاً (Both)</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>   {{ __('doctor_show.Clinic Address') }}:</strong></div>
                        <div class="col-md-8">{{ $doctor->clinic_address ?? '-' }}</div>
                    </div>
                    @if($doctor->latitude && $doctor->longitude)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>   {{ __('doctor_show.Location') }}:</strong></div>
                        <div class="col-md-8">
                          {{ __('doctor_show.Latitude') }} {{ $doctor->latitude }},  {{ __('doctor_show.Longitude') }}: {{ $doctor->longitude }}
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>   {{ __('doctor_show.Status') }}:</strong></div>
                        <div class="col-md-8">
                            <span class="badge badge-{{ $doctor->status == 'active' ? 'success' : ($doctor->status == 'suspended' ? 'danger' : 'warning') }}">
                                {{ $doctor->status == 'active' ? __('doctor_show.Active') : ($doctor->status == 'suspended' ? __('doctor_show.Suspended') : __('doctor_show.Inactive')) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>   {{ __('doctor_show.Registration Date') }}:</strong></div>
                        <div class="col-md-8">{{ $doctor->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($doctor->bookings && $doctor->bookings->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('doctor_show.Bookings') }} ({{ $doctor->bookings_count }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('doctor_show.Patient') }}</th>
                                    <th>{{ __('doctor_show.Date') }}</th>
                                    <th>{{ __('doctor_show.Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doctor->bookings->take(10) as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}">#{{ $booking->id }}</a>
                                        </td>
                                        <td>{{ $booking->patient->user->name ?? '-' }}</td>
                                        <td>{{ $booking->date_time ? $booking->date_time->format('Y-m-d H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($doctor->reviews && $doctor->reviews->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('doctor_show.Reviews') }} ({{ $doctor->reviews_count }})</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>  {{ __('doctor_show.Reviews') }}:</strong> {{ $doctor->average_rating ?? 0 }} / 5.0
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('doctor_show.Actions') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.doctors.toggleStatus', $doctor->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-{{ $doctor->status == 'active' ? 'warning' : 'success' }} btn-block">
                            <i class="fas fa-{{ $doctor->status == 'active' ? 'ban' : 'check' }}"></i>
                            {{ $doctor->status == 'active' ? __('doctor_show.Suspend Doctor') : __('doctor_show.Activate Doctor') }}
                        </button>
                    </form>

                    <a href="{{ route('admin.users.show', $doctor->user_id) }}" class="btn btn-info btn-block mb-3">
                        <i class="fas fa-user"></i> {{__('doctor_show.View User Details') }}
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('doctor_show.Delete Doctor') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('doctor_show.Warning: The doctor will be permanently deleted and this action cannot be undone.') }}</p>
                    <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" onsubmit="return confirm(__('doctor_show.Are you sure you want to delete this doctor?'));">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i>{{ __('doctor_show.Delete') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('doctor_show.Statistics') }}</h6>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('doctor_show.Total Bookings') }}:</strong> {{ $doctor->bookings_count ?? 0 }}</p>
                    <p><strong>{{ __('doctor_show.Total Reviews') }}:</strong> {{ $doctor->reviews_count ?? 0 }}</p>
                    <p><strong>{{ __('doctor_show.Average Rating') }}:</strong> {{ $doctor->average_rating ?? 0 }} / 5.0</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
