@extends('admin.master')
@section('title', 'تعديل الحجز')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل الحجز</h1>
        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">تعديل بيانات الحجز</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="date_time">تاريخ ووقت الحجز</label>
                    <input type="datetime-local" class="form-control @error('date_time') is-invalid @enderror" 
                           id="date_time" name="date_time" 
                           value="{{ old('date_time', $booking->date_time ? $booking->date_time->format('Y-m-d\TH:i') : '') }}" required>
                    @error('date_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">الحالة</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                        <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>ملغى</option>
                        <option value="rescheduled" {{ old('status', $booking->status) == 'rescheduled' ? 'selected' : '' }}>معاد جدولته</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price">المبلغ</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                           id="price" name="price" value="{{ old('price', $booking->price) }}">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

