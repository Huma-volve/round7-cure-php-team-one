@extends('admin.master')
@section('title', 'تفاصيل المريض')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل المريض</h1>
        <div>
            <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المريض</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>الاسم:</strong></div>
                        <div class="col-md-8">{{ $patient->user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>البريد الإلكتروني:</strong></div>
                        <div class="col-md-8">{{ $patient->user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>رقم الهاتف:</strong></div>
                        <div class="col-md-8">{{ $patient->user->mobile ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>الجنس:</strong></div>
                        <div class="col-md-8">
                            @if($patient->gender)
                                <span class="badge badge-info">
                                    {{ $patient->gender == 'male' ? 'ذكر' : ($patient->gender == 'female' ? 'أنثى' : 'آخر') }}
                                </span>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>تاريخ الميلاد:</strong></div>
                        <div class="col-md-8">{{ $patient->birthdate ? $patient->birthdate->format('Y-m-d') : '-' }}</div>
                    </div>
                    @if($patient->medical_notes)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>ملاحظات طبية:</strong></div>
                        <div class="col-md-8">{{ $patient->medical_notes }}</div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>تاريخ التسجيل:</strong></div>
                        <div class="col-md-8">{{ $patient->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($bookings && $bookings->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الحجوزات ({{ $patient->bookings_count }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الطبيب</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}">#{{ $booking->id }}</a>
                                        </td>
                                        <td>{{ $booking->doctor->user->name ?? '-' }}</td>
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
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger">
                    <h6 class="m-0 font-weight-bold text-primary">حذف المريض</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">تنبيه: سيتم حذف المريض نهائياً ولا يمكن التراجع عن هذه العملية.</p>
                    <form action="{{ route('admin.patients.destroy', $patient->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المريض؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> حذف المريض
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات</h6>
                </div>
                <div class="card-body">
                    <p><strong>عدد الحجوزات:</strong> {{ $patient->bookings_count ?? 0 }}</p>
                    <a href="{{ route('admin.users.show', $patient->user_id) }}" class="btn btn-info btn-block">
                        <i class="fas fa-user"></i> عرض بيانات المستخدم
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

