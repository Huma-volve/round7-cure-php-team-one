# 📋 Booking API Documentation - Complete Guide

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
```

---

## 📅 Date & Time Formats

### المطلوب من Frontend:

**Format:** `Y-m-d H:i:s` (MySQL DateTime format)

**Examples:**
- ✅ Correct: `"2025-11-15 10:00:00"`
- ✅ Correct: `"2025-12-25 14:30:00"`
- ❌ Wrong: `"2025-11-15T10:00:00Z"` (ISO format)
- ❌ Wrong: `"15/11/2025 10:00"` (different format)

### الاستجابة من Backend:

1. **date_time** (Raw): `"2025-11-15 10:00:00"` (Y-m-d H:i:s)
2. **date_time_formatted** (Human Readable): `"15 Nov 2025 10:00 AM"` (d M Y h:i A)

### Timezone:
- جميع الأوقات في **Server Timezone** (UTC أو حسب config/app.php)
- Frontend يجب أن يحولها حسب timezone المستخدم

---

## 📊 Payment Methods

القيم المقبولة:
- `"cash"` - الدفع نقداً
- `"stripe"` - الدفع عبر Stripe
- `"paypal"` - الدفع عبر PayPal

---

## 📊 Booking Status

القيم المتاحة:
- `"pending"` - معلق (في انتظار التأكيد/الدفع)
- `"confirmed"` - مؤكد
- `"cancelled"` - ملغي
- `"rescheduled"` - إعادة جدولة

---

## 🚀 API Endpoints

### 1. Book New Appointment
**POST** `/api/patient/bookings`

#### Request Body:
```json
{
  "doctor_id": 1,
  "date_time": "2025-11-15 10:00:00",
  "payment_method": "cash"
}
```

#### Field Details:

| Field | Type | Required | Description | Validation |
|-------|------|----------|-------------|------------|
| `doctor_id` | integer | ✅ Yes | رقم الطبيب | Must exist in `doctors` table |
| `date_time` | string (datetime) | ✅ Yes | تاريخ ووقت الموعد | Format: `Y-m-d H:i:s`, Must be **after now** |
| `payment_method` | string (enum) | ✅ Yes | طريقة الدفع | Must be one of: `cash`, `stripe`, `paypal` |

#### Validation Rules:
- ✅ `doctor_id` must exist in database
- ✅ `date_time` must be valid datetime
- ✅ `date_time` must be **in the future** (after current time)
- ✅ `payment_method` must be valid enum value

#### Success Response (201 Created):
```json
{
  "success": true,
  "status": 201,
  "message": "تم حجز الموعد بنجاح",
  "data": {
    "id": 1,
    "date_time": "2025-11-15 10:00:00",
    "date_time_formatted": "15 Nov 2025 10:00 AM",
    "status": "pending",
    "status_label": "معلق",
    "payment_method": "cash",
    "price": 200.00,
    "doctor": {
      "id": 1,
      "specialty": {
        "id": 1,
        "name": "Cardiology"
      },
      "license_number": "DOC123456",
      "clinic_address": "الرياض، المملكة العربية السعودية",
      "location": {
        "lat": 24.7136,
        "lng": 46.6753
      },
      "session_price": 200.00,
      "consultation_type": ["in_clinic", "home_visit"],
      "user": {
        "name": "د. أحمد محمد",
        "email": "doctor@example.com",
        "mobile": "0551111111",
        "profile_photo": null
      },
      "availability": {
        "monday": ["09:00", "10:00", "11:00"],
        "tuesday": ["09:00", "14:00", "15:00"]
      },
      "consultation_type": ["in_clinic", "home_visit"],
      "average_rating": 4.5,
      "reviews_count": 10
    },
    "patient": {
      "id": 1,
      "gender": "male",
      "birthdate": "1990-01-15",
      "user": {
        "name": "محمد أحمد",
        "email": "patient@example.com",
        "mobile": "0553333333",
        "profile_photo": null
      }
    },
    "payment": {
      "id": 1,
      "booking_id": 1,
      "amount": 200.00,
      "transaction_id": "cash_6907b0fbadfab",
      "gateway": "cash",
      "status": "pending",
      "created_at": "2025-10-29 18:00:00",
      "updated_at": "2025-10-29 18:00:00"
    },
    "can_cancel": true,
    "can_reschedule": true,
    "created_at": "2025-10-29 18:00:00",
    "updated_at": "2025-10-29 18:00:00"
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
    "doctor_id": ["يجب اختيار طبيب"],
    "date_time": ["لا يمكن حجز موعد في الماضي"],
    "payment_method": ["طريقة الدفع غير صحيحة"]
  }
}
```

**404 Not Found (Doctor):**
```json
{
  "success": false,
  "status": 404,
  "message": "الطبيب المحدد غير موجود"
}
```

**409 Conflict (Time not available):**
```json
{
  "success": false,
  "status": 409,
  "message": "هذا الوقت غير متاح، يرجى اختيار وقت آخر"
}
```

**403 Forbidden (Not a patient):**
```json
{
  "success": false,
  "status": 403,
  "message": "غير مصرح لك بالوصول"
}
```

---

### 2. Get My Bookings
**GET** `/api/patient/bookings`

#### Query Parameters (Optional):

| Parameter | Type | Required | Description | Values |
|-----------|------|----------|-------------|--------|
| `status` | string | ❌ No | تصفية حسب الحالة | `pending`, `confirmed`, `cancelled`, `rescheduled` |
| `upcoming_only` | boolean | ❌ No | فقط المواعيد القادمة | `true`, `false`, `1`, `0` |
| `date` | string | ❌ No | تصفية حسب تاريخ محدد | تاريخ بصيغة `Y-m-d` (مثال: `2025-11-15`) |

#### Examples:
```
GET /api/patient/bookings
GET /api/patient/bookings?status=confirmed
GET /api/patient/bookings?upcoming_only=true
GET /api/patient/bookings?status=pending&upcoming_only=true
GET /api/patient/bookings?date=2025-11-15
GET /api/patient/bookings?status=confirmed&date=2025-11-15
```

#### Success Response (200 OK):
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب المواعيد بنجاح",
  "data": {
    "data": [
      {
        "id": 1,
        "date_time": "2025-11-15 10:00:00",
        "date_time_formatted": "15 Nov 2025 10:00 AM",
        "status": "pending",
        "status_label": "معلق",
        "payment_method": "cash",
        "price": 200.00,
        "doctor": {
          "id": 1,
          "specialty": {
            "id": 1,
            "name": "Cardiology"
          },
          "license_number": "DOC123456",
          "session_price": 200.00,
          "consultation_type": ["in_clinic", "home_visit"],
          "user": {
            "name": "د. أحمد محمد",
            "email": "doctor@example.com"
          }
        },
        "patient": {
          "id": 1,
          "gender": "male",
          "user": {
            "name": "محمد أحمد",
            "email": "patient@example.com"
          }
        },
        "payment": null,
        "can_cancel": true,
        "can_reschedule": true,
        "created_at": "2025-10-29 18:00:00",
        "updated_at": "2025-10-29 18:00:00"
      }
    ],
    "current_page": 1,
    "per_page": 15,
    "total": 10,
    "last_page": 1,
    "from": 1,
    "to": 10
  }
}
```

#### Pagination Info:
- **Default per_page**: 15
- **Response includes**: `current_page`, `per_page`, `total`, `last_page`, `from`, `to`

---

### 3. Get Booking Details
**GET** `/api/patient/bookings/{id}`

#### URL Parameters:
- `id` (required, integer) - رقم الموعد

#### Example:
```
GET /api/patient/bookings/1
```

#### Success Response (200 OK):
```json
{
  "success": true,
  "status": 200,
  "message": "تم جلب تفاصيل الموعد بنجاح",
  "data": {
    "id": 1,
    "date_time": "2025-11-15 10:00:00",
    "date_time_formatted": "15 Nov 2025 10:00 AM",
    "status": "confirmed",
    "status_label": "مؤكد",
    "payment_method": "cash",
    "price": 200.00,
    "doctor": {
      "id": 1,
      "specialty": {
        "id": 1,
        "name": "Cardiology"
      },
      "license_number": "DOC123456",
      "clinic_address": "الرياض، المملكة العربية السعودية",
      "location": {
        "lat": 24.7136,
        "lng": 46.6753
      },
      "session_price": 200.00,
      "consultation_type": ["in_clinic", "home_visit"],
      "user": {
        "name": "د. أحمد محمد",
        "email": "doctor@example.com",
        "mobile": "0551111111",
        "profile_photo": null
      },
      "availability": {
        "monday": ["09:00", "10:00", "11:00"],
        "tuesday": ["09:00", "14:00", "15:00"]
      },
      "consultation_type": ["in_clinic", "home_visit"]
    },
    "patient": {
      "id": 1,
      "gender": "male",
      "birthdate": "1990-01-15",
      "user": {
        "name": "محمد أحمد",
        "email": "patient@example.com",
        "mobile": "0553333333"
      }
    },
    "payment": {
      "id": 1,
      "booking_id": 1,
      "amount": 200.00,
      "transaction_id": "stripe_pi_abc123",
      "gateway": "stripe",
      "status": "success",
      "created_at": "2025-10-29 18:00:00",
      "updated_at": "2025-10-29 18:05:00"
    },
    "can_cancel": true,
    "can_reschedule": false,
    "created_at": "2025-10-29 18:00:00",
    "updated_at": "2025-10-29 18:00:00"
  }
}
```

#### Error Responses:
- **404**: الموعد غير موجود
- **403**: هذا الموعد ليس لك

---

### 4. Reschedule Appointment
**PUT** `/api/patient/bookings/{id}/reschedule`

#### URL Parameters:
- `id` (required, integer) - رقم الموعد

#### Request Body:
```json
{
  "date_time": "2025-11-20 14:00:00"
}
```

#### Field Details:

| Field | Type | Required | Description | Validation |
|-------|------|----------|-------------|------------|
| `date_time` | string (datetime) | ✅ Yes | تاريخ ووقت الموعد الجديد | Format: `Y-m-d H:i:s`, Must be **after now** |

#### Validation Rules:
- ✅ `date_time` must be valid datetime
- ✅ `date_time` must be **in the future**
- ✅ Booking must not be cancelled
- ✅ New time must be available (no conflict)

#### Success Response (200 OK):
```json
{
  "success": true,
  "status": 200,
  "message": "تم إعادة جدولة الموعد بنجاح",
  "data": {
    "id": 1,
    "date_time": "2025-11-20 14:00:00",
    "date_time_formatted": "20 Nov 2025 02:00 PM",
    "status": "rescheduled",
    "status_label": "إعادة جدولة",
    "payment_method": "cash",
    "price": 200.00,
    "doctor": { ... },
    "patient": { ... },
    "payment": null,
    "can_cancel": true,
    "can_reschedule": true,
    "created_at": "2025-10-29 18:00:00",
    "updated_at": "2025-10-29 18:30:00"
  }
}
```

#### Error Responses:
- **400**: لا يمكن إعادة جدولة هذا الموعد (موعد ماضي أو ملغي)
- **403**: هذا الموعد ليس لك
- **404**: الموعد غير موجود
- **409**: الوقت الجديد غير متاح

---

### 5. Cancel Appointment
**DELETE** `/api/patient/bookings/{id}/cancel`

#### URL Parameters:
- `id` (required, integer) - رقم الموعد

#### Request Body:
```
No body required
```

#### Success Response (200 OK):
```json
{
  "success": true,
  "status": 200,
  "message": "تم إلغاء الموعد بنجاح",
  "data": {
    "id": 1,
    "date_time": "2025-11-15 10:00:00",
    "date_time_formatted": "15 Nov 2025 10:00 AM",
    "status": "cancelled",
    "status_label": "ملغي",
    "payment_method": "cash",
    "price": 200.00,
    "doctor": { ... },
    "patient": { ... },
    "payment": {
      "id": 1,
      "booking_id": 1,
      "amount": 200.00,
      "transaction_id": "cash_6907b0fbadfab",
      "gateway": "cash",
      "status": "failed",
      "created_at": "2025-10-29 18:00:00",
      "updated_at": "2025-10-29 18:35:00"
    },
    "can_cancel": false,
    "can_reschedule": false,
    "created_at": "2025-10-29 18:00:00",
    "updated_at": "2025-10-29 18:35:00"
  }
}
```

#### Cancellation Rules:
- ✅ Can cancel if appointment is **more than 24 hours away**
- ✅ Can cancel if status is `pending`
- ❌ Cannot cancel if less than 24 hours
- ❌ Cannot cancel if already cancelled

#### Error Responses:
- **400**: لا يمكن إلغاء هذا الموعد
- **403**: هذا الموعد ليس لك
- **404**: الموعد غير موجود

---

## 📋 Response Structure

### Standard Response Format:
```json
{
  "success": true/false,
  "status": 200/201/400/403/404/409/422/500,
  "message": "رسالة بالعربية",
  "data": { ... }
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

## 🔑 Important Notes

### 1. Date/Time Format:
- **Frontend sends**: `"2025-11-15 10:00:00"` (Y-m-d H:i:s)
- **Backend returns**:
  - `date_time`: `"2025-11-15 10:00:00"` (Raw)
  - `date_time_formatted`: `"15 Nov 2025 10:00 AM"` (Human readable)

### 2. Payment Method:
- عند اختيار `"cash"`: Booking يُنشأ مباشرة (status: pending)
- عند اختيار `"stripe"` أو `"paypal"`: يتم إنشاء payment intent وجلب تفاصيل الدفع

### 3. Payment Information:
- **Response Field**: `payment` object (nullable)
- **Contains**: تفاصيل المدفوعة إذا كانت موجودة (id, amount, transaction_id, gateway, status)
- **Note**: قد يكون `null` إذا لم يتم إنشاء مدفوعة بعد

### 4. Status Flow:
```
pending → confirmed (بعد الدفع/التأكيد)
pending → cancelled (إلغاء)
confirmed → rescheduled (إعادة جدولة)
rescheduled → cancelled (إلغاء)
```

### 5. Permissions:
- فقط المريض صاحب الموعد يمكنه:
  - إعادة الجدولة
  - الإلغاء
  - عرض التفاصيل

### 6. Business Rules:
- **Cancel**: يجب أن يكون الموعد بعد 24 ساعة
- **Reschedule**: يجب أن يكون الموعد في المستقبل
- **Book**: لا يمكن حجز موعد في الماضي

---

## 📱 Mobile App Integration Example

### Flutter/Dart Example:
```dart
// Book Appointment
final response = await http.post(
  Uri.parse('$baseUrl/api/patient/bookings'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'doctor_id': 1,
    'date_time': '2025-11-15 10:00:00', // Format: Y-m-d H:i:s
    'payment_method': 'cash',
  }),
);

final data = jsonDecode(response.body);
if (data['success']) {
  final booking = data['data'];
  print('Booking ID: ${booking['id']}');
  print('Status: ${booking['status']}');
}
```

### React Native Example:
```javascript
// Book Appointment
const response = await fetch(`${baseUrl}/api/patient/bookings`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    doctor_id: 1,
    date_time: '2025-11-15 10:00:00', // Format: Y-m-d H:i:s
    payment_method: 'cash',
  }),
});

const data = await response.json();
if (data.success) {
  console.log('Booking ID:', data.data.id);
  console.log('Status:', data.data.status);
}
```

---

## ✅ Summary Checklist for Frontend Team

- [ ] Use `Y-m-d H:i:s` format for all datetime fields
- [ ] Always include `Authorization: Bearer {token}` header
- [ ] Set `Content-Type: application/json` for POST/PUT requests
- [ ] Handle validation errors (422) properly
- [ ] Handle conflict errors (409) for unavailable times
- [ ] Check `can_cancel` and `can_reschedule` before showing buttons
- [ ] Use `status_label` for display (Arabic text)
- [ ] Parse pagination data correctly for list endpoints
- [ ] Handle timezone conversion on frontend if needed

---

**Last Updated:** 2025-11-02  
**Version:** 1.1.0

