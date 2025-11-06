# 🩺 Cure API Documentation

توثيق واجهات برمجة تطبيقات (API) الخاصة بتطبيق **Cure**، والتي تُستخدم من قبل تطبيق الموبايل والواجهة الأمامية.

---

## 🔑 Authentication

جميع الـ endpoints المحمية تتطلب إرسال **Bearer Token** في الهيدر:



---

## 🧍‍♂️ Login

**Endpoint:**  
`POST /api/login`

**Headers:**



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
------

### 🧍‍♂️ home

**Endpoint:** 

`GET /api/?latitude=30.0444&longitude=31.2357&search=Dermatology`

**Headers:**


Accept: application/json
Authorization: Bearer <token>

Query Parameters:

 | Key       | Type   | Required | Description                |
| --------- | ------ | -------- | -------------------------- |
| latitude  | float  | ✅        | إحداثي خط العرض للموقع     |
| longitude | float  | ✅        | إحداثي خط الطول            |
| search    | string | ❌        | مصطلح البحث (مثلاً التخصص) |
| radius    | int    | ❌        | المسافة المسموح بها للبحث  |

 

{
    "success": true,
    "status": 200,
    "message": "تمت العملية بنجاح",
    "data": {
        "user": {
            "id": 2,
            "name": "Eslam",
            "greeting": "Welcome back, Eslam",
            "location": {
                "address": "12 El-Nasr Street, Cairo",
                "location_lat": "30.05000000",
                "location_lng": "31.23333333"
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
                "name": "Ahmed",
                "specialty": "Dermatology",
                "clinic_address": "Heliopolis, Cairo",
                "average_rating": 5,
                "reviews_count": 1,
                "availability": "{\"sun\":\"4-9\",\"wed\":\"3-7\"}",
                "consultation_type": ["in_clinic", "home_visit"],
                "is_favorite": false,
                "image": null,
                "distance_km": 0.68
            }
        ]
    }
}

👩‍⚕️ Doctor Details

Endpoint:
GET /api/doctors-details/{id}


Headers:
Accept: application/json


Response Example:

{
    "status": true,
    "data": {
        "id": 2,
        "name": "Ahmed",
        "specialty": "Dermatology",
        "license_number": "LIC-002",
        "clinic_address": "Heliopolis, Cairo",
        "consultation_type": ["in_clinic", "home_visit"]
    }
}

❤️ Favorites
➕ Toggle Favorite

Endpoint:
POST /api/favorites/toggle/{doctor_id}

Headers:

Accept: application/json
Authorization: Bearer <token>

Response

{
    "status": true,
    "message": "Favorite added successfully",
    "doctor_id": 3
}

📋 Get Favorites

Endpoint:
GET /api/favorites

Headers:
Accept: application/json
Authorization: Bearer <token>

Response

{
    "status": true,
    "data": [
        {
            "id": 3,
            "name": "Sara",
            "specialty": "Pediatrics",
            "clinic_address": "Maadi, Alex"
        }
    ]
}



✅ Check Favorite

Endpoint:
GET /api/favorites/check/{doctor_id}

Headers:
Accept: application/json
Authorization: Bearer <token>

{
    "status": true,
    "message": "success",
    "data": {
        "is_favorite": true
    }
}

🔍 Search History

Endpoint:
POST /api/store-search-history?search_query=eslam

Headers:
Accept: application/json
Authorization: Bearer <token>

Headers:

| Key          | Type   | Required | Description                     |
| ------------ | ------ | -------- | ------------------------------- |
| search_query | string | ✅        | مصطلح البحث الذي أدخله المستخدم |

Response

{
    "data": [
        {
            "id": 1,
            "user": {
                "id": 2,
                "name": "Eslam"
            },
            "specialty": {
                "id": 1,
                "name": "Cardiology"
            }
        }
    ]
}

🗂️ Notes

جميع الردود تأتي بصيغة JSON.

الـ token صالح لكل الطلبات المحمية.

الصور حاليًا null حتى يتم ربطها في المستقبل.

📅 آخر تحديث: 1 نوفمبر 2025
✍️ تم الإعداد بواسطة: Eslam Mohamed



---

هل ترغب يا إسلام أن أجهز لك نفس الـ documentation دي في **ملف فعلي باسم `api.md`** قابلة للتحميل؟







