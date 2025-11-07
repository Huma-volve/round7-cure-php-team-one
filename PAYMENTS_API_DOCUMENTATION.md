# 💳 Payments API Documentation - Complete Guide

## 📌 Base URL
```
http://your-domain.com/api
```

## 🔐 Authentication
جميع الـ Endpoints تحتاج إلى:
```http
Authorization: Bearer {your-token}
Content-Type: application/json
Accept: application/json
Accept-Language: ar|en
```

**ملاحظة:** Webhooks endpoints لا تحتاج Authentication (يتم التحقق عبر Signature)

**Localization:** يمكنك تحديد اللغة المطلوبة للاستجابة باستخدام header `Accept-Language`:
- `Accept-Language: ar` - للاستجابة بالعربية
- `Accept-Language: en` - للاستجابة بالإنجليزية (الافتراضي)
- إذا لم يتم تحديد اللغة، سيتم استخدام الإنجليزية كافتراضي

---

## 💰 Payment Gateways

القيم المتاحة:
- `"stripe"` - الدفع عبر Stripe
- `"paypal"` - الدفع عبر PayPal
- `"cash"` - الدفع نقداً (في العيادة)

---

## 📊 Payment Status

القيم المتاحة:
- `"pending"` - معلق (في انتظار الدفع)
- `"processing"` - قيد المعالجة
- `"success"` - ناجح (تم الدفع بنجاح)
- `"failed"` - فشل (فشل عملية الدفع)

---

## 💵 Currency Format

- **Currency Code**: يجب أن يكون 3 أحرف (مثال: `USD`, `EUR`, `SAR`)
- **Amount**: رقم عشري (مثال: `200.00`, `150.50`)
- **Minimum Amount**: `0.50` (أقل مبلغ مسموح)

---

## 🚀 API Endpoints

### 1. Create Payment Intent
**POST** `/api/payments/create-intent`

> **Description:** إنشاء عملية دفع جديدة للحجز

#### Request Body:
```json
{
  "booking_id": 123,
  "gateway": "stripe",
  "currency": "USD",
  "amount": 200.00,
  "description": "Booking #123 with Dr. Ahmed",
  "return_url": "https://app.example.com/paypal/return",
  "cancel_url": "https://app.example.com/paypal/cancel"
}
```

#### Field Details:

| Field | Type | Required | Description | Validation |
|-------|------|----------|-------------|------------|
| `booking_id` | integer | ✅ Yes | رقم الحجز | Must exist in `bookings` table |
| `gateway` | string (enum) | ✅ Yes | طريقة الدفع | Must be one of: `stripe`, `paypal`, `cash` |
| `currency` | string | ✅ Yes | العملة | Must be 3 characters (e.g., `USD`, `EUR`) |
| `amount` | number | ✅ Yes | المبلغ | Must be >= 0.50 |
| `description` | string | ❌ No | وصف الدفع | Max 255 characters |
| `return_url` | string (URL) | ⚠️ Required for PayPal | رابط العودة بعد نجاح الدفع (PayPal فقط) | Valid URL format |
| `cancel_url` | string (URL) | ⚠️ Required for PayPal | رابط العودة بعد إلغاء الدفع (PayPal فقط) | Valid URL format |

#### 📍 من أين تأتي `return_url` و `cancel_url`؟

**المصدر:** هذه الـ URLs تأتي من **Frontend/Mobile App** وتُرسل في Request Body.

**الغرض:**
- `return_url`: رابط الصفحة/الشاشة في تطبيقك التي سيُعاد إليها المستخدم **بعد نجاح الدفع**
- `cancel_url`: رابط الصفحة/الشاشة في تطبيقك التي سيُعاد إليها المستخدم **إذا ألغى الدفع**

**أمثلة على الـ URLs:**

**Web Application:**
```json
{
  "return_url": "https://yourdomain.com/payment/success?booking_id=123",
  "cancel_url": "https://yourdomain.com/payment/cancel?booking_id=123"
}
```

**Mobile App (Deep Links):**
```json
{
  "return_url": "myapp://payment/success?booking_id=123",
  "cancel_url": "myapp://payment/cancel?booking_id=123"
}
```

**React Native / Flutter:**
```json
{
  "return_url": "exp://192.168.1.1:8081/--/payment-success",
  "cancel_url": "exp://192.168.1.1:8081/--/payment-cancel"
}
```

**ملاحظات مهمة:**
- ✅ **لـ PayPal**: **مطلوبة** (`required_if:gateway,paypal`) - يجب إرسال `return_url` و `cancel_url`
- ❌ **لـ Stripe**: غير مطلوبة (التأكيد يتم عبر Stripe SDK داخل التطبيق)
- ❌ **لـ Cash**: غير مطلوبة
- ⚠️ يجب أن تكون URLs صحيحة ومتاحة (valid URLs)

#### Validation Rules:
- ✅ `booking_id` must exist in database
- ✅ `gateway` must be valid enum value
- ✅ `currency` must be exactly 3 characters
- ✅ `amount` must be >= 0.50
- ✅ `return_url` and `cancel_url` are **required if gateway = paypal**
- ✅ `return_url` and `cancel_url` must be valid URLs (if provided)

#### Gateway-Specific Notes:

**Stripe:**
- `return_url` and `cancel_url` غير مطلوبة (التأكيد يتم عبر Stripe SDK)
- الاستجابة تحتوي على `client_secret` للاستخدام مع Stripe SDK

**PayPal:**
- ⚠️ **يُفضّل إرسال `return_url` و `cancel_url`** لتجربة أفضل
- هذه الـ URLs تأتي من Frontend/Mobile App (روابط صفحاتك/شاشاتك)
- `return_url`: صفحة النجاح (بعد إتمام الدفع)
- `cancel_url`: صفحة الإلغاء (إذا ألغى المستخدم)
- الاستجابة تحتوي على `approve_url` لإرسال المستخدم إلى PayPal

**Cash:**
- لا يتطلب SDK أو URLs
- يتم إنشاء سجل دفع `pending` فقط
- الدفع يتم خارج النظام (في العيادة)

#### Success Response (201 Created):

**Stripe Response:**
```json
{
  "success": true,
  "status": 201,
  "message": "تم إنشاء عملية الدفع",
  "data": {
    "id": 55,
    "booking_id": 123,
    "amount": 200.0,
    "transaction_id": "pi_abc123xyz",
    "gateway": "stripe",
    "status": "pending",
    "created_at": "2025-10-29 21:10:00",
    "updated_at": "2025-10-29 21:10:00"
  }
}
```

**PayPal Response:**
```json
{
  "success": true,
  "status": 201,
  "message": "تم إنشاء عملية الدفع",
  "data": {
    "id": 56,
    "booking_id": 123,
    "amount": 200.0,
    "transaction_id": "PAYPAL_ORDER_ID_123",
    "gateway": "paypal",
    "status": "pending",
    "created_at": "2025-10-29 21:10:00",
    "updated_at": "2025-10-29 21:10:00"
  }
}
```

**Cash Response:**
```json
{
  "success": true,
  "status": 201,
  "message": "تم إنشاء عملية الدفع",
  "data": {
    "id": 57,
    "booking_id": 123,
    "amount": 200.0,
    "transaction_id": "cash_6534f...",
    "gateway": "cash",
    "status": "pending",
    "created_at": "2025-10-29 21:10:00",
    "updated_at": "2025-10-29 21:10:00"
  }
}
```

#### Error Responses:

**422 Validation Error:**
```json
{
  "success": false,
  "status": 422,
  "message": "البيانات المرسلة غير صحيحة",
  "errors": {
    "booking_id": ["الحجز المحدد غير موجود"],
    "gateway": ["طريقة الدفع غير صحيحة"],
    "amount": ["المبلغ يجب أن يكون أكبر من 0.50"]
  }
}
```

**404 Not Found (Booking):**
```json
{
  "success": false,
  "status": 404,
  "message": "الحجز المحدد غير موجود"
}
```

**401 Unauthorized:**
```json
{
  "success": false,
  "status": 401,
  "message": "غير مصرح لك بالوصول"
}
```

---

### 2. Confirm Payment
**POST** `/api/payments/confirm`

> **Description:** تأكيد عملية الدفع بعد إتمامها من قبل المستخدم

#### Request Body:
```json
{
  "gateway": "stripe",
  "payment_id": "pi_abc123xyz"
}
```

#### Field Details:

| Field | Type | Required | Description | Validation |
|-------|------|----------|-------------|------------|
| `gateway` | string (enum) | ✅ Yes | طريقة الدفع | Must be one of: `stripe`, `paypal` |
| `payment_id` | string | ✅ Yes | رقم الدفع من Gateway | Transaction ID من Stripe/PayPal |

#### Validation Rules:
- ✅ `gateway` must be `stripe` or `paypal` (Cash payments are handled offline - لا تحتاج تأكيد عبر API)
- ✅ `payment_id` must be valid transaction ID

#### Gateway-Specific Notes:

**Stripe:**
- `payment_id` هو `payment_intent_id` من Stripe (يبدأ بـ `pi_`)
- يتم التحقق من حالة الدفع مع Stripe API

**PayPal:**
- `payment_id` هو `order_id` من PayPal
- يتم تنفيذ `capture` للطلب

**Cash:**
- لا يمكن تأكيد `cash` عبر هذا الـ endpoint
- يتم التحديث يدوياً من قبل Admin

#### Success Response (200 OK):
```json
{
  "success": true,
  "status": 200,
  "message": "تم تأكيد عملية الدفع",
  "data": {
    "status": "succeeded",
    "provider": "stripe",
    "payment_id": "pi_abc123xyz"
  }
}
```

#### Error Responses:

**422 Validation Error:**
```json
{
  "success": false,
  "status": 422,
  "message": "البيانات المرسلة غير صحيحة",
  "errors": {
    "gateway": ["طريقة الدفع غير صحيحة"],
    "payment_id": ["رقم الدفع مطلوب"]
  }
}
```

**404 Not Found (Payment):**
```json
{
  "success": false,
  "status": 404,
  "message": "عملية الدفع غير موجودة"
}
```

**400 Bad Request (Payment Failed):**
```json
{
  "success": false,
  "status": 400,
  "message": "فشل في تأكيد عملية الدفع"
}
```

---

### 3. Get Payment Details
**GET** `/api/payments/{id}`

> **Description:** عرض تفاصيل عملية دفع معينة

#### URL Parameters:
- `id` (required, integer) - رقم عملية الدفع

#### Success Response (200 OK):
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب تفاصيل الدفع",
  "data": {
    "id": 55,
    "booking_id": 123,
    "amount": 200.0,
    "transaction_id": "pi_abc123xyz",
    "gateway": "stripe",
    "status": "success",
    "created_at": "2025-10-29 21:10:00",
    "updated_at": "2025-10-29 21:15:30"
  }
}
```

#### Error Responses:

**404 Not Found:**
```json
{
  "success": false,
  "status": 404,
  "message": "عملية الدفع غير موجودة"
}
```

**403 Forbidden:**
```json
{
  "success": false,
  "status": 403,
  "message": "غير مصرح لك بعرض هذه العملية"
}
```

---

### 4. Webhooks (Stripe)
**POST** `/api/webhooks/stripe`

> **Description:** Webhook endpoint لاستقبال إشعارات Stripe (لا يحتاج Authentication)

#### Headers Required:
```http
Stripe-Signature: {stripe-signature}
Content-Type: application/json
```

#### Request Body:
يتم إرسال البيانات من Stripe تلقائياً:
```json
{
  "type": "payment_intent.succeeded",
  "data": {
    "object": {
      "id": "pi_abc123xyz",
      "status": "succeeded",
      "amount": 20000,
      "currency": "usd"
    }
  }
}
```

#### Success Response (200 OK):
```json
{
  "success": true,
  "status": 200,
  "message": "webhook processed",
  "data": {
    "provider": "stripe",
    "status": "processing"
  }
}
```

#### Notes:
- ✅ يتم التحقق من `Stripe-Signature` قبل المعالجة
- ✅ يتم تحديث حالة الدفع والحجز تلقائياً
- ✅ لا يحتاج إلى Authorization header

---

### 5. Webhooks (PayPal)
**POST** `/api/webhooks/paypal`

> **Description:** Webhook endpoint لاستقبال إشعارات PayPal (لا يحتاج Authentication)

#### Headers Required:
```http
PayPal-Transmission-Sig: {paypal-signature}
PayPal-Transmission-Id: {transmission-id}
PayPal-Transmission-Time: {timestamp}
Content-Type: application/json
```

#### Request Body:
يتم إرسال البيانات من PayPal تلقائياً:
```json
{
  "event_type": "CHECKOUT.ORDER.APPROVED",
  "resource": {
    "id": "PAYPAL_ORDER_ID_123",
    "status": "APPROVED"
  }
}
```

#### Success Response (200 OK):
```json
{
  "success": true,
  "status": 200,
  "message": "webhook processed",
  "data": {
    "provider": "paypal",
    "status": "processing"
  }
}
```

#### Notes:
- ✅ يتم التحقق من PayPal Signature قبل المعالجة
- ✅ يتم تحديث حالة الدفع والحجز تلقائياً
- ✅ لا يحتاج إلى Authorization header

---

## 📋 Response Structure

### Standard Response Format:
```json
{
  "success": true/false,
  "status": 200/201/400/401/403/404/422/500,
  "message": "رسالة بالعربية",
  "data": { ... }
}
```

### Payment Resource Structure:
```json
{
  "id": 55,
  "booking_id": 123,
  "amount": 200.0,
  "transaction_id": "pi_abc123xyz",
  "gateway": "stripe",
  "status": "success",
  "created_at": "2025-10-29 21:10:00",
  "updated_at": "2025-10-29 21:15:30"
}
```

### Error Response Format:
```json
{
  "success": false,
  "status": 422,
  "message": "البيانات المرسلة غير صحيحة",
  "errors": {
    "field_name": ["خطأ 1", "خطأ 2"]
  }
}
```

---

## 🔄 Payment Flow

### Stripe Flow:
1. **Frontend/Mobile** → `POST /api/payments/create-intent` (مع `gateway=stripe`)
2. **Backend** → ينشئ PaymentIntent ويعيد `transaction_id` و `client_secret`
3. **Frontend/Mobile** → يستخدم Stripe SDK لتأكيد الدفع باستخدام `client_secret`
4. **Stripe** → يرسل Webhook → `POST /api/webhooks/stripe`
5. **Backend** → يحدّث حالة الدفع والحجز تلقائياً

### PayPal Flow:
1. **Frontend/Mobile** → `POST /api/payments/create-intent` (مع `gateway=paypal`)
2. **Backend** → ينشئ PayPal Order ويعيد `transaction_id` و `approve_url`
3. **Frontend/Mobile** → يرسل المستخدم إلى `approve_url` (PayPal Website)
4. **PayPal** → المستخدم يوافق ويعود إلى `return_url`
5. **Frontend/Mobile** → `POST /api/payments/confirm` (مع `payment_id`)
6. **Backend** → ينفذ `capture` للطلب ويحدّث الحالة
7. **PayPal** → قد يرسل Webhook → `POST /api/webhooks/paypal`

### Cash Flow:
1. **Frontend/Mobile** → `POST /api/payments/create-intent` (مع `gateway=cash`)
2. **Backend** → ينشئ سجل دفع `pending`
3. **Admin/Staff** → يحدّث الحالة يدوياً بعد استلام النقد

---

## 🔑 Important Notes

### 1. Amount Calculation:
- ⚠️ **مهم**: لا تثق بالمبلغ القادم من العميل
- ✅ يُفضّل إعادة احتساب المبلغ من بيانات الحجز في السيرفر
- ✅ المبلغ يجب أن يكون >= 0.50

### 2. Security:
- ✅ جميع Payments تحتاج Authentication (ما عدا Webhooks)
- ✅ Webhooks يتم التحقق منها عبر Signature
- ✅ لا تعرض المفاتيح السرية (`client_secret`) في Logs

### 3. Status Flow:
```
pending → processing → success ✅
pending → failed ❌
```

### 4. Booking Integration:
- عند نجاح الدفع (`success`):
  - `payments.status = success`
  - `bookings.status = confirmed` (يتم تلقائياً)
- عند فشل الدفع (`failed`):
  - `payments.status = failed`
  - `bookings.status = pending` (يبقى معلق)

### 5. Gateway Differences:

| Feature | Stripe | PayPal | Cash |
|---------|--------|--------|------|
| SDK Required | ✅ Yes | ❌ No | ❌ No |
| Client Secret | ✅ Yes | ❌ No | ❌ No |
| Approve URL | ❌ No | ✅ Yes | ❌ No |
| Webhook | ✅ Yes | ✅ Yes | ❌ No |
| Return URL | ❌ No | ✅ Recommended | ❌ No |

---

## 📱 Mobile App Integration Examples

### Flutter/Dart Example:

#### Create Payment Intent (Stripe):
```dart
final response = await http.post(
  Uri.parse('$baseUrl/api/payments/create-intent'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'booking_id': 123,
    'gateway': 'stripe',
    'currency': 'USD',
    'amount': 200.00,
    'description': 'Booking #123',
  }),
);

final data = jsonDecode(response.body);
if (data['success']) {
  final payment = data['data'];
  final transactionId = payment['transaction_id'];
  final gateway = payment['gateway'];
  
  // Use Stripe SDK with transaction_id
  // await stripe.confirmPayment(transactionId);
}
```

#### Confirm Payment (Stripe):
```dart
final response = await http.post(
  Uri.parse('$baseUrl/api/payments/confirm'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'gateway': 'stripe',
    'payment_id': 'pi_abc123xyz',
  }),
);

final data = jsonDecode(response.body);
if (data['success']) {
  print('Payment confirmed: ${data['data']['status']}');
}
```

#### Create Payment Intent (PayPal):
```dart
// تحديد روابط الصفحات/الشاشات في تطبيقك
const returnUrl = 'myapp://payment/success?booking_id=123';
const cancelUrl = 'myapp://payment/cancel?booking_id=123';

final response = await http.post(
  Uri.parse('$baseUrl/api/payments/create-intent'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'booking_id': 123,
    'gateway': 'paypal',
    'currency': 'USD',
    'amount': 200.00,
    'return_url': returnUrl,  // رابط شاشة النجاح في تطبيقك
    'cancel_url': cancelUrl,  // رابط شاشة الإلغاء في تطبيقك
  }),
);

final data = jsonDecode(response.body);
if (data['success']) {
  final payment = data['data'];
  // Get approve_url from payment response and redirect user
  // final approveUrl = payment['approve_url'];
  // await launchUrl(Uri.parse(approveUrl));
}
```

---

### React Native Example:

#### Create Payment Intent:
```javascript
const response = await fetch(`${baseUrl}/api/payments/create-intent`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    booking_id: 123,
    gateway: 'stripe',
    currency: 'USD',
    amount: 200.00,
    description: 'Booking #123',
  }),
});

const data = await response.json();
if (data.success) {
  const payment = data.data;
  console.log('Transaction ID:', payment.transaction_id);
  // Use Stripe React Native SDK
}
```

#### Confirm Payment:
```javascript
const response = await fetch(`${baseUrl}/api/payments/confirm`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    gateway: 'stripe',
    payment_id: 'pi_abc123xyz',
  }),
});

const data = await response.json();
if (data.success) {
  console.log('Payment status:', data.data.status);
}
```

---

### React/Next.js Example:

#### Create Payment Intent (Stripe):
```typescript
const createPayment = async (bookingId: number, amount: number) => {
  const response = await fetch('/api/payments/create-intent', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      booking_id: bookingId,
      gateway: 'stripe',
      currency: 'USD',
      amount: amount,
      description: `Booking #${bookingId}`,
    }),
  });

  const data = await response.json();
  if (data.success) {
    return data.data;
  }
  throw new Error(data.message);
};
```

#### Use with Stripe.js:
```typescript
import { loadStripe } from '@stripe/stripe-js';

const stripe = await loadStripe('pk_test_...');
const payment = await createPayment(123, 200.00);

const result = await stripe.confirmCardPayment(payment.client_secret, {
  payment_method: {
    card: cardElement,
  },
});

if (result.error) {
  console.error(result.error);
} else {
  // Confirm with backend
  await fetch('/api/payments/confirm', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      gateway: 'stripe',
      payment_id: result.paymentIntent.id,
    }),
  });
}
```

---

## ✅ Summary Checklist for Frontend Team

- [ ] Use correct gateway values: `stripe`, `paypal`, `cash`
- [ ] Always include `Authorization: Bearer {token}` header
- [ ] Set `Content-Type: application/json` for POST requests
- [ ] Currency must be 3 characters (e.g., `USD`, `EUR`)
- [ ] Amount must be >= 0.50
- [ ] Handle validation errors (422) properly
- [ ] For Stripe: Use `client_secret` with Stripe SDK
- [ ] For PayPal: Redirect user to `approve_url`
- [ ] After payment confirmation, call `/api/payments/confirm`
- [ ] Check payment `status` before updating UI
- [ ] Handle payment failures gracefully
- [ ] Don't expose sensitive keys (keep `client_secret` secure)

---

## 🔗 Related Endpoints

- **Create Booking with Payment**: `POST /api/patient/bookings` (see Booking API Documentation)
- **Get Booking**: `GET /api/patient/bookings/{id}`

---

**Last Updated:** 2025-11-02  
**Version:** 1.1.0

