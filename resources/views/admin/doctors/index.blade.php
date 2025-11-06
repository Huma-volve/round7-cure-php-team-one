@extends('admin.master')
@section('title', 'الأطباء')

@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">الأطباء</h1>
    <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> إضافة طبيب جديد
    </a>
  </div>

  <form method="GET" action="{{ route('admin.doctors.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
      <input type="text" name="q" class="form-control" placeholder="بحث بالاسم أو البريد" value="{{ request('q') }}">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-control">
        <option value="">كل الحالات</option>
        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوف</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">بحث</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>البريد</th>
          <th>التخصص</th>
          <th>رقم الترخيص</th>
          <th>سعر الجلسة</th>
          <th>الحالة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($doctors as $doctor)
          <tr>
            <td>{{ ($doctors->currentPage() - 1) * $doctors->perPage() + $loop->iteration }}</td>
            <td>{{ $doctor->user->name ?? '-' }}</td>
            <td>{{ $doctor->user->email ?? '-' }}</td>
            <td>{{ $doctor->specialty->name ?? '-' }}</td>
            <td>{{ $doctor->license_number ?? '-' }}</td>
            <td>{{ $doctor->session_price ?? '-' }} EGP</td>
            <td>
              <span class="badge badge-{{ $doctor->status == 'active' ? 'success' : ($doctor->status == 'suspended' ? 'danger' : 'warning') }}">
                {{ $doctor->status == 'active' ? 'نشط' : ($doctor->status == 'suspended' ? 'موقوف' : 'غير نشط') }}
              </span>
            </td>
            <td>
              <div class="btn-group" role="group">
                <a href="{{ route('admin.doctors.show', $doctor->id) }}" class="btn btn-sm btn-info">
                  <i class="fas fa-eye"></i> عرض
                </a>
                <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="btn btn-sm btn-primary">
                  <i class="fas fa-edit"></i> تعديل
                </a>
                <form action="{{ route('admin.doctors.toggleStatus', $doctor->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-{{ $doctor->status == 'active' ? 'warning' : 'success' }}">
                    <i class="fas fa-{{ $doctor->status == 'active' ? 'ban' : 'check' }}"></i> {{ $doctor->status == 'active' ? 'إيقاف' : 'تفعيل' }}
                  </button>
                </form>
                <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطبيب؟');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> حذف
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">لا توجد نتائج</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $doctors->appends(request()->query())->links() }}
</div>
@endsection
