@extends('admin.master')
@section('title', __('specialties.Specialty Details'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{__('specialties.Specialty Details')}}</h1>
        <div>
            <a href="{{ route('admin.specialties.edit', $specialty->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> {{ __('specialties.Edit') }}
            </a>
            <a href="{{ route('admin.specialties.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('specialties.Back to Specialties') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('specialties.Specialty Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            @if($specialty->image)
                                <img src="{{ asset('storage/' . $specialty->image) }}"
                                     alt="{{ $specialty->name }}"
                                     style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 10px; border: 2px solid #e3e6f0;">
                            @else
                                <div style="width: 200px; height: 200px; background: #f8f9fa; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: 2px solid #e3e6f0;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>{{ __('specialties.Name') }}:</strong></div>
                                <div class="col-md-8">
                                    <h5 class="text-primary">{{ $specialty->name }}</h5>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>{{ __('specialties.Created At') }}:</strong></div>
                                <div class="col-md-8">{{ $specialty->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>{{ __('specialties.Updated At') }}:</strong></div>
                                <div class="col-md-8">{{ $specialty->updated_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Doctors in this specialty --}}
            @if($doctors && $doctors->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('specialties.Doctors in this Specialty') }} ({{ $doctors->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('specialties.Doctor Name') }}</th>
                                    <th>{{ __('specialties.Email') }}</th>
                                    <th>{{ __('specialties.Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doctors as $doctor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.doctors.show', $doctor->id) }}">
                                                {{ $doctor->user->name ?? '-' }}
                                            </a>
                                        </td>
                                        <td>{{ $doctor->user->email ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $doctor->status == 'active' ? 'success' : 'warning' }}">
                                                {{ $doctor->status }}
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
        </div>

        <div class="col-lg-4">
            {{-- Delete Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger">
                    <h6 class="m-0 font-weight-bold text-white">{{ __('specialties.Delete Specialty') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('specialties.Delete Warning') }}</p>
                    <form action="{{ route('admin.specialties.destroy', $specialty->id) }}" method="POST"
                          onsubmit="return confirm('{{ __('specialties.Delete Confirmation') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> {{ __('specialties.Delete Specialty') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Statistics Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('specialties.Statistics') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">{{ $doctors->count() ?? 0 }}</h4>
                                <small class="text-muted">{{ __('specialties.Total Doctors') }}</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success">{{ $specialty->created_at->diffForHumans() }}</h4>
                                <small class="text-muted">{{ __('specialties.Created') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
