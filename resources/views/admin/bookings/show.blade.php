@extends('admin.master')
@section('title', 'تفاصيل الحجز')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الحجز</h1>
        <div>
            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ $backUrl ?? route('admin.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الحجز</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>رقم الحجز:</strong></div>
                        <div class="col-md-8">#{{ $booking->id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>الطبيب:</strong></div>
                        <div class="col-md-8">
                            {{ $booking->doctor->user->name ?? '-' }}
                            @if($booking->doctor->user)
                                <a href="{{ route('admin.users.show', $booking->doctor->user->id) }}" class="btn btn-sm btn-link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>المريض:</strong></div>
                        <div class="col-md-8">
                            {{ $booking->patient->user->name ?? '-' }}
                            @if($booking->patient->user)
                                <a href="{{ route('admin.users.show', $booking->patient->user->id) }}" class="btn btn-sm btn-link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>تاريخ ووقت الحجز:</strong></div>
                        <div class="col-md-8">{{ $booking->date_time ? $booking->date_time->format('Y-m-d H:i') : '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>الحالة:</strong></div>
                        <div class="col-md-8">
                            <span class="badge badge-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                                {{ $booking->status }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>المبلغ:</strong></div>
                        <div class="col-md-8">{{ $booking->price ?? '-' }} {{ $booking->price ? 'EGP' : '' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>طريقة الدفع:</strong></div>
                        <div class="col-md-8">{{ $booking->payment_method ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>تاريخ الإنشاء:</strong></div>
                        <div class="col-md-8">{{ $booking->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($booking->payment)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الدفع</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>رقم الدفع:</strong></div>
                        <div class="col-md-8">
                            <a href="{{ route('admin.payments.show', $booking->payment->id) }}">#{{ $booking->payment->id }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>المبلغ:</strong></div>
                        <div class="col-md-8">{{ $booking->payment->amount }} {{ $booking->payment->currency ?? 'EGP' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>البوابة:</strong></div>
                        <div class="col-md-8">{{ $booking->payment->gateway ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>الحالة:</strong></div>
                        <div class="col-md-8">
                            <span class="badge badge-{{ $booking->payment->status == 'success' ? 'success' : ($booking->payment->status == 'failed' ? 'danger' : 'warning') }}">
                                {{ $booking->payment->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($booking->disputes && $booking->disputes->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">النزاعات</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->disputes as $dispute)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $dispute->type }}</td>
                                        <td>{{ $dispute->status }}</td>
                                        <td>{{ $dispute->created_at->format('Y-m-d H:i') }}</td>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تغيير الحالة</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking->id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="status">الحالة الجديدة</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>ملغى</option>
                                <option value="rescheduled" {{ $booking->status == 'rescheduled' ? 'selected' : '' }}>معاد جدولته</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">تحديث الحالة</button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">حذف الحجز</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">تنبيه: سيتم حذف الحجز نهائياً ولا يمكن التراجع عن هذه العملية.</p>
                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحجز؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> حذف الحجز
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

