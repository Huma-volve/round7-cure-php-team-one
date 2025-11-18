# ماذا يحدث في Database عند Google Login

## 🔄 Flow الكامل

عندما الموبايل يبعت Google ID Token لـ `POST /api/google-login`، هذا ما يحدث:

---

## 📋 الخطوات بالتفصيل

### 1️⃣ التحقق من Google ID Token
```php
$payload = $client->verifyIdToken($token);
```
- ✅ يتحقق من صحة الـ token
- ✅ يستخرج بيانات المستخدم من Google

### 2️⃣ البحث عن المستخدم أو إنشاؤه
```php
$user = User::updateOrCreate(
    ['email' => $payload['email']],  // البحث بالـ email
    [
        // البيانات اللي هتتحفظ
    ]
);
```

**ماذا يحدث:**
- 🔍 **يبحث** عن user بالـ email في الـ database
- ✅ **لو موجود**: يحدث البيانات
- ➕ **لو مش موجود**: ينشئ user جديد

---

## 💾 البيانات اللي بتتسجل في Database

### الحقول اللي بتتسجل/تتحدث:

| الحقل | القيمة | ملاحظات |
|------|-------|---------|
| `email` | من Google | ✅ مفتاح البحث |
| `name` | من Google | ✅ الاسم الكامل |
| `google_id` | من Google (`sub`) | ✅ معرف Google الفريد |
| `mobile` | `google-{sub}` | ⚠️ إذا لم يكن رقم هاتف في Google |
| `password` | Random hash | 🔒 يتم إنشاء password عشوائي |
| `email_verified_at` | `now()` | ✅ يتم التحقق من البريد تلقائياً |
| `profile_photo` | من Google (`picture`) | 📷 صورة الملف الشخصي |

### مثال:
```php
User::updateOrCreate(
    ['email' => 'hookshamosiba201555@gmail.com'],
    [
        'name' => 'Mohamed Samir',
        'google_id' => '115524539257383648137',
        'mobile' => 'google-1155245392573',
        'password' => Hash::make(Str::random(32)),
        'email_verified_at' => '2025-11-18 17:43:46',
        'profile_photo' => 'https://lh3.googleusercontent.com/a/...',
    ]
);
```

---

## 🔄 السيناريوهات المختلفة

### السيناريو 1: User جديد (أول مرة)
```
1. البحث بالـ email → ❌ مش موجود
2. إنشاء user جديد
3. حفظ كل البيانات
4. تعيين role: patient
5. إنشاء Sanctum token
```

**النتيجة:**
- ✅ User جديد في الـ database
- ✅ كل البيانات محفوظة
- ✅ Role: patient
- ✅ Sanctum token جاهز

---

### السيناريو 2: User موجود (تسجيل دخول مرة ثانية)
```
1. البحث بالـ email → ✅ موجود
2. تحديث البيانات:
   - تحديث name (لو تغير)
   - تحديث google_id (لو تغير)
   - تحديث profile_photo (لو تغير)
   - تحديث email_verified_at
3. الحفاظ على:
   - password القديم (لو كان موجود)
   - mobile (لو كان موجود)
   - باقي البيانات
4. تعيين role: patient (لو مش موجود)
5. إنشاء Sanctum token جديد
```

**النتيجة:**
- ✅ البيانات محدثة
- ✅ Sanctum token جديد
- ✅ User مسجل دخول

---

### السيناريو 3: User موجود لكن بدون google_id
```
1. البحث بالـ email → ✅ موجود
2. تحديث google_id
3. تحديث باقي البيانات
4. تعيين role: patient
5. إنشاء Sanctum token
```

**النتيجة:**
- ✅ ربط Google Account بالحساب الموجود
- ✅ Sanctum token جاهز

---

## 📊 مثال من الـ Database

### قبل Google Login:
```sql
users table:
id | email                          | name | google_id | mobile | email_verified_at
---|--------------------------------|------|-----------|--------|------------------
50 | hookshamosiba201555@gmail.com  | NULL | NULL      | NULL   | NULL
```

### بعد Google Login:
```sql
users table:
id | email                          | name          | google_id           | mobile                  | email_verified_at
---|--------------------------------|---------------|---------------------|-------------------------|------------------
50 | hookshamosiba201555@gmail.com  | Mohamed Samir | 115524539257383648137 | google-1155245392573 | 2025-11-18 17:43:46
```

---

## 🔐 الأمان والخصوصية

### Password:
- ✅ يتم إنشاء password عشوائي تلقائياً
- ✅ مش لازم المستخدم يعرف الـ password
- ✅ لو user موجود، الـ password القديم يفضل كما هو

### Email Verification:
- ✅ `email_verified_at` يتم تعيينه تلقائياً
- ✅ لأن Google verified الـ email

### Google ID:
- ✅ يتم حفظه دائماً
- ✅ يتم تحديثه لو تغير
- ✅ يستخدم للربط بين Google Account والـ User

---

## 🎭 Roles (الأدوار)

### Role: Patient
```php
if (!$user->hasRole('patient')) {
    $user->assignRole('patient');
}
```

**ماذا يحدث:**
- ✅ لو user جديد → يتم تعيين role `patient`
- ✅ لو user موجود بدون role → يتم تعيين role `patient`
- ✅ لو user موجود مع role → يفضل كما هو

---

## 🔑 Sanctum Token

### بعد حفظ البيانات:
```php
Auth::login($user);
$token = $user->createToken('auth_token')->plainTextToken;
```

**ماذا يحدث:**
- ✅ تسجيل دخول المستخدم في Laravel
- ✅ إنشاء Sanctum token جديد
- ✅ Token يتم إرجاعه في الـ response

**الـ Token:**
- 📝 Format: `id|random_string`
- ⏰ صالح حتى يتم حذفه
- 🔒 يستخدم في كل الـ API requests

---

## 📝 Response الكامل

```json
{
  "message": "Login successful with Google",
  "token": "6|Oj7PagGeqOBlOlhUZsQpi8GUrT8ONcVfD6go1Fx900c81e35",
  "user": {
    "id": 50,
    "name": "Mohamed Samir",
    "email": "hookshamosiba201555@gmail.com",
    "google_id": "115524539257383648137",
    "mobile": "google-1155245392573",
    "email_verified_at": "2025-11-18T17:43:46.000000Z",
    "profile_photo": "https://lh3.googleusercontent.com/...",
    "roles": [
      {
        "name": "patient"
      }
    ]
  }
}
```

---

## ✅ الخلاصة

### نعم، البيانات بتتسجل في Database! ✅

**ما يحدث:**
1. ✅ البحث عن user بالـ email
2. ✅ إنشاء user جديد أو تحديث الموجود
3. ✅ حفظ/تحديث البيانات:
   - name, email, google_id
   - mobile, profile_photo
   - email_verified_at
4. ✅ تعيين role: patient
5. ✅ إنشاء Sanctum token
6. ✅ إرجاع البيانات + token

**النتيجة:**
- ✅ User موجود في الـ database
- ✅ كل البيانات محفوظة
- ✅ جاهز للاستخدام في التطبيق

---

## 🔍 للتحقق من Database

### في Laravel Tinker:
```bash
php artisan tinker
```

```php
// البحث عن user
$user = User::where('email', 'hookshamosiba201555@gmail.com')->first();
$user->google_id;  // 115524539257383648137
$user->name;       // Mohamed Samir
$user->roles;      // patient
```

### في Database مباشرة:
```sql
SELECT * FROM users WHERE email = 'hookshamosiba201555@gmail.com';
SELECT * FROM model_has_roles WHERE model_id = 50;
```

---

## ⚠️ ملاحظات مهمة

1. **Email هو المفتاح:**
   - البحث يتم بالـ email فقط
   - لو email موجود، يتم التحديث
   - لو email جديد، يتم الإنشاء

2. **Google ID يتم تحديثه دائماً:**
   - حتى لو user موجود
   - للتأكد من الربط الصحيح

3. **Password:**
   - User جديد: password عشوائي
   - User موجود: password القديم يفضل

4. **Role:**
   - دائماً `patient` للـ Google login
   - لو user موجود مع role آخر، يفضل كما هو

5. **Email Verification:**
   - دائماً `verified` لأن Google verified الـ email

