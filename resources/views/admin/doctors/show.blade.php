@extends('admin.master')
@section('title', 'تفاصيل الطبيب')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الطبيب</h1>
        <div>
            <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الطبيب</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>الاسم:</strong></div>
                        <div class="col-md-8">{{ $doctor->user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>البريد الإلكتروني:</strong></div>
                        <div class="col-md-8">{{ $doctor->user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>رقم الهاتف:</strong></div>
                        <div class="col-md-8">{{ $doctor->user->mobile ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>التخصص:</strong></div>
                        <div class="col-md-8">{{ $doctor->specialty->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>رقم الترخيص:</strong></div>
                        <div class="col-md-8">{{ $doctor->license_number }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>سعر الجلسة:</strong></div>
                        <div class="col-md-8">{{ $doctor->session_price }} EGP</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>نوع الاستشارة:</strong></div>
                        <div class="col-md-8">
                            @if($doctor->consultation_type && count($doctor->consultation_type) > 0)
                                @foreach($doctor->consultation_type as $type)
                                    <span class="badge badge-info mr-2">
                                        {{ $type == 'in_clinic' ? 'في العيادة (In-clinic)' : 'زيارة منزلية (Home Visit)' }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>عنوان العيادة:</strong></div>
                        <div class="col-md-8">{{ $doctor->clinic_address ?? '-' }}</div>
                    </div>
                    @if($doctor->latitude && $doctor->longitude)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>الموقع:</strong></div>
                        <div class="col-md-8">
                            خط العرض: {{ $doctor->latitude }}, خط الطول: {{ $doctor->longitude }}
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>الحالة:</strong></div>
                        <div class="col-md-8">
                            <span class="badge badge-{{ $doctor->status == 'active' ? 'success' : ($doctor->status == 'suspended' ? 'danger' : 'warning') }}">
                                {{ $doctor->status == 'active' ? 'نشط' : ($doctor->status == 'suspended' ? 'موقوف' : 'غير نشط') }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>تاريخ التسجيل:</strong></div>
                        <div class="col-md-8">{{ $doctor->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($doctor->bookings && $doctor->bookings->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الحجوزات ({{ $doctor->bookings_count }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المريض</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
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
                    <h6 class="m-0 font-weight-bold text-primary">التقييمات ({{ $doctor->reviews_count }})</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>متوسط التقييم:</strong> {{ $doctor->average_rating ?? 0 }} / 5.0
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الإجراءات</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.doctors.toggleStatus', $doctor->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-{{ $doctor->status == 'active' ? 'warning' : 'success' }} btn-block">
                            <i class="fas fa-{{ $doctor->status == 'active' ? 'ban' : 'check' }}"></i> 
                            {{ $doctor->status == 'active' ? 'إيقاف الطبيب' : 'تفعيل الطبيب' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.users.show', $doctor->user_id) }}" class="btn btn-info btn-block mb-3">
                        <i class="fas fa-user"></i> عرض بيانات المستخدم
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger">
                    <h6 class="m-0 font-weight-bold text-primary">حذف الطبيب</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">تنبيه: سيتم حذف الطبيب نهائياً ولا يمكن التراجع عن هذه العملية.</p>
                    <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطبيب؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> حذف الطبيب
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات</h6>
                </div>
                <div class="card-body">
                    <p><strong>عدد الحجوزات:</strong> {{ $doctor->bookings_count ?? 0 }}</p>
                    <p><strong>عدد التقييمات:</strong> {{ $doctor->reviews_count ?? 0 }}</p>
                    <p><strong>متوسط التقييم:</strong> {{ $doctor->average_rating ?? 0 }} / 5.0</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
