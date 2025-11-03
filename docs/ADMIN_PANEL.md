# وثائق لوحة الإدارة (Admin Panel)

الغرض: تمكين الأدمن من إدارة المستخدمين والحجوزات، ومراقبة المدفوعات والنزاعات، وإدارة تذاكر الدعم. الواجهة مبنية على Blade وتستخدم مصادقة Breeze.

## الوصول والصلاحيات
- تسجيل الدخول:
  - GET `/login`, POST `/login`, POST `/logout`
  - التسجيل معطّل (لا يوجد `/register`).
- حماية لوحة التحكم: جميع مسارات لوحة الإدارة تحت `auth` + `role:admin`.
- تلميح: أضف دور `admin` للمستخدم عبر Spatie قبل محاولة الدخول.

## روابط لوحة الإدارة (Blade)
- لوحة التحكم الرئيسية: `/admin`
- المستخدمون: `/admin/users`
- الحجوزات: `/admin/bookings`
- المدفوعات: `/admin/payments`
- النزاعات: `/admin/disputes` (تبويبين: نزاعات مدفوعات/حجوزات)
- التذاكر: `/admin/tickets`

جميع القوائم تجلب البيانات عبر استدعاءات AJAX من واجهات الـ API أدناه وتعرضها داخل جداول مع فلاتر أساسية.

## واجهات API (محمية بـ Sanctum + role:admin)
- Users
  - GET `/api/admin/users?q=`
  - GET `/api/admin/users/{id}`
- Bookings
  - GET `/api/admin/bookings?status=&date_from=&date_to=`
  - GET `/api/admin/bookings/{id}`
- Payments
  - GET `/api/admin/payments?status=&gateway=&date_from=&date_to=&min_amount=&max_amount=`
  - GET `/api/admin/payments/{id}`
- Disputes
  - GET `/api/admin/disputes/payments`
  - GET `/api/admin/disputes/payments/{id}`
  - GET `/api/admin/disputes/bookings`
  - GET `/api/admin/disputes/bookings/{id}`
- Tickets
  - GET `/api/admin/tickets?status=&priority=`
  - GET `/api/admin/tickets/{id}`

## الملفات المضافة/المعدلة (أهم النقاط)
- الموديلات:
  - `app/Models/PaymentDispute.php`
  - `app/Models/BookingDispute.php`
  - `app/Models/Ticket.php`, `app/Models/TicketMessage.php`
  - علاقات جديدة:
    - `Payment::disputes()`، `Booking::disputes()`
- الميجريشن (جداول جديدة):
  - `payment_disputes`, `booking_disputes`, `tickets`, `ticket_messages`
- الكنترولرز (قراءة فقط):
  - `app/Http/Controllers/Admin/UserController.php`
  - `app/Http/Controllers/Admin/BookingController.php`
  - `app/Http/Controllers/Admin/PaymentController.php`
  - `app/Http/Controllers/Admin/PaymentDisputeController.php`
  - `app/Http/Controllers/Admin/BookingDisputeController.php`
  - `app/Http/Controllers/Admin/TicketController.php`
- المسارات:
  - API: `routes/api/admin.php`
  - Web: `routes/web.php` (مجموعة `/admin` مع `auth` + `role:admin`)
- القوالب (Blade):
  - `resources/views/admin/users/index.blade.php`
  - `resources/views/admin/bookings/index.blade.php`
  - `resources/views/admin/payments/index.blade.php`
  - `resources/views/admin/disputes/index.blade.php`
  - `resources/views/admin/tickets/index.blade.php`
- المصادقة:
  - تم تثبيت Laravel Breeze (Blade)
  - `routes/auth.php`: تم تعطيل مسارات التسجيل

## إعدادات وتشغيل
1. قواعد البيانات
   - شغّل الميجريشن: `php artisan migrate`
   - ملاحظة: في حال تعارض جداول محادثات/مرفقات مكررة، وحِّد الميجريشن أو أضف حمايات `Schema::hasTable`.
2. البث
   - تم ضبط `BROADCAST_DRIVER=log` مؤقتًا في `config/broadcasting.php` لتجنّب أخطاء مفاتيح Pusher/Reverb.
   - عند تهيئة المفاتيح، أعد القيمة إلى `reverb` وحدّث `.env` بمفاتيحك.
3. دور الأدمن
   - عيّن دور `admin` للمستخدم المستهدف عبر Spatie، أو أضف Seeder عند الحاجة.

## ما هو مدعوم الآن
- عرض قوائم وفلاتر أساسية للمستخدمين، الحجوزات، المدفوعات.
- عرض نزاعات مدفوعات/حجوزات (قراءة فقط).
- عرض تذاكر الدعم (قراءة فقط).

## خارطة الطريق المقترحة (لاحقًا)
- إضافة إجراءات تعديل/إغلاق للنزاعات والتذاكر.
- تغيير حالة الحجز من لوحة التحكم.
- تصدير CSV لقوائم المدفوعات.
- تنبيهات بريد/نظام عند الرد على التذاكر أو حسم النزاعات.

---

آخر تحديث: 2025-11-03

