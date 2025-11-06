@extends('admin.master')
@section('title', 'إضافة طبيب جديد')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إضافة طبيب جديد</h1>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">بيانات الطبيب</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.doctors.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">الاسم <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobile">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                   id="mobile" name="mobile" value="{{ old('mobile') }}" required>
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="specialty_id">التخصص <span class="text-danger">*</span></label>
                            <select name="specialty_id" id="specialty_id" class="form-control @error('specialty_id') is-invalid @enderror" required>
                                <option value="">اختر التخصص</option>
                                @foreach($specialties as $specialty)
                                    <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                        {{ $specialty->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialty_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="license_number">رقم الترخيص <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                                   id="license_number" name="license_number" value="{{ old('license_number') }}" required>
                            @error('license_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="session_price">سعر الجلسة <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('session_price') is-invalid @enderror" 
                                   id="session_price" name="session_price" value="{{ old('session_price') }}" required>
                            @error('session_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clinic_address">عنوان العيادة</label>
                            <input type="text" class="form-control @error('clinic_address') is-invalid @enderror" 
                                   id="clinic_address" name="clinic_address" value="{{ old('clinic_address') }}">
                            @error('clinic_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>نوع الاستشارة</label>
                    <div class="mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="consultation_type[]" 
                                   id="consultation_type_in_clinic" value="in_clinic" 
                                   {{ in_array('in_clinic', old('consultation_type', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="consultation_type_in_clinic">
                                في العيادة (In-clinic)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="consultation_type[]" 
                                   id="consultation_type_home_visit" value="home_visit" 
                                   {{ in_array('home_visit', old('consultation_type', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="consultation_type_home_visit">
                                زيارة منزلية (Home Visit)
                            </label>
                        </div>
                    </div>
                    @error('consultation_type')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    @error('consultation_type.*')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="latitude">خط العرض</label>
                            <input type="number" step="0.00000001" class="form-control @error('latitude') is-invalid @enderror" 
                                   id="latitude" name="latitude" value="{{ old('latitude') }}">
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="longitude">خط الطول</label>
                            <input type="number" step="0.00000001" class="form-control @error('longitude') is-invalid @enderror" 
                                   id="longitude" name="longitude" value="{{ old('longitude') }}">
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

