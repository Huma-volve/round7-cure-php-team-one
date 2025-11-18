@extends('admin.master')
@section('title', __('أوقات العمل'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('تحديث أوقات العمل') }}</h1>
        <span class="text-muted">{{ $doctor->user->name }}</span>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('حدد وقت البداية والنهاية لكل يوم') }}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('doctor.schedule.update') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('اليوم') }}</th>
                                <th>{{ __('وقت البداية') }}</th>
                                <th>{{ __('وقت النهاية') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($days = [
                                'saturday' => 'السبت',
                                'sunday' => 'الأحد',
                                'monday' => 'الإثنين',
                                'tuesday' => 'الثلاثاء',
                                'wednesday' => 'الأربعاء',
                                'thursday' => 'الخميس',
                                'friday' => 'الجمعة',
                            ])
                            @foreach($days as $day => $label)
                                @php
                                    $slot = $schedule[$day][0] ?? null;
                                    if(is_string($slot) && str_contains($slot, '-')) {
                                        [$startVal, $endVal] = explode('-', $slot);
                                    } else {
                                        $startVal = $slot['start'] ?? null;
                                        $endVal = $slot['end'] ?? null;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td>
                                        <input type="time" class="form-control @error('schedule.' . $day . '.start') is-invalid @enderror"
                                            name="schedule[{{ $day }}][start]" value="{{ old('schedule.' . $day . '.start', $startVal) }}">
                                    </td>
                                    <td>
                                        <input type="time" class="form-control @error('schedule.' . $day . '.end') is-invalid @enderror"
                                            name="schedule[{{ $day }}][end]" value="{{ old('schedule.' . $day . '.end', $endVal) }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('حفظ أوقات العمل') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

