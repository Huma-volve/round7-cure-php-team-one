@extends('admin.master')
@section('title', __('مرضاي'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('مرضاي') }}</h1>
        <span class="text-muted">{{ $doctor->user->name }}</span>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('الاسم') }}</th>
                            <th>{{ __('البريد الإلكتروني') }}</th>
                            <th>{{ __('رقم الهاتف') }}</th>
                            <th>{{ __('آخر موعد') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                            <tr>
                                <td>{{ $patient->user->name ?? '-' }}</td>
                                <td>{{ $patient->user->email ?? '-' }}</td>
                                <td>{{ $patient->user->mobile ?? '-' }}</td>
                                <td>{{ optional($patient->bookings()->where('doctor_id', $doctor->id)->latest('date_time')->first())->date_time?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">{{ __('لا يوجد مرضى مسجلين بعد') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($patients->hasPages())
            <div class="card-footer">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

