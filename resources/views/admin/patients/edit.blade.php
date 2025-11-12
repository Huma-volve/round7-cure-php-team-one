@extends('admin.master')
@section('title', __('patients.Edit Patient'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('patients.Edit Patient')}}</h1>
        <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{__('patients.Back')}}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{__('patients.Patient Information')}}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.patients.update', $patient->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('patients.Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $patient->user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">{{ __('patients.Email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $patient->user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mobile">{{ __('patients.Mobile') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                           id="mobile" name="mobile" value="{{ old('mobile', $patient->user->mobile) }}" required>
                    @error('mobile')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gender">{{ __('patients.Gender') }} </label>
                            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                                <option value="">{{__('patients.Select Gender')}}</option>
                                <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                                <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>آخر</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="birthdate"> {{ __('patients.Birthdate') }} </label>
                            <input type="date" class="form-control @error('birthdate') is-invalid @enderror"
                                   id="birthdate" name="birthdate" value="{{ old('birthdate', $patient->birthdate ? $patient->birthdate->format('Y-m-d') : '') }}">
                            @error('birthdate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="medical_notes"> {{ __('patients.Medical Notes') }} </label>
                    <textarea name="medical_notes" id="medical_notes" class="form-control @error('medical_notes') is-invalid @enderror"
                              rows="4">{{ old('medical_notes', $patient->medical_notes) }}</textarea>
                    @error('medical_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('patients.Save Changes') }}  </button>
                    <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-secondary">{{__('patients.Cancel')}}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

