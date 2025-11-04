@extends('admin.master')
@section('title', 'Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي المستخدمين</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        <div class="text-xs text-muted mt-1">مرضى: {{ $totalPatients }} | أطباء: {{ $totalDoctors }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">إجمالي الحجوزات</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookings }}</div>
                        <div class="text-xs text-muted mt-1">مؤكدة: {{ $confirmedBookings }} | معلقة: {{ $pendingBookings }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إجمالي الإيرادات (شهري)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($monthlyPayments, 2) }} EGP</div>
                        <div class="text-xs text-muted mt-1">اليوم: {{ number_format($todayPayments, 2) }} | السنة: {{ number_format($yearlyPayments, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disputes Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">النزاعات المعلقة</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $openDisputes }}</div>
                        <div class="text-xs text-muted mt-1">محلولة: {{ $resolvedDisputes }} | مرفوضة: {{ $rejectedDisputes }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Area Chart - Bookings by Month -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">الحجوزات حسب الشهر (آخر 6 أشهر)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="bookingsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart - Booking Status -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">توزيع حالات الحجوزات</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="bookingStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Area Chart - Payments by Month -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">المدفوعات حسب الشهر (آخر 6 أشهر)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="paymentsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart - Payment Gateway -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">توزيع بوابات الدفع</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="paymentGatewayChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Bookings -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">الحجوزات القادمة</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الطبيب</th>
                                <th>المريض</th>
                                <th>التاريخ والوقت</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingBookings as $booking)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $booking->doctor->user->name ?? '-' }}</td>
                                    <td>{{ $booking->patient->user->name ?? '-' }}</td>
                                    <td>{{ $booking->date_time ? $booking->date_time->format('Y-m-d H:i') : '-' }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ $booking->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد حجوزات قادمة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Bookings Chart
    const bookingsCtx = document.getElementById('bookingsChart');
    if (bookingsCtx) {
        const bookingsData = @json($bookingsByMonth ?? []);
        if (bookingsData && bookingsData.length > 0) {
            const bookingsLabels = bookingsData.map(item => {
                const monthNames = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                return monthNames[item.month - 1] + ' ' + item.year;
            });
            const bookingsCounts = bookingsData.map(item => item.count);

            new Chart(bookingsCtx, {
                type: 'line',
                data: {
                    labels: bookingsLabels,
                    datasets: [{
                        label: 'عدد الحجوزات',
                        data: bookingsCounts,
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }
    }

    // Payments Chart
    const paymentsCtx = document.getElementById('paymentsChart');
    if (paymentsCtx) {
        const paymentsData = @json($paymentsByMonth ?? []);
        if (paymentsData && paymentsData.length > 0) {
            const paymentsLabels = paymentsData.map(item => {
                const monthNames = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                return monthNames[item.month - 1] + ' ' + item.year;
            });
            const paymentsAmounts = paymentsData.map(item => item.total);

            new Chart(paymentsCtx, {
                type: 'line',
                data: {
                    labels: paymentsLabels,
                    datasets: [{
                        label: 'المبلغ (EGP)',
                        data: paymentsAmounts,
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                        pointBorderColor: 'rgba(28, 200, 138, 1)',
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: 'rgba(28, 200, 138, 1)',
                        pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }
    }

    // Booking Status Chart
    const statusCtx = document.getElementById('bookingStatusChart');
    if (statusCtx) {
        const bookingStatusData = @json($bookingStatusData ?? []);
        if (bookingStatusData && Object.keys(bookingStatusData).length > 0) {
            const statusLabels = Object.keys(bookingStatusData);
            const statusCounts = Object.values(bookingStatusData);

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#c0392b'],
                        hoverBorderColor: 'rgba(234, 236, 244, 1)',
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: 'rgb(255,255,255)',
                        bodyFontColor: '#858796',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                }
            });
        }
    }

    // Payment Gateway Chart
    const gatewayCtx = document.getElementById('paymentGatewayChart');
    if (gatewayCtx) {
        const paymentGatewayData = @json($paymentGatewayData ?? []);
        if (paymentGatewayData && Object.keys(paymentGatewayData).length > 0) {
            const gatewayLabels = Object.keys(paymentGatewayData);
            const gatewayCounts = Object.values(paymentGatewayData);

            new Chart(gatewayCtx, {
                type: 'doughnut',
                data: {
                    labels: gatewayLabels,
                    datasets: [{
                        data: gatewayCounts,
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
                        hoverBorderColor: 'rgba(234, 236, 244, 1)',
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: 'rgb(255,255,255)',
                        bodyFontColor: '#858796',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                }
            });
        }
    }
});
</script>
@endpush
@endsection
