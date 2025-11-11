@extends('admin.master')
@section('title', 'تفاصيل المستخدم')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل المستخدم</h1>
        <div>
            <a href="{{ route('admin.users.payment-methods.index', $user->id) }}" class="btn btn-info mr-2">
                <i class="fas fa-credit-card"></i> طرق الدفع
            </a>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">معلومات المستخدم</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4"><strong>الاسم:</strong></div>
                    <div class="col-md-8">{{ $user->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>البريد الإلكتروني:</strong></div>
                    <div class="col-md-8">{{ $user->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>رقم الهاتف:</strong></div>
                    <div class="col-md-8">{{ $user->mobile ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>تاريخ الميلاد:</strong></div>
                    <div class="col-md-8">{{ $user->birthdate ? $user->birthdate->format('Y-m-d') : '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>تاريخ التسجيل:</strong></div>
                    <div class="col-md-8">{{ $user->created_at->format('Y-m-d H:i') }}</div>
                </div>
                @if($user->profile_photo)
                <div class="row mb-3">
                    <div class="col-md-4"><strong>الصورة الشخصية:</strong></div>
                    <div class="col-md-8">
                        <img src="{{ asset($user->profile_photo) }}" alt="Profile" class="img-thumbnail" style="max-width: 150px;">
                    </div>
                </div>
                @endif
            </div>
        </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الأدوار</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>الأدوار الحالية:</strong>
                        <div class="mt-2">
                            <span class="badge badge-info">API: {{ implode(', ', $apiRoles ?: ['لا يوجد']) }}</span>
                            <span class="badge badge-secondary ml-2">Web: {{ implode(', ', $webRoles ?: ['لا يوجد']) }}</span>
                        </div>
                    </div>
                    <hr>
                    <form method="POST" action="{{ route('admin.users.updateRoles', $user->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label"><strong>Guard:</strong></label>
                            <select name="guard" id="guardSelect" class="form-control" required>
                                <option value="api">API</option>
                                <option value="web">Web</option>
                            </select>
                        </div>
                        <div class="mb-3" id="apiRolesDiv">
                            <label class="form-label">الأدوار (API Guard):</label>
                            <div>
                                @foreach($allRoles->get('api', []) as $role)
                                    <div class="form-check">
                                        <input class="form-check-input api-role-checkbox" type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                               id="api_role_{{ $role->id }}" {{ in_array($role->name, $apiRoles) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="api_role_{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3 d-none" id="webRolesDiv">
                            <label class="form-label">الأدوار (Web Guard):</label>
                            <div>
                                @foreach($allRoles->get('web', []) as $role)
                                    <div class="form-check">
                                        <input class="form-check-input web-role-checkbox" type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                               id="web_role_{{ $role->id }}" {{ in_array($role->name, $webRoles) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="web_role_{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">تحديث الأدوار</button>
                    </form>
                </div>
                <script>
                    document.getElementById('guardSelect').addEventListener('change', function() {
                        const guard = this.value;
                        if (guard === 'api') {
                            document.getElementById('apiRolesDiv').classList.remove('d-none');
                            document.getElementById('webRolesDiv').classList.add('d-none');
                            document.querySelectorAll('.web-role-checkbox').forEach(cb => cb.checked = false);
                        } else {
                            document.getElementById('apiRolesDiv').classList.add('d-none');
                            document.getElementById('webRolesDiv').classList.remove('d-none');
                            document.querySelectorAll('.api-role-checkbox').forEach(cb => cb.checked = false);
                        }
                    });
                </script>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات</h6>
                </div>
                <div class="card-body">
                    <p><strong>عدد الحجوزات:</strong> {{ $bookings->count() }}</p>
                    @if($user->patient)
                        <p><strong>نوع المستخدم:</strong> مريض</p>
                        <p><strong>الجنس:</strong> {{ $user->patient->gender ?? '-' }}</p>
                    @endif
                    @if($user->doctor)
                        <p><strong>نوع المستخدم:</strong> طبيب</p>
                        <p><strong>التخصص:</strong> {{ $user->doctor->specialty->name ?? '-' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($bookings && $bookings->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">الحجوزات</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الطبيب</th>
                            <th>المريض</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $booking->doctor->user->name ?? '-' }}</td>
                                <td>{{ $booking->patient->user->name ?? '-' }}</td>
                                <td>{{ $booking->date_time ? $booking->date_time->format('Y-m-d H:i') : '-' }}</td>
                                <td>{{ $booking->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

