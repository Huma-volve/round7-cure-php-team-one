# 🩺 Doctor Appointment System

**Backend:** Laravel 12 (PHP 8.2+)  
**Frontend:** Mobile App (Flutter / React Native)  
**Database:** MySQL / PostgreSQL

## 📖 Overview
Doctor Appointment System هو مشروع متكامل بيهدف لتسهيل عملية **حجز المواعيد الطبية** بين المرضى والأطباء من خلال تطبيق موبايل، مع **لوحة تحكم ويب للإدارة (Admins & Helpers)**.  
النظام بيشمل تسجيل المستخدمين، البحث عن الأطباء حسب الموقع أو التخصص، الحجز والدفع، التقييمات، الدردشة الفورية، والإشعارات اللحظية.

---

## 🚀 Features

### 👩‍⚕️ For Patients
- التسجيل والدخول (Email / Mobile / Google / OTP).
- البحث عن الأطباء حسب التخصص أو الموقع الجغرافي (Google Maps API).
- إدارة المفضلة (Add/Remove Favorites).
- حجز المواعيد مع طرق دفع متعددة (PayPal / Stripe / Cash).
- استعراض الحجوزات القادمة والسابقة.
- كتابة التقييمات بعد الجلسة.
- الدردشة الفورية مع الطبيب (Laravel WebSocket).
- إعدادات الحساب (تعديل الملف الشخصي / كلمة المرور / حذف الحساب).
- استقبال الإشعارات (FCM / APNS).

### 🧑‍⚕️ For Doctors
- الحساب يُنشأ من قبل الأدمن ويتم تفعيله عبر البريد أو OTP.
- تسجيل الدخول الآمن (Email / Mobile / Google / OTP).
- إدارة جدول المواعيد (availability).
- عرض وإدارة الحجوزات (قبول / إلغاء / إعادة جدولة).
- عرض تقييمات المرضى والرد عليها.
- إدارة الملف الشخصي (الاسم، التخصص، العيادة، السعر).
- تقارير الأرباح والحجوزات.
- دردشة فورية مع المرضى.
- تلقي إشعارات بالحجوزات والمراجعات الجديدة.

### 🧑‍💻 For Admins & Helpers (Web Dashboard)
- تسجيل الدخول الآمن مع 2FA (OTP).
- إدارة المستخدمين والأطباء (إنشاء، تعديل، حذف، تعليق الحساب).
- إدارة الحجوزات والمدفوعات والمنازعات.
- مراقبة النظام (System Logs / Reports).
- إدارة محتوى الأسئلة الشائعة (FAQs) والسياسات (Policies).
- إشعارات وتنبيهات فورية.
- صلاحيات مرنة باستخدام Spatie Laravel Permission (Roles: Admin, Doctor, Patient).

---

## ⚙️ Tech Stack

| Layer | Technology |
|-------|-------------|
| **Backend** | PHP 8.2+, Laravel 12 |
| **Database** | MySQL / PostgreSQL |
| **Frontend (Mobile)** | Flutter / React Native |
| **Authentication** | Laravel Sanctum (JWT Tokens) |
| **Payments** | PayPal API, Stripe API |
| **Maps / Location** | Google Maps API |
| **Notifications** | Firebase Cloud Messaging (FCM), Apple Push Notification Service (APNS) |
| **Real-time Chat** | Laravel Echo + WebSockets |
| **Hosting / Scalability** | AWS / Laravel Vapor |
| **Security** | HTTPS, encryption, GDPR/HIPAA compliance |

---

## 🧩 System Modules

| Module | Description |
|--------|--------------|
| **Authentication** | تسجيل الدخول والتفعيل عبر OTP أو Google |
| **Doctors Management** | إضافة وتحديث بيانات الأطباء وتخصصاتهم |
| **Patients Management** | إدارة ملفات المرضى وتاريخ حجوزاتهم |
| **Bookings** | إنشاء، إلغاء، إعادة جدولة المواعيد |
| **Payments** | معالجة المدفوعات عبر PayPal أو Stripe |
| **Reviews** | كتابة التقييمات بعد الجلسات |
| **Chat** | دردشة فورية بين الطبيب والمريض |
| **Notifications** | إشعارات للحجوزات والتحديثات |
| **Favorites** | حفظ الأطباء المفضلين |
| **System Logs** | تسجيل الأحداث (logging & auditing) |
| **FAQs / Policies** | إدارة الأسئلة الشائعة والسياسات |

---

## 🔒 Security & Compliance
- استخدام HTTPS + JWT Authentication.
- تشفير بيانات المستخدمين وكلمات المرور.
- توافق مع معايير GDPR/HIPAA لحماية البيانات الصحية.
- فحص صلاحيات الوصول باستخدام **Spatie Laravel Permission**.
- نسخ احتياطي يومي للبيانات.

---

## 🧠 Architecture
- **Layered Architecture** (Controllers → Services → Models).
- **RESTful API** بالكامل للموبايل.
- **RBAC** للتحكم في الصلاحيات.
- **Event Broadcasting** مع WebSockets.
- **Queue Jobs** لإرسال الإشعارات وتنفيذ المهام المؤجلة.

---

## 📊 Performance & Scalability
- زمن استجابة الـ API أقل من 2 ثانية.
- دعم أكثر من 100 مستخدم متزامن مبدئيًا.
- Caching باستخدام Redis.
- إمكانية نشر على **Laravel Vapor** أو **AWS EC2**.

---

## 🧾 Installation (for Backend)
```bash
git clone https://github.com/Huma-volve/round7-cure-php-team-one.git
cd round7-cure-php-team-one
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

---

## 🧑‍💻 Default Roles & Accounts
Seeder Roles:
- `admin`
- `doctor`
- `patient`

Default Admin User:
```
Email: admin@example.com
Password: password123
```

---

## 📱 API Endpoints (Examples)
```
POST   /api/patient/register
POST   /api/patient/login
GET    /api/doctors/nearby
POST   /api/booking/create
GET    /api/patient/bookings
POST   /api/chat/send
```

---

## 🧩 Risks & Mitigations
| Risk | Mitigation |
|------|-------------|
| Payment failure | Laravel Retry Queues |
| Data breach | Encryption + Access Control |
| Scalability issues | Caching + Load Balancing |

---

## 🧾 License
This project is licensed under the **MIT License**.
