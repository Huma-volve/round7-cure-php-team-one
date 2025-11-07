# 🩺 Cure API Documentation

توثيق واجهات برمجة تطبيقات (API) الخاصة بتطبيق **Cure**، والتي تُستخدم من قبل تطبيق الموبايل والواجهة الأمامية.

---

## 🔑 المصادقة (Authentication)

جميع الـ endpoints المحمية تتطلب إرسال **Bearer Token** في الهيدر.

---

## 🧍‍♂️ تسجيل الدخول (Login)

**Endpoint:**  
`POST /api/login`

**Body Parameters:**

| Key | Type | Required | Description |
|-----|------|-----------|-------------|
| email | string | ✅ | البريد الإلكتروني للمستخدم |
| password | string | ✅ | كلمة المرور |

**Response Example:**

```json
{
  "0": {
    "id": 2,
    "name": "Eslam",
    "email": "eslam@example.com",
    "mobile": "01000000001",
    "role": "doctor",
    "doctor": {
      "id": 1,
      "specialty": "Cardiology",
      "license_number": "LIC-001",
      "clinic_address": "Nasr City, Cairo",
      "session_price": 400
    }
  },
  "token": "2|S7bGU0ry7zEp2Y5Z0y1aTHoAbBVas3WE9yYubOSr964e9242"
}
```

---

## 🏠 الصفحة الرئيسية (Home)

**Endpoint:**  
`GET /api/home?latitude=30.0444&longitude=31.2357&search=Dermatology`

**Query Parameters:**

| Key | Type | Required | Description |
|-----|------|-----------|-------------|
| latitude | float | ✅ | إحداثي خط العرض للموقع |
| longitude | float | ✅ | إحداثي خط الطول |
| search | string | ❌ | مصطلح البحث (مثلاً التخصص) |
| radius | int | ❌ | المسافة المسموح بها للبحث |

**Response Example:**

```json
 "success": true,
    "status": 200,
    "message": "تمت العملية بنجاح",
    "data": {
        "user": {
            "id": 39,
            "name": "smsm",
            "greeting": "Welcome back, smsm",
            "location": {
                "address": "12 El-Nasr Street, Cairo",
                "location_lat": "33.00000000",
                "location_lng": "22.00000000"
            },
            "profile_photo": null
        },
        "specialties": [
            {
                "id": 1,
                "name": "Cardiology"
            },
            {
                "id": 4,
                "name": "Dentist"
            },
            {
                "id": 2,
                "name": "Dermatology"
            },
            {
                "id": 7,
                "name": "General Practice"
            },
            {
                "id": 5,
                "name": "Neurology"
            },
            {
                "id": 6,
                "name": "Ophthalmology"
            },
            {
                "id": 3,
                "name": "Pediatrics"
            }
        ],
        "doctors_near_you": [
            {
                "id": 2,
                "name": "patient test",
                "specialty": "Dermatology",
                "clinic_address": "Heliopolis, Cairo",
                "average_rating": 0,
                "reviews_count": 0,
                "availability": "{\"sun\":\"4-9\",\"wed\":\"3-7\"}",
                "is_favorite": true,
                "image": null,
                "distance_km": 0.68
            }
        ]
    }

```

---


## ❤️ المفضلة (Favorites)

### ➕ إضافة / إزالة طبيب من المفضلة

**Endpoint:**  
`POST /api/favorites/toggle/{doctor_id}`

**Response Example:**

```json
{
  "status": true,
  "message": "success",
  "data": {
    "status": "added",
    "message": "Favorite added successfully",
    "doctor_id": 2
  }
}
```

---
## 📋 عرض المفضلة (Get Favorites)


**Endpoint:**  
`GET /api/favorites`

**Response Example:**

```json
{
    "status": true,
    "message": "success",
    "data": {
        "favorites": [
            {
                "id": 2,
                "user_id": 3,
                "specialty_id": 2,
                "license_number": "LIC-002",
                "clinic_address": "Heliopolis, Cairo",
                "latitude": "30.04800000",
                "longitude": "31.23000000",
                "session_price": "350.00",
                "availability_json": "{\"sun\":\"4-9\",\"wed\":\"3-7\"}",
                "experience": null,
                "about_me": null,
                "average_rating": 0,
                "reviews_count": 0,
                "pivot": {
                    "user_id": 39,
                    "doctor_id": 2,
                    "created_at": "2025-11-04T19:40:36.000000Z",
                    "updated_at": "2025-11-04T19:40:36.000000Z"
                },
                "user": {
                    "id": 3,
                    "name": "patient test",
                    "profile_photo": null
                },
                "specialty": {
                    "id": 2,
                    "name": "Dermatology"
                }
            },
            {
                "id": 3,
                "user_id": 4,
                "specialty_id": 3,
                "license_number": "LIC-003",
                "clinic_address": "Maadi, Alex",
                "latitude": "31.20010000",
                "longitude": "29.91870000",
                "session_price": "300.00",
                "availability_json": "{\"tue\":\"2-6\",\"thu\":\"4-9\"}",
                "experience": null,
                "about_me": null,
                "average_rating": 0,
                "reviews_count": 0,
                "pivot": {
                    "user_id": 39,
                    "doctor_id": 3,
                    "created_at": "2025-11-04T15:01:04.000000Z",
                    "updated_at": "2025-11-04T15:01:04.000000Z"
                },
                "user": {
                    "id": 4,
                    "name": "Eslam",
                    "profile_photo": null
                },
                "specialty": {
                    "id": 3,
                    "name": "Pediatrics"
                }
            }
        ]
    },
    "errors": null
}

```

---
 
## 📋 يفحص المفضلة (Check Favorites)

**Endpoint:**  
`GET /api/favorites/check/{doctor_id} `

**Response Example:**

```json

{
    "status": false,
    "message": "Doctor is not a favorite.",
    "data": null,
    "errors": null

    +++++++++++++++++++

    {
    "status": true,
    "message": "success",
    "data": {
        "is_favorite": true
    },
    "errors": null
}
}

```

---

## 🔍 سجل البحث (Search History)

**Endpoint:**  
`POST /api/store-search-history?search_query=eslam`

**Response Example:**

```json
{
  "data": [
    {
      "id": 1,
      "user": { "id": 2, "name": "Eslam" },
      "specialty": { "id": 1, "name": "Cardiology" }
    }
  ]
}
```
##  جميع  التخصصات   (Specialties)

**Endpoint:**  
`GET /api/specialties`

**Response Example:**

```json
{
    "specialties": [
        {
            "id": 1,
            "name": "Cardiology",
            "image": null
        },
        {
            "id": 2,
            "name": "Dermatology",
            "image": null
        },
        {
            "id": 3,
            "name": "Pediatrics",
            "image": null
        },
        {
            "id": 4,
            "name": "Dentist",
            "image": null
        },
        {
            "id": 5,
            "name": "Neurology",
            "image": null
        },
        {
            "id": 6,
            "name": "Ophthalmology",
            "image": null
        },
        {
            "id": 7,
            "name": "General Practice",
            "image": null
        }
    ]
}

```

---




##  جميع  الدكاتره   (Doctors)

**Endpoint:**  
`GET /api/doctors`

**Response Example:**

```json
 
    "success": true,
    "status": 200,
    "message": "تم جلب قائمة الأطباء بنجاح",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "user_id": 2,
                "specialty_id": 1,
                "license_number": "LIC-001",
                "clinic_address": "Nasr City, Cairo",
                "latitude": "30.05200000",
                "longitude": "31.23700000",
                "session_price": "400.00",
                "availability_json": "{\"day\":\"mon\",\"from\":\"15:00\",\"to\":\"20:00\"}",
                "experience": null,
                "about_me": null,
                "average_rating": 0,
                "reviews_count": 0,
                "user": {
                    "id": 2,
                    "name": "doctor test",
                    "email": "doctortest@example.com",
                    "mobile": "01144778523",
                    "birthdate": null,
                    "profile_photo": null,
                    "location_lat": null,
                    "location_lng": null,
                    "email_verified_at": null,
                    "created_at": "2025-11-03T19:00:27.000000Z",
                    "updated_at": "2025-11-03T19:00:27.000000Z",
                    "email_otp": null,
                    "email_otp_expires_at": null,
                    "phone_otp": null,
                    "phone_otp_expires_at": null,
                    "email_otp_sent_at": null,
                    "deleted_at": null,
                    "google_id": null
                },
                "specialty": {
                    "id": 1,
                    "name": "Cardiology",
                    "image": null,
                    "created_at": "2025-11-03T19:00:28.000000Z",
                    "updated_at": "2025-11-03T19:00:28.000000Z"
                }
            },
            {
                "id": 2,
                "user_id": 3,
                "specialty_id": 2,
                "license_number": "LIC-002",
                "clinic_address": "Heliopolis, Cairo",
                "latitude": "30.04800000",
                "longitude": "31.23000000",
                "session_price": "350.00",
                "availability_json": "{\"sun\":\"4-9\",\"wed\":\"3-7\"}",
                "experience": null,
                "about_me": null,
                "average_rating": 0,
                "reviews_count": 0,
                "user": {
                    "id": 3,
                    "name": "patient test",
                    "email": "patient1@example.com",
                    "mobile": "01144778598",
                    "birthdate": null,
                    "profile_photo": null,
                    "location_lat": null,
                    "location_lng": null,
                    "email_verified_at": null,
                    "created_at": "2025-11-03T19:00:27.000000Z",
                    "updated_at": "2025-11-03T19:00:27.000000Z",
                    "email_otp": null,
                    "email_otp_expires_at": null,
                    "phone_otp": null,
                    "phone_otp_expires_at": null,
                    "email_otp_sent_at": null,
                    "deleted_at": null,
                    "google_id": null
                },
                "specialty": {
                    "id": 2,
                    "name": "Dermatology",
                    "image": null,
                    "created_at": "2025-11-03T19:00:28.000000Z",
                    "updated_at": "2025-11-03T19:00:28.000000Z"
                }
            },
            {
                "id": 3,
                "user_id": 4,
                "specialty_id": 3,
                "license_number": "LIC-003",
                "clinic_address": "Maadi, Alex",
                "latitude": "31.20010000",
                "longitude": "29.91870000",
                "session_price": "300.00",
                "availability_json": "{\"tue\":\"2-6\",\"thu\":\"4-9\"}",
                "experience": null,
                "about_me": null,
                "average_rating": 0,
                "reviews_count": 0,
                "user": {
                    "id": 4,
                    "name": "Eslam",
                    "email": "eslam@example.com",
                    "mobile": "01000000001",
                    "birthdate": "1998-04-30T21:00:00.000000Z",
                    "profile_photo": null,
                    "location_lat": "30.05000000",
                    "location_lng": "31.23333333",
                    "email_verified_at": null,
                    "created_at": "2025-11-03T19:00:28.000000Z",
                    "updated_at": "2025-11-03T19:00:28.000000Z",
                    "email_otp": null,
                    "email_otp_expires_at": null,
                    "phone_otp": null,
                    "phone_otp_expires_at": null,
                    "email_otp_sent_at": null,
                    "deleted_at": null,
                    "google_id": null
                },
                "specialty": {
                    "id": 3,
                    "name": "Pediatrics",
                    "image": null,
                    "created_at": "2025-11-03T19:00:28.000000Z",
                    "updated_at": "2025-11-03T19:00:28.000000Z"
                }
            },
            {
                "id": 4,
                "user_id": 17,
                "specialty_id": 1,
                "license_number": "DOC123456",
                "clinic_address": "الرياض، المملكة العربية السعودية",
                "latitude": "24.71360000",
                "longitude": "46.67530000",
                "session_price": "200.00",
                "availability_json": {
                    "monday": [
                        "09:00",
                        "10:00",
                        "11:00",
                        "14:00",
                        "15:00"
                    ],
                    "tuesday": [
                        "09:00",
                        "10:00",
                        "11:00",
                        "14:00",
                        "15:00"
                    ],
                    "wednesday": [
                        "09:00",
                        "10:00",
                        "11:00",
                        "14:00",
                        "15:00"
                    ],
                    "thursday": [
                        "09:00",
                        "10:00",
                        "11:00"
                    ],
                    "friday": [],
                    "saturday": [
                        "10:00",
                        "11:00"
                    ],
                    "sunday": []
                },
                "experience": null,
                "about_me": null,
                "average_rating": 3.5,
                "reviews_count": 2,
                "user": {
                    "id": 17,
                    "name": "د. أحمد محمد",
                    "email": "doctor@example.com",
                    "mobile": "0551111111",
                    "birthdate": null,
                    "profile_photo": null,
                    "location_lat": null,
                    "location_lng": null,
                    "email_verified_at": null,
                    "created_at": "2025-11-03T19:00:29.000000Z",
                    "updated_at": "2025-11-03T19:00:29.000000Z",
                    "email_otp": null,
                    "email_otp_expires_at": null,
                    "phone_otp": null,
                    "phone_otp_expires_at": null,
                    "email_otp_sent_at": null,
                    "deleted_at": null,
                    "google_id": null
                },
                "specialty": {
                    "id": 1,
                    "name": "Cardiology",
                    "image": null,
                    "created_at": "2025-11-03T19:00:28.000000Z",
                    "updated_at": "2025-11-03T19:00:28.000000Z"
                }
            }
        ],
        "first_page_url": "https://round7-cure.huma-volve.com/api/doctors?page=1",
        "from": 1,
        "last_page": 2,
        "last_page_url": "https://round7-cure.huma-volve.com/api/doctors?page=2",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "page": null,
                "active": false
            },
            {
                "url": "https://round7-cure.huma-volve.com/api/doctors?page=1",
                "label": "1",
                "page": 1,
                "active": true
            },
            {
                "url": "https://round7-cure.huma-volve.com/api/doctors?page=2",
                "label": "2",
                "page": 2,
                "active": false
            },
            {
                "url": "https://round7-cure.huma-volve.com/api/doctors?page=2",
                "label": "Next &raquo;",
                "page": 2,
                "active": false
            }
        ],
        "next_page_url": "https://round7-cure.huma-volve.com/api/doctors?page=2",
        "path": "https://round7-cure.huma-volve.com/api/doctors",
        "per_page": 4,
        "prev_page_url": null,
        "to": 4,
        "total": 5
    }


```

---


##     تفاصيل الدكتور(Doctors-Details)

**Endpoint:**  
`GET /api/doctor/{id}`

**Response Example:**

```json
 
    
 {
    "success": true,
    "status": 200,
    "message": "تم جلب بيانات الطبيب بنجاح",
    "data": {
        "doctor": {
            "id": 4,
            "specialty": "Cardiology",
            "license_number": "DOC123456",
            "clinic_address": "الرياض، المملكة العربية السعودية",
            "location": {
                "lat": 24.7136,
                "lng": 46.6753
            },
            "session_price": 200,
            "average_rating": 3.5,
            "reviews_count": 2,
            "availability": {
                "monday": [
                    "09:00",
                    "10:00",
                    "11:00",
                    "14:00",
                    "15:00"
                ],
                "tuesday": [
                    "09:00",
                    "10:00",
                    "11:00",
                    "14:00",
                    "15:00"
                ],
                "wednesday": [
                    "09:00",
                    "10:00",
                    "11:00",
                    "14:00",
                    "15:00"
                ],
                "thursday": [
                    "09:00",
                    "10:00",
                    "11:00"
                ],
                "friday": [],
                "saturday": [
                    "10:00",
                    "11:00"
                ],
                "sunday": []
            },
            "user": {
                "id": 17,
                "name": "د. أحمد محمد",
                "email": "doctor@example.com",
                "mobile": "0551111111",
                "profile_photo": null
            }
        },
        "experience": 0,
        "patient_count": 7,
        "about_me": null,
        "session_price": 200,
        "availability": {
            "monday": [
                "09:00",
                "10:00",
                "11:00",
                "14:00",
                "15:00"
            ],
            "tuesday": [
                "09:00",
                "10:00",
                "11:00",
                "14:00",
                "15:00"
            ],
            "wednesday": [
                "09:00",
                "10:00",
                "11:00",
                "14:00",
                "15:00"
            ],
            "thursday": [
                "09:00",
                "10:00",
                "11:00"
            ],
            "friday": [],
            "saturday": [
                "10:00",
                "11:00"
            ],
            "sunday": []
        },
        "reviews": [
            {
                "id": 1,
                "rating": 3,
                "comment": "Updated review",
                "user": {
                    "id": 19,
                    "name": "محمد عبدالله",
                    "profile_photo": null,
                    "created_at": "2025-11-04 11:59:09"
                },
                "created_at": "2025-11-04 11:59:09"
            },
            {
                "id": 2,
                "rating": 4,
                "comment": "Good review!",
                "user": {
                    "id": 19,
                    "name": "محمد عبدالله",
                    "profile_photo": null,
                    "created_at": "2025-11-04 12:11:53"
                },
                "created_at": "2025-11-04 12:11:53"
            }
        ]
    }
}


```

---

📅 **آخر تحديث:** 4 نوفمبر 2025  
✍️ **إعداد:** Eslam Mohamed
