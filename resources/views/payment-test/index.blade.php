<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تجربة الدفع - Payment Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .payment-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            margin-bottom: 20px;
        }
        .gateway-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 5px;
        }
        .badge-stripe {
            background: #635BFF;
            color: white;
        }
        .badge-paypal {
            background: #0070BA;
            color: white;
        }
        .badge-cash {
            background: #28a745;
            color: white;
        }
        .result-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            word-break: break-all;
        }
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="payment-card">
                    <div class="text-center mb-4">
                        <h1 class="display-4 mb-2">
                            <i class="fas fa-credit-card text-primary"></i>
                        </h1>
                        <h2 class="mb-3">تجربة خدمات الدفع</h2>
                        <p class="text-muted">اختبر خدمات الدفع المتاحة: Stripe, PayPal, Cash</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Create Payment Intent Form -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-plus-circle"></i> إنشاء عملية دفع جديدة</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('payment-test.create-intent') }}" id="paymentForm">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">رقم الحجز <span class="text-danger">*</span></label>
                                        <select name="booking_id" class="form-select" required>
                                            <option value="">اختر حجز...</option>
                                            @foreach($bookings as $booking)
                                                <option value="{{ $booking->id }}">
                                                    #{{ $booking->id }} - {{ $booking->patient->user->name ?? 'N/A' }} 
                                                    ({{ $booking->date_time ? $booking->date_time->format('Y-m-d H:i') : 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">أو أدخل رقم حجز موجود</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                                        <select name="gateway" class="form-select" required id="gatewaySelect">
                                            <option value="">اختر طريقة الدفع...</option>
                                            <option value="stripe">Stripe</option>
                                            <option value="paypal">PayPal</option>
                                            <option value="cash">Cash (نقداً)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" class="form-control" 
                                               step="0.01" min="0.5" value="100.00" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">العملة <span class="text-danger">*</span></label>
                                        <select name="currency" class="form-select" required>
                                            <option value="USD">USD</option>
                                            <option value="EUR">EUR</option>
                                            <option value="EGP" selected>EGP</option>
                                            <option value="SAR">SAR</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">الوصف</label>
                                        <input type="text" name="description" class="form-control" 
                                               placeholder="وصف الدفع (اختياري)">
                                    </div>
                                </div>

                                <!-- Return/Cancel URLs (shown for PayPal and Stripe) -->
                                <div id="paymentUrls" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Return URL (بعد نجاح الدفع) <span class="text-danger">*</span></label>
                                            <input type="url" name="return_url" class="form-control" 
                                                   value="{{ url('/payment-test?payment_success=true') }}" placeholder="https://...">
                                            <small class="text-muted">سيتم إعادة توجيه المستخدم هنا بعد إتمام الدفع</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Cancel URL (بعد إلغاء الدفع) <span class="text-danger">*</span></label>
                                            <input type="url" name="cancel_url" class="form-control" 
                                                   value="{{ url('/payment-test?payment_cancelled=true') }}" placeholder="https://...">
                                            <small class="text-muted">سيتم إعادة توجيه المستخدم هنا إذا ألغى الدفع</small>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane"></i> إنشاء عملية الدفع
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Payment Response -->
                    @if(session('payment_response'))
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-check-circle"></i> نتيجة إنشاء الدفع</h5>
                            </div>
                            <div class="card-body">
                                <div class="result-box">
                                    <strong>Payment ID:</strong> {{ session('payment_response.payment_id') ?? 'N/A' }}<br>
                                    <strong>Status:</strong> 
                                    <span class="badge bg-{{ session('payment_response.status') == 'succeeded' ? 'success' : 'warning' }}">
                                        {{ session('payment_response.status') ?? 'N/A' }}
                                    </span>
                                </div>

                                @if(session('payment_response.approve_url') && session('payment')->gateway == 'stripe')
                                    <div class="result-box mt-3">
                                        <strong><i class="fab fa-stripe"></i> Stripe Checkout URL:</strong>
                                        <div class="code-block mt-2">{{ session('payment_response.approve_url') }}</div>
                                        <small class="text-muted">سيتم إعادة توجيهك إلى صفحة Stripe الرسمية لإدخال بيانات البطاقة</small>
                                    </div>

                                    <!-- Stripe Checkout Redirect -->
                                    <div class="card mt-4" style="border: 2px solid #635BFF;">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fab fa-stripe"></i> الدفع عبر Stripe</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <p class="mb-4">سيتم إعادة توجيهك إلى صفحة Stripe الرسمية لإدخال بيانات البطاقة بشكل آمن</p>
                                            <a href="{{ session('payment_response.approve_url') }}" 
                                               class="btn btn-primary btn-lg" target="_blank">
                                                <i class="fab fa-stripe"></i> اذهب إلى صفحة الدفع في Stripe
                                            </a>
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-shield-alt"></i> بيانات البطاقة محمية ومشفرة بواسطة Stripe
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(session('payment_response.approve_url') && session('payment')->gateway == 'paypal')
                                    <div class="result-box mt-3">
                                        <strong><i class="fab fa-paypal"></i> PayPal Approve URL:</strong>
                                        <div class="code-block mt-2">{{ session('payment_response.approve_url') }}</div>
                                        <small class="text-muted">سيتم إعادة توجيهك إلى صفحة PayPal الرسمية لإتمام الدفع</small>
                                    </div>

                                    <!-- PayPal Redirect -->
                                    <div class="card mt-4" style="border: 2px solid #0070BA;">
                                        <div class="card-header text-white" style="background: #0070BA;">
                                            <h5 class="mb-0"><i class="fab fa-paypal"></i> الدفع عبر PayPal</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <p class="mb-4">سيتم إعادة توجيهك إلى صفحة PayPal الرسمية لإتمام الدفع</p>
                                            <a href="{{ session('payment_response.approve_url') }}" 
                                               class="btn btn-primary mt-2" style="background: #0070BA; border-color: #0070BA;" target="_blank">
                                                <i class="fab fa-paypal"></i> اذهب إلى PayPal
                                            </a>
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-shield-alt"></i> بيانات الدفع محمية ومشفرة بواسطة PayPal
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(session('payment'))
                                    <div class="result-box mt-3">
                                        <strong>Payment Details:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Payment ID: #{{ session('payment')->id }}</li>
                                            <li>Booking ID: #{{ session('payment')->booking_id }}</li>
                                            <li>Amount: {{ session('payment')->amount }} {{ session('payment')->currency ?? 'EGP' }}</li>
                                            <li>Transaction ID: 
                                                <code>{{ session('payment')->transaction_id }}</code>
                                            </li>
                                            @if(session('payment_response.payment_intent_id'))
                                                <li>Payment Intent ID: 
                                                    <code>{{ session('payment_response.payment_intent_id') }}</code>
                                                </li>
                                            @endif
                                            <li>Gateway: 
                                                <span class="gateway-badge badge-{{ session('payment')->gateway }}">
                                                    {{ session('payment')->gateway }}
                                                </span>
                                            </li>
                                            <li>Status: 
                                                <span class="badge bg-{{ session('payment')->status == 'success' ? 'success' : (session('payment')->status == 'failed' ? 'danger' : 'warning') }}">
                                                    {{ session('payment')->status }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Confirm Payment Form -->
                    @if(session('payment_response') && session('payment_response.payment_id'))
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-check-double"></i> تأكيد الدفع</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('payment-test.confirm') }}">
                                    @csrf
                                    <input type="hidden" name="gateway" value="{{ session('payment')->gateway ?? 'stripe' }}">
                                    <input type="hidden" name="payment_id" value="{{ session('payment_response.payment_id') }}">
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        <strong>للتأكيد:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>بعد إتمام الدفع في Stripe أو PayPal، سيتم إعادة توجيهك تلقائياً</li>
                                            <li>أو استخدم هذا النموذج يدوياً بعد إتمام الدفع</li>
                                            <li>Payment ID: <code>{{ session('payment_response.payment_id') }}</code></li>
                                        </ul>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-check"></i> تأكيد الدفع
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Confirmation Result -->
                    @if(session('confirmation'))
                        <div class="card mt-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-check-circle"></i> نتيجة التأكيد</h5>
                            </div>
                            <div class="card-body">
                                <div class="result-box">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-{{ session('confirmation.successful') ? 'success' : 'danger' }}">
                                        {{ session('confirmation.status') }}
                                    </span><br>
                                    <strong>Provider:</strong> {{ session('confirmation.provider') }}<br>
                                    <strong>Payment ID:</strong> {{ session('confirmation.payment_id') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- API Documentation -->
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-book"></i> معلومات API</h5>
                        </div>
                        <div class="card-body">
                            <h6>Endpoints المتاحة:</h6>
                            <ul>
                                <li><code>POST /api/payments/create-intent</code> - إنشاء عملية دفع</li>
                                <li><code>POST /api/payments/confirm</code> - تأكيد الدفع</li>
                                <li><code>GET /api/payments/{id}</code> - عرض تفاصيل الدفع</li>
                            </ul>
                            <h6 class="mt-3">Gateways المدعومة:</h6>
                            <div>
                                <span class="gateway-badge badge-stripe">Stripe</span>
                                <span class="gateway-badge badge-paypal">PayPal</span>
                                <span class="gateway-badge badge-cash">Cash</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide Payment URLs based on gateway selection
        const gatewaySelect = document.getElementById('gatewaySelect');
        if (gatewaySelect) {
            gatewaySelect.addEventListener('change', function() {
                const paymentUrls = document.getElementById('paymentUrls');
                if (this.value === 'paypal' || this.value === 'stripe') {
                    paymentUrls.style.display = 'block';
                    paymentUrls.querySelectorAll('input').forEach(input => {
                        input.required = true;
                    });
                } else {
                    paymentUrls.style.display = 'none';
                    paymentUrls.querySelectorAll('input').forEach(input => {
                        input.required = false;
                    });
                }
            });
        }

        // Check if payment was successful or cancelled
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('payment_success') === 'true') {
            const sessionId = urlParams.get('session_id');
            if (sessionId) {
                // Auto-confirm Stripe payment
                setTimeout(() => {
                    const confirmForm = document.querySelector('form[action*="confirm"]');
                    if (confirmForm) {
                        // Update payment_id to session_id
                        const paymentIdInput = confirmForm.querySelector('input[name="payment_id"]');
                        if (paymentIdInput) {
                            paymentIdInput.value = sessionId;
                        }
                        confirmForm.submit();
                    }
                }, 1000);
            }
        }
        
        if (urlParams.get('payment_cancelled') === 'true') {
            alert('تم إلغاء عملية الدفع.');
        }
    </script>
</body>
</html>

