# شرح كل Endpoint في Google OAuth

## 📋 الـ 5 Endpoints

```php
Route::get('/google-auth-url', [AuthController::class, 'getGoogleAuthUrl']);
Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/google/user-data', [AuthController::class, 'getGoogleUserData']);
Route::middleware('auth:sanctum')->get('/google/my-data', [AuthController::class, 'getMyGoogleData']);
```

---

## 1️⃣ `/api/google-auth-url` (GET)

### الفائدة:
**الحصول على رابط Google للدخول إليه**

### متى تستخدمه:
- في الفرونت إند (Web)
- تريد عمل OAuth Flow الكامل (redirect)

### الطلب:
```bash
curl --location 'http://127.0.0.1:8000/api/google-auth-url'
```

### الـ Response:
```json
{
  "success": true,
  "data": {
    "url": "https://accounts.google.com/o/oauth2/auth?client_id=...&redirect_uri=...&scope=...",
    "state": "random_state_value_12345"
  }
}
```

### الاستخدام في الفرونت:
```javascript
// 1. احصل على URL
const response = await fetch('http://127.0.0.1:8000/api/google-auth-url');
const { data } = await response.json();
const { url, state } = data;

// 2. حفظ state
localStorage.setItem('oauth_state', state);

// 3. وجّه المستخدم للـ URL
window.location.href = url;
```

### المميزات:
✅ آمن (يولد state عشوائي)
✅ يدعم OAuth Flow الكامل
✅ يرجع redirect_uri صحيح

---

## 2️⃣ `/api/google/callback` (GET)

### الفائدة:
**استقبال الـ callback من Google بعد تسجيل الدخول**

### متى يتم تنفيذه:
- تلقائياً بعد ما المستخدم يسجل دخول في Google
- Google ترجع المستخدم على هذا الـ endpoint

### الطلب (تلقائي):
```
http://127.0.0.1:8000/api/google/callback?code=AUTHORIZATION_CODE&state=STATE_VALUE
```

### الـ Response (للويب):
```json
// Redirect إلى frontend مع token
Location: http://localhost:3000/auth/google/callback?token=3|SANCTUM_TOKEN&success=true
```

### الـ Response (للموبايل):
```json
// Deep Link redirect
Location: myapp://auth/callback?token=3|SANCTUM_TOKEN&success=true
```

### الـ Response (للـ API):
```json
{
  "message": "Login successful with Google",
  "token": "3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd",
  "user": {
    "id": 50,
    "email": "hookshamosiba201555@gmail.com",
    "name": "Mohamed Samir",
    "google_id": "115524539257383648137",
    "profile_photo": "https://lh3.googleusercontent.com/..."
  }
}
```

### المميزات:
✅ يستبدل authorization code بـ tokens
✅ يتحقق من الـ state للأمان
✅ ينشئ/يحدث المستخدم تلقائياً
✅ يرجع Sanctum token

---

## 3️⃣ `/api/google-login` (POST)

### الفائدة:
**تسجيل دخول مباشر بـ Google ID Token (بدون redirect)**

### متى تستخدمه:
- من الموبايل (React Native, Android, iOS)
- من الفرونت إند مع Google Identity Services
- عندما تحصل على Google ID Token مباشرة

### الطلب:
```bash
curl --location 'http://127.0.0.1:8000/api/google-login' \
  --header 'Content-Type: application/json' \
  --data '{
    "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEyMzQ1Njc4OTAiLCJ0eXAiOiJKV1QifQ..."
  }'
```

### الـ Response:
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

### الاستخدام في الموبايل (React Native):
```javascript
import { GoogleSignin } from '@react-native-google-signin/google-signin';

// تسجيل الدخول
const userInfo = await GoogleSignin.signIn();
const googleIdToken = userInfo.idToken;

// إرسال للـ API
const response = await fetch('http://127.0.0.1:8000/api/google-login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    token: googleIdToken
  })
});

const data = await response.json();
// حفظ Sanctum token
await AsyncStorage.setItem('access_token', data.token);
```

### المميزات:
✅ لا يحتاج redirect
✅ آسرع (direct token)
✅ مثالي للموبايل
✅ validation صارم للـ token

---

## 4️⃣ `/api/google/user-data` (POST)

### الفائدة:
**جلب بيانات المستخدم من Google ID Token أو Authorization Code**

### متى تستخدمه:
- تريد معلومات المستخدم من Google بدون تسجيل دخول
- اختبار Token دون تسجيل دخول فعلي

### الطلب:
```bash
# مع Google ID Token
curl --location 'http://127.0.0.1:8000/api/google/user-data' \
  --header 'Content-Type: application/json' \
  --data '{
    "token": "eyJhbGciOiJSUzI1NiIs..."
  }'

# أو مع Authorization Code
curl --location 'http://127.0.0.1:8000/api/google/user-data' \
  --header 'Content-Type: application/json' \
  --data '{
    "token": "4/0AY0e-g4KZ3q...",
    "code": "4/0AY0e-g4KZ3q..."
  }'
```

### الـ Response:
```json
{
  "success": true,
  "data": {
    "email": "hookshamosiba201555@gmail.com",
    "name": "Mohamed Samir",
    "given_name": "Mohamed",
    "family_name": "Samir",
    "picture": "https://lh3.googleusercontent.com/...",
    "google_id": "115524539257383648137",
    "email_verified": true,
    "locale": "en"
  }
}
```

### المميزات:
✅ لا يسجل دخول (لا يعدل البيانات)
✅ فقط يجلب بيانات المستخدم
✅ مفيد للاختبار
✅ يقبل Token أو Authorization Code

---

## 5️⃣ `/api/google/my-data` (GET) ⭐ مع Auth

### الفائدة:
**جلب بيانات Google للمستخدم المسجل دخول فعلاً**

### متى تستخدمه:
- المستخدم مسجل دخول بالفعل
- تريد معلومات Google الخاصة به
- تريد التحقق من ربط Google Account

### المتطلبات:
- ✅ المستخدم **لازم يكون مسجل دخول** (Bearer token مطلوب)

### الطلب:
```bash
curl --location 'http://127.0.0.1:8000/api/google/my-data' \
  --header 'Authorization: Bearer 3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd'
```

### الـ Response:
```json
{
  "success": true,
  "data": {
    "email": "hookshamosiba201555@gmail.com",
    "name": "Mohamed Samir",
    "picture": "https://lh3.googleusercontent.com/...",
    "google_id": "115524539257383648137",
    "email_verified_at": "2025-11-18T16:34:24.000000Z",
    "profile_photo": "https://lh3.googleusercontent.com/...",
    "mobile": "google-115524539257383648137",
    "has_google_account": true
  }
}
```

### الاستخدام:
```javascript
// بعد تسجيل الدخول
const response = await fetch('http://127.0.0.1:8000/api/google/my-data', {
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('access_token')}`
  }
});

const data = await response.json();
console.log('Google data:', data.data);
```

### المميزات:
✅ آمن (يحتاج authorization)
✅ يجلب بيانات المستخدم المسجل فقط
✅ بيانات مخزنة في DB (سريعة)
✅ لا يحتاج Google API

---

## 📊 جدول المقارنة

| الـ Endpoint | الطريقة | الفائدة | متطلبات | استخدام |
|------------|--------|--------|----------|---------|
| `/google-auth-url` | GET | الحصول على رابط OAuth | - | الفرونت (Web) |
| `/google/callback` | GET | استقبال callback تلقائي | Google redirect | الفرونت (Web) |
| `/google-login` | POST | تسجيل دخول مباشر | Google ID Token | الموبايل / الفرونت |
| `/google/user-data` | POST | جلب بيانات بدون login | Google ID Token | الاختبار / التحقق |
| `/google/my-data` | GET | جلب بيانات المستخدم | Sanctum Token | بعد الـ login |

---

## 🎯 أي endpoint تستخدم؟

### للموبايل (✅ الموصى به):
```
1. استخدم Google Sign-In SDK
2. احصل على Google ID Token
3. أرسلها لـ POST /api/google-login
4. احصل على Sanctum Token
5. استخدمها في كل الـ API requests
```

### للفرونت إند (الويب):
```
الطريقة 1 (OAuth Flow):
1. GET /api/google-auth-url
2. وجّه المستخدم للـ URL
3. Google يرجعه على GET /api/google/callback
4. احصل على Sanctum Token

الطريقة 2 (Direct):
1. استخدم Google Identity Services
2. احصل على Google ID Token
3. أرسلها لـ POST /api/google-login
4. احصل على Sanctum Token
```

### للاختبار والتطوير:
```
1. استخدم POST /api/google/user-data (بدون login)
2. أو GET /api/google/my-data (مع login)
```

---

## 💾 Flow الكامل

### للموبايل:
```
Google Sign-In SDK
      ↓
احصل على Google ID Token (JWT)
      ↓
POST /api/google-login
      ↓
ينشئ/يحدث المستخدم
      ↓
يرجع Sanctum Token + User Data
      ↓
احفظ Sanctum Token
      ↓
استخدمه في كل الـ API requests
```

### للفرونت (الويب):
```
GET /api/google-auth-url
      ↓
احصل على رابط OAuth + state
      ↓
وجّه المستخدم للـ رابط
      ↓
المستخدم يسجل دخول Google
      ↓
Google ترجعه على GET /api/google/callback?code=...&state=...
      ↓
ينشئ/يحدث المستخدم
      ↓
يرجع redirect مع Sanctum Token
      ↓
احفظ Sanctum Token
      ↓
استخدمه في كل الـ API requests
```

---

## ⚠️ ملاحظات مهمة

1. **Google ID Token (JWT)**
   - من Google مباشرة
   - صيغة: `eyJ...` (3 أجزاء)
   - صالح لمدة ساعة

2. **Sanctum Token**
   - من Laravel
   - صيغة: `id|token`
   - يستخدم في كل الـ requests بعد الـ login

3. **Authorization Code**
   - من Google
   - صيغة: `4/0AY0e-g4KZ3q...`
   - صالح لدقائق فقط

4. **الأمان**
   - `/google-login` و `/google/user-data` بتحتاج Google ID Token
   - `/google/my-data` بتحتاج Sanctum Token
   - `/google/callback` آمن (يتحقق من state)

