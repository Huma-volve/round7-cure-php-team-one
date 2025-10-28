# 📖 Cure - API Documentation

<div align="center">

# 🏥 Cure Platform

**نظام حجز مواعيد طبية شامل**

---

[Authentication](#-authentication) • [Patient APIs](#-patient-endpoints) • [Doctor APIs](#-doctor-endpoints) • [Shared APIs](#-shared-endpoints) • [Error Handling](#-error-responses)

</div>

---

## 📋 Table of Contents

- [Authentication](#-authentication)
- [Patient Endpoints](#-patient-endpoints)
- [Doctor Endpoints](#-doctor-endpoints)
- [Admin Endpoints](#-admin-endpoints)
- [Shared Endpoints](#-shared-endpoints)
- [Error Responses](#-error-responses)
- [Data Models](#-data-models)
- [Development Log](#-development-log)

---

## 🔐 Authentication

### Base URL
```
http://localhost:8000/api
```

### Headers Required
جميع الـ APIs (ما عدا الـ public) تحتاج إلى:

```http
Authorization: Bearer {your-token}
Content-Type: application/json
Accept: application/json
```

### How to Get Token
استخدم Laravel Sanctum للـ authentication.

---

## 🚀 Public Endpoints

### 1. Test Role Endpoint
**GET** `/api/test-role`

> **Description:** Test endpoint لفحص النظام والأدوار

**Headers:** None (Public)

**Response (200):**
```json
{
    "user_id": 1,
    "roles": ["admin"],
    "has_admin": true
}
```

---

## 👤 User Endpoints

### 1. Get Authenticated User Info
**GET** `/api/user`

> **Description:** جلب معلومات المستخدم المسجل دخوله

**Headers:**
```http
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "id": 1,
    "name": "محمد أحمد",
    "email": "mohamed@example.com",
    "mobile": "0555555555",
    "profile_photo": null,
    "roles": ["patient"],
    "email_verified_at": "2025-10-26T10:00:00.000000Z",
    "created_at": "2025-10-26T10:00:00.000000Z"
}
```

**Errors:**
- `401 Unauthorized` - إذا لم يتم تسجيل الدخول

---

## 🏥 Patient Endpoints

### 1. Book New Appointment
**POST** `/api/patient/bookings`

> **Description:** المريض يقوم بحجز موعد جديد مع طبيب

**Headers:**
```http
Authorization: Bearer {patient-token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "doctor_id": 1,
    "date_time": "2025-11-15 10:00:00",
    "payment_method": "cash"
}
```

**Request Parameters:**
- `doctor_id` (required, integer) - رقم الطبيب
- `date_time` (required, datetime) - تاريخ ووقت الموعد (format: Y-m-d H:i:s)
- `payment_method` (required, enum) - طريقة الدفع: `cash`, `paypal`, `stripe`

**Response (201 Created):**
```json
{
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
            "specialty": "طب القلب",
            "session_price": 200.00,
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
        "can_cancel": true,
        "can_reschedule": true,
        "created_at": "2025-10-26 10:00:00",
        "updated_at": "2025-10-26 10:00:00"
    }
}
```

**Possible Errors:**
- `403 Forbidden` - المستخدم ليس مريض
- `404 Not Found` - الطبيب غير موجود
- `409 Conflict` - الوقت غير متاح
- `422 Validation Error` - بيانات غير صحيحة

---

### 2. Get My Bookings
**GET** `/api/patient/bookings`

> **Description:** عرض جميع مواعيد المريض

**Headers:**
```http
Authorization: Bearer {patient-token}
```

**Query Parameters (Optional):**
- `status` - حالة الموعد: `pending`, `confirmed`, `cancelled`, `rescheduled`
- `upcoming_only` - `true` لعرض القادمة فقط

**Example Request:**
```
GET /api/patient/bookings?status=confirmed&upcoming_only=true
```

**Response (200):**
```json
{
    "message": "تم جلب المواعيد بنجاح",
    "data": {
        "data": [
            {
                "id": 1,
                "date_time": "2025-11-15 10:00:00",
                "date_time_formatted": "15 Nov 2025 10:00 AM",
                "status": "confirmed",
                "status_label": "مؤكد",
                "payment_method": "cash",
                "price": 200.00,
                "doctor": {
                    "specialty": "طب القلب",
                    "user": {
                        "name": "د. أحمد محمد"
                    }
                },
                "can_cancel": true,
                "can_reschedule": false
            }
        ],
        "current_page": 1,
        "total": 10,
        "per_page": 15,
        "last_page": 1
    }
}
```

---

### 3. Get Booking Details
**GET** `/api/patient/bookings/{id}`

> **Description:** عرض تفاصيل موعد معين

**Headers:**
```http
Authorization: Bearer {patient-token}
```

**URL Parameters:**
- `id` (required, integer) - رقم الموعد

**Response (200):**
```json
{
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
            "specialty": "طب القلب",
            "license_number": "DOC123456",
            "clinic_address": "الرياض، المملكة العربية السعودية",
            "location": {
                "lat": 24.7136,
                "lng": 46.6753
            },
            "session_price": 200.00,
            "user": {
                "name": "د. أحمد محمد",
                "email": "doctor@example.com",
                "mobile": "0551111111",
                "profile_photo": null
            },
            "availability": {
                "monday": ["09:00", "10:00", "11:00"],
                "tuesday": ["09:00", "14:00", "15:00"]
            }
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
        "can_cancel": true,
        "can_reschedule": false,
        "created_at": "2025-10-26 10:00:00",
        "updated_at": "2025-10-26 10:00:00"
    }
}
```

**Errors:**
- `403 Forbidden` - هذا الموعد ليس لك
- `404 Not Found` - الموعد غير موجود

---

### 4. Reschedule Appointment
**PUT** `/api/patient/bookings/{id}/reschedule`

> **Description:** إعادة جدولة موعد موجود

**Headers:**
```http
Authorization: Bearer {patient-token}
Content-Type: application/json
```

**URL Parameters:**
- `id` (required, integer) - رقم الموعد

**Request Body:**
```json
{
    "date_time": "2025-11-20 14:00:00"
}
```

**Request Parameters:**
- `date_time` (required, datetime) - التاريخ والوقت الجديد

**Response (200):**
```json
{
    "message": "تم إعادة جدولة الموعد بنجاح",
    "data": {
        "id": 1,
        "date_time": "2025-11-20 14:00:00",
        "status": "rescheduled",
        "status_label": "إعادة جدولة"
    }
}
```

**Errors:**
- `400 Bad Request` - لا يمكن إعادة الجدولة (موعد قديم أو ملغي)
- `403 Forbidden` - هذا الموعد ليس لك
- `404 Not Found` - الموعد غير موجود
- `409 Conflict` - الوقت الجديد غير متاح

---

### 5. Cancel Appointment
**DELETE** `/api/patient/bookings/{id}/cancel`

> **Description:** إلغاء موعد (قبل 24 ساعة فقط)

**Headers:**
```http
Authorization: Bearer {patient-token}
```

**URL Parameters:**
- `id` (required, integer) - رقم الموعد

**Response (200):**
```json
{
    "message": "تم إلغاء الموعد بنجاح",
    "data": {
        "id": 1,
        "status": "cancelled",
        "status_label": "ملغي"
    }
}
```

**Errors:**
- `400 Bad Request` - لا يمكن الإلغاء (أقل من 24 ساعة)
- `403 Forbidden` - هذا الموعد ليس لك
- `404 Not Found` - الموعد غير موجود

---

## 👨‍⚕️ Doctor Endpoints

### 1. Doctor Dashboard
**GET** `/api/doctor/dashboard`

> **Description:** لوحة تحكم الطبيب مع المواعيد القادمة والإحصائيات

**Headers:**
```http
Authorization: Bearer {doctor-token}
```

**Response (200):**
```json
{
    "message": "تم جلب بيانات لوحة التحكم بنجاح",
    "data": {
        "upcoming": [
            {
                "id": 1,
                "date_time": "2025-11-15 10:00:00",
                "date_time_formatted": "15 Nov 2025 10:00 AM",
                "status": "confirmed",
                "status_label": "مؤكد",
                "patient": {
                    "user": {
                        "name": "محمد أحمد",
                        "email": "patient@example.com",
                        "mobile": "0553333333"
                    }
                },
                "price": 200.00
            }
        ],
        "pending": [
            {
                "id": 5,
                "date_time": "2025-11-20 11:00:00",
                "status": "pending",
                "status_label": "معلق",
                "patient": {
                    "user": {
                        "name": "سارة أحمد"
                    }
                }
            }
        ],
        "stats": {
            "total_upcoming": 15,
            "pending": 3,
            "today": 5
        }
    }
}
```

---

### 2. Get Doctor Bookings
**GET** `/api/doctor/bookings`

> **Description:** عرض جميع مواعيد الطبيب

**Headers:**
```http
Authorization: Bearer {doctor-token}
```

**Query Parameters (Optional):**
- `status` - حالة الموعد
- `upcoming_only` - `true` للقادمة فقط

**Response (200):**
```json
{
    "message": "تم جلب المواعيد بنجاح",
    "data": {
        "data": [ /* List of bookings */ ],
        "current_page": 1,
        "total": 50
    }
}
```

---

### 3. Get Booking Details (Doctor)
**GET** `/api/doctor/bookings/{id}`

> **Description:** عرض تفاصيل موعد (للطبيب)

**Headers:**
```http
Authorization: Bearer {doctor-token}
```

**URL Parameters:**
- `id` (required, integer) - رقم الموعد

**Response (200):**
```json
{
    "message": "تم جلب تفاصيل الموعد بنجاح",
    "data": { /* Same structure as patient booking */ }
}
```

---

### 4. Confirm Booking
**PUT** `/api/doctor/bookings/{id}/confirm`

> **Description:** تأكيد موعد معلق

**Headers:**
```http
Authorization: Bearer {doctor-token}
```

**URL Parameters:**
- `id` (required, integer) - رقم الموعد

**Response (200):**
```json
{
    "message": "تم تأكيد الموعد بنجاح",
    "data": {
        "id": 1,
        "status": "confirmed",
        "status_label": "مؤكد"
    }
}
```

**Errors:**
- `403 Forbidden` - هذا الموعد ليس لك
- `404 Not Found` - الموعد غير موجود

---

## 👑 Admin Endpoints

### 1. Admin Dashboard
**GET** `/api/admin/dashboard`

> **Description:** لوحة تحكم الإدارة

**Headers:**
```http
Authorization: Bearer {admin-token}
```

**Response (200):**
```json
{
    "ok": true,
    "area": "admin only"
}
```

---

## 🔍 Shared Endpoints

### 1. Get Available Slots
**GET** `/api/doctors/{doctorId}/available-slots`

> **Description:** عرض الأوقات المتاحة لطبيب معين

**Headers:**
```http
Authorization: Bearer {token}
```

**URL Parameters:**
- `doctorId` (required, integer) - رقم الطبيب

**Response (200):**
```json
{
    "message": "تم جلب الأوقات المتاحة بنجاح",
    "data": {
        "doctor": {
            "id": 1,
            "specialty": "طب القلب",
            "session_price": 200.00,
            "user": {
                "name": "د. أحمد محمد"
            }
        },
        "available_slots": [
            {
                "datetime": "2025-11-10 10:00:00",
                "formatted": "10 Nov 2025 10:00 AM"
            },
            {
                "datetime": "2025-11-10 11:00:00",
                "formatted": "10 Nov 2025 11:00 AM"
            }
        ],
        "availability": {
            "monday": ["09:00", "10:00", "11:00"],
            "tuesday": ["09:00", "14:00", "15:00"]
        }
    }
}
```

---

## ❌ Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "message": "غير مصرح لك بحجز موعد"
}
```

### 404 Not Found
```json
{
    "message": "الطبيب المحدد غير موجود"
}
```

### 409 Conflict
```json
{
    "message": "هذا الوقت غير متاح، يرجى اختيار وقت آخر",
    "available_slots": [ /* Optional */ ]
}
```

### 422 Validation Error
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "date_time": ["لا يمكن حجز موعد في الماضي"],
        "doctor_id": ["يجب اختيار طبيب"]
    }
}
```

### 500 Server Error
```json
{
    "message": "حدث خطأ أثناء حجز الموعد",
    "error": "Error details"
}
```

---

## 📊 Data Models

### Booking Status
| Status | Label (Arabic) | Description |
|--------|----------------|-------------|
| `pending` | معلق | في انتظار تأكيد الطبيب |
| `confirmed` | مؤكد | مؤكد من الطبيب |
| `cancelled` | ملغي | تم إلغاء الموعد |
| `rescheduled` | إعادة جدولة | تم إعادة جدولته |

### Payment Methods
| Method | Description |
|--------|-------------|
| `cash` | نقداً |
| `paypal` | PayPal |
| `stripe` | Stripe |

---

## 💡 Important Notes

1. **Date Format**: استخدم `Y-m-d H:i:s` للتواريخ (مثال: `2025-11-15 10:00:00`)
2. **Cannot book in past**: لا يمكن حجز مواعيد في الماضي
3. **No double bookings**: لا يمكن حجز موعدين في نفس الوقت
4. **Cancellation policy**: يمكن الإلغاء قبل 24 ساعة فقط
5. **Timezone**: النظام يستخدم timezone السيرفر

---

## 🛠️ Testing Accounts

### Admin
```
Email: admin@example.com
Password: password123
```

### Doctor
```
Email: doctor@example.com
Password: password
```

### Patient
```
Email: patient@example.com
Password: password
```

---

## 📝 Development Log

### 🗓️ [2025-10-27] - Controller Organization (Api/Dashboard Separation)

#### 📁 New Structure:
- ✅ **تم فصل Controllers** في مجلدات منفصلة:
  - `app/Http/Controllers/Api/` - كل API Controllers
  - `app/Http/Controllers/Dashboard/` - جاهز للـ Dashboard Controllers المستقبلية
- ✅ `PatientController.php` نقل إلى `Api/PatientController.php`
- ✅ `DoctorController.php` نقل إلى `Api/DoctorController.php`
- ✅ Routes محدثة لاستخدام الـ namespace الجديد
- ✅ البنية أصبحت جاهزة لـ Dashboard Controllers

#### 🎯 Benefits:
- 📦 Organization: APIs منفصلة عن Dashboards
- 🚀 Scalability: سهل إضافة Dashboard Controllers
- 🧹 Separation: كل controller في مكانه الصحيح

---

### 🗓️ [2025-10-27] - Response Standardization

#### ✨ Response Trait Added:
- ✅ تم إنشاء **ApiResponseTrait** لـ Responses موحدة
- ✅ Controllers تستخدم Trait بدلاً من كود مكرر
- ✅ نفس شكل Response في كل API
- ✅ أسهل للصيانة والتطوير
- ✅ Error Handling موحد ومنظم

#### 📝 Methods Available:
- `successResponse()` - Success responses (includes status code in JSON)
- `errorResponse()` - Error responses (includes status code in JSON)
- `createdResponse()` - 201 Created
- `unauthorizedResponse()` - 403 Forbidden
- `notFoundResponse()` - 404 Not Found
- `conflictResponse()` - 409 Conflict
- `validationErrorResponse()` - 422 Validation
- `paginatedResponse()` - Paginated data
- `serverErrorResponse()` - 500 Server Error

#### ✨ Response Structure:
```json
{
    "success": true/false,
    "status": 200,
    "message": "...",
    "data": {...}
}
```

---

### 🗓️ [2025-10-27] - Routes Organized in Separate Files

#### ✨ Routes Organization:
- ✅ **فصل Routes في ملفات منفصلة** لمنع Conflicts مع Team
- ✅ `routes/api/patient.php` - كل routes المريض
- ✅ `routes/api/doctor.php` - كل routes الطبيب
- ✅ `routes/api/admin.php` - كل routes الإدارة
- ✅ `routes/api/shared.php` - كل routes المشتركة
- ✅ `routes/api/public.php` - كل routes العامة
- ✅ كل developer يعمل على ملف منفصل
- ✅ منع Git Conflicts
- ✅ كود أكثر تنظيم ووضوح
- ✅ أسهل للصيانة والتطوير

#### 📂 New Route Files:
- ✅ `routes/api/patient.php`
- ✅ `routes/api/doctor.php`
- ✅ `routes/api/admin.php`
- ✅ `routes/api/shared.php`
- ✅ `routes/api/public.php`

---

### 🗓️ [2025-10-26] - Routes Refactoring

#### ✨ Routes Improvements:
- ✅ إعادة تنظيم `routes/api.php` بشكل أفضل
- ✅ تعليقات واضحة لكل قسم
- ✅ Nested Groups للـ bookings
- ✅ Route names متسقة ومنظمة
- ✅ فصل واضح لكل role (Patient, Doctor, Admin)
- ✅ Shared routes منفصلة
- ✅ استخدام `Route::controller()` لتقليل التكرار
- ✅ كود أقل وأكثر نظافة (لا تكرار لـ Controller class)
- ✅ البنية أصبحت سهلة القراءة والصيانة

---

### 🗓️ [2025-10-26] - Code Refactoring

#### ✨ Refactoring Changes:
- ✅ تم إنشاء **Services Layer** (BookingService)
- ✅ تم إنشاء **Repositories** (BookingRepository)
- ✅ تم إضافة **Constants** (BookingStatus, PaymentMethod)
- ✅ تحسين **Exception Handling**
- ✅ Controllers أصبحت **Thin Controllers**
- ✅ استخدام **Dependency Injection**
- ✅ **Clean Architecture** يتبع Best Practices

#### 📂 New Files Created:
- ✅ `app/Services/Booking/BookingService.php`
- ✅ `app/Repositories/BookingRepository.php`
- ✅ `app/Constants/BookingStatus.php`
- ✅ `app/Constants/PaymentMethod.php`
- ✅ `Cure_API.postman_collection.json` - Postman Collection

#### 📝 Benefits:
- 🎯 **Separation of Concerns** - كل جزء في مكانه
- 🧪 **Testability** - سهل الاختبار
- 🔄 **Maintainability** - سهل الصيانة
- 📈 **Scalability** - سهل إضافة features

---

### 🗓️ [2025-10-26] - Booking System Implementation

#### APIs Added:
- ✅ Patient: 5 endpoints
- ✅ Doctor: 4 endpoints
- ✅ Admin: 1 endpoint
- ✅ Shared: 1 endpoint
- ✅ Public: 1 endpoint

#### Controllers Added:
- ✅ `PatientController.php` - كل عمليات المريض
- ✅ `DoctorController.php` - لوحة تحكم الطبيب وعرض المواعيد
- ✅ Separation of concerns لكل role

#### Models Added:
- ✅ Doctor, Patient, Booking, Payment, Review

#### Features:
- ✅ Conflict prevention
- ✅ Availability check
- ✅ Cancellation policy (24h)
- ✅ Rescheduling system
- ✅ Role-based access
- ✅ Arabic messages
- ✅ OTP support for mobile verification

---

## 🔄 Quick Links

- **Base URL**: `http://localhost:8000/api`
- **Routes**: `routes/api.php`
- **Controllers**: `app/Http/Controllers/`
- **Models**: `app/Models/`
- **Resources**: `app/Http/Resources/`

---

<div align="center">

**Made with ❤️ by Team Huma Volve**

*Last Updated: 2025-10-27*

</div>
