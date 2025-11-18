# خطوات استخدام Google OAuth 2.0 Playground - خطوة بخطوة

## 🎯 الهدف
الحصول على Google ID Token للاختبار

---

## 📋 الخطوات بالتفصيل

### الخطوة 1: اختر الـ Scopes

في القائمة اللي على اليسار، **ابحث عن** أو **اختر**:

#### الطريقة أ: من القائمة
1. ابحث في القائمة عن:
   - ✅ **"OpenID Connect"** أو **"OAuth2 API v2"**
   - ✅ **"User Info API"**

2. أو استخدم الـ search box في أعلى القائمة

#### الطريقة ب: أدخل الـ Scopes يدوياً (الأسهل) ⭐

في حقل **"Input your own scopes"** في الأسفل، أدخل:

```
openid
https://www.googleapis.com/auth/userinfo.email
https://www.googleapis.com/auth/userinfo.profile
```

أو في سطر واحد:
```
openid https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile
```

---

### الخطوة 2: اضغط "Authorize APIs"

1. بعد ما أدخلت الـ scopes، اضغط الزر الأزرق **"Authorize APIs"**
2. سيفتح نافذة جديدة لـ Google Sign-In
3. **سجل دخول** بحسابك Google (مثلاً: hookshamosiba201555@gmail.com)
4. **وافق** على الـ permissions
5. سيرجعك للـ Playground تلقائياً

---

### الخطوة 3: Exchange authorization code

1. بعد ما رجعت للـ Playground، ستظهر **"Step 2"** مفتوحة
2. اضغط الزر الأزرق **"Exchange authorization code for tokens"**
3. ستظهر لك الـ tokens في الـ Response على اليمين

---

### الخطوة 4: انسخ ID Token

في الـ Response على اليمين، ستجد JSON مثل:

```json
{
  "access_token": "ya29.a0AfH6SMC...",
  "expires_in": 3599,
  "refresh_token": "1//04...",
  "scope": "openid https://www.googleapis.com/auth/userinfo.email ...",
  "token_type": "Bearer",
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEyMzQ1Njc4OTAiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIxMjM0NTY3ODkwLWFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXVkIjoiMTIzNDU2Nzg5MC1hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsInN1YiI6IjExNTUyNDUzOTI1NzM4MzY0ODEzNyIsImVtYWlsIjoiaG9va3NoYW1vc2liYTIwMTU1NUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6Ik1vaGFtZWQgU2FtaXIiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSjFhcXEyMFk2QXY2SnE2ZlNveHVUZWJ5WkpvY3BrUG5seTR2X090RzNNVU1YT1FST3A9czk2LWMiLCJpYXQiOjE3MzQ1Njg0NjQsImV4cCI6MTczNDU3MjA2NH0.abc123def456..."
}
```

**انسخ قيمة `id_token`** (الجزء اللي يبدأ بـ `eyJ...`)

---

### الخطوة 5: استخدم الـ Token في Postman

1. افتح Postman
2. أنشئ request جديد:
   - **Method:** POST
   - **URL:** `http://127.0.0.1:8000/api/google-login`
3. في **Headers:**
   ```
   Content-Type: application/json
   Accept: application/json
   ```
4. في **Body** (raw JSON):
   ```json
   {
     "token": "eyJhbGciOiJSUzI1NiIs..."  ← الصق الـ id_token هنا
   }
   ```
5. اضغط **Send**

---

## 🎯 ملخص سريع

```
1. أدخل scopes: openid email profile
2. اضغط "Authorize APIs"
3. سجل دخول Google
4. اضغط "Exchange authorization code"
5. انسخ id_token
6. استخدمه في Postman
```

---

## ⚠️ ملاحظات مهمة

1. **الـ ID Token صالح لمدة ساعة فقط**
   - لو انتهت، احصل على token جديد

2. **الـ Token طويل جداً**
   - انسخه كامل (يبدأ بـ `eyJ` وينتهي بـ `...`)

3. **تأكد من الـ Scopes**
   - لازم يكون `openid` موجود
   - لازم يكون `email` و `profile` موجودين

4. **لو ما ظهرت "Step 2"**
   - تأكد إنك سجلت دخول بنجاح
   - جرب مرة ثانية

---

## 🧪 اختبار سريع

بعد ما نسخت الـ token، جرب في Terminal:

```bash
curl --location 'http://127.0.0.1:8000/api/google-login' \
  --header 'Content-Type: application/json' \
  --data '{
    "token": "YOUR_ID_TOKEN_HERE"
  }'
```

---

## ✅ النتيجة المتوقعة

```json
{
  "message": "Login successful with Google",
  "token": "3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd",
  "user": {
    "id": 50,
    "email": "hookshamosiba201555@gmail.com",
    "name": "Mohamed Samir",
    "google_id": "115524539257383648137"
  }
}
```

---

## 🆘 لو واجهت مشاكل

### المشكلة: ما فيش "Exchange authorization code" button
**الحل:** تأكد إنك سجلت دخول بنجاح في الخطوة 2

### المشكلة: الـ id_token مش موجود
**الحل:** تأكد إنك أدخلت `openid` في الـ scopes

### المشكلة: "Invalid token" في Postman
**الحل:** 
- انسخ الـ token كامل (من `eyJ` لحد النهاية)
- تأكد إن الـ token مش منتهي الصلاحية

