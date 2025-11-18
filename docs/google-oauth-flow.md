# Google OAuth Flow - الخطوات بالترتيب

## الطريقة 1: OAuth Flow الكامل (مع Redirect)

### الخطوة 1: الحصول على Google Auth URL
```bash
GET /api/google-auth-url
```

**Response:**
```json
{
  "success": true,
  "data": {
    "url": "https://accounts.google.com/o/oauth2/auth?...",
    "state": "random_state_value"
  }
}
```

### الخطوة 2: توجيه المستخدم للـ URL
- افتح الـ `url` في المتصفح
- المستخدم يسجل دخول Google
- Google ترجع المستخدم على `/api/google/callback?code=AUTHORIZATION_CODE&state=STATE_VALUE`

### الخطوة 3: Google Callback (تلقائي)
```bash
GET /api/google/callback?code=AUTHORIZATION_CODE&state=STATE_VALUE
```

**Response:**
```json
{
  "message": "Login successful with Google",
  "token": "14|5Lnp5otRJmC18jKJk7BCYrqrmEaLdhEnPDMDgTZAcc35a1e1",
  "user": {
    "id": 51,
    "name": "Mohamed Samir",
    "email": "hookshamosiba201555@gmail.com",
    "google_id": "1155245392573",
    ...
  }
}
```

---

## الطريقة 2: Google Identity Services (ID Token مباشر)

### الخطوة 1: في الفرونت إند - إعداد Google Identity Services
```html
<script src="https://accounts.google.com/gsi/client" async defer></script>
```

```javascript
google.accounts.id.initialize({
  client_id: 'YOUR_GOOGLE_CLIENT_ID',
  callback: handleCredentialResponse
});

function handleCredentialResponse(response) {
  // response.credential هو Google ID token (JWT)
  const googleIdToken = response.credential;
  
  // أرسله للـ API
  loginWithGoogle(googleIdToken);
}
```

### الخطوة 2: إرسال Google ID Token للـ API
```bash
POST /api/google-login
Content-Type: application/json

{
  "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6..."
}
```

**Response:**
```json
{
  "message": "Login successful with Google",
  "token": "14|5Lnp5otRJmC18jKJk7BCYrqrmEaLdhEnPDMDgTZAcc35a1e1",
  "user": {
    "id": 51,
    "name": "Mohamed Samir",
    "email": "hookshamosiba201555@gmail.com",
    "google_id": "1155245392573",
    ...
  }
}
```

---

## الطريقة 3: جلب بيانات Google للمستخدم المسجل دخول

### الخطوة 1: تسجيل الدخول أولاً (بأي طريقة)
```bash
POST /api/login
# أو
POST /api/google-login
```

### الخطوة 2: استخدام Sanctum Token لجلب بيانات Google
```bash
GET /api/google/my-data
Authorization: Bearer 14|5Lnp5otRJmC18jKJk7BCYrqrmEaLdhEnPDMDgTZAcc35a1e1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "email": "hookshamosiba201555@gmail.com",
    "name": "Mohamed Samir",
    "picture": "https://lh3.googleusercontent.com/...",
    "google_id": "1155245392573",
    "has_google_account": true
  }
}
```

---

## ملخص الـ Endpoints

| Endpoint | Method | Auth Required | الوصف |
|----------|--------|----------------|-------|
| `/api/google-auth-url` | GET | ❌ | الحصول على Google OAuth URL |
| `/api/google/callback` | GET | ❌ | معالجة callback من Google |
| `/api/google-login` | POST | ❌ | تسجيل الدخول بـ Google ID token |
| `/api/google/user-data` | POST | ❌ | جلب بيانات من Google ID token |
| `/api/google/my-data` | GET | ✅ | جلب بيانات Google للمستخدم المسجل |

---

## مثال كامل - OAuth Flow

### 1. Frontend: طلب Google Auth URL
```javascript
const response = await fetch('/api/google-auth-url');
const { data } = await response.json();
const { url, state } = data;

// حفظ state في localStorage أو session
localStorage.setItem('oauth_state', state);

// توجيه المستخدم للـ URL
window.location.href = url;
```

### 2. Google Callback (تلقائي)
- Google ترجع المستخدم على `/api/google/callback?code=...&state=...`
- الـ backend يعالج الـ callback تلقائياً
- يرجع token و user data

### 3. Frontend: استخدام الـ Token
```javascript
// بعد الـ callback، استخدم الـ token في كل الـ requests
fetch('/api/user', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

---

## مثال كامل - Google Identity Services

### 1. Frontend: إعداد Google Sign-In Button
```html
<div id="g_id_onload"
     data-client_id="YOUR_GOOGLE_CLIENT_ID"
     data-callback="handleCredentialResponse">
</div>
<div class="g_id_signin" data-type="standard"></div>
```

### 2. Frontend: معالجة الـ Response
```javascript
function handleCredentialResponse(response) {
  const googleIdToken = response.credential;
  
  // إرسال للـ API
  fetch('/api/google-login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      token: googleIdToken
    })
  })
  .then(res => res.json())
  .then(data => {
    // حفظ الـ token
    localStorage.setItem('access_token', data.token);
    // توجيه المستخدم للصفحة الرئيسية
    window.location.href = '/dashboard';
  });
}
```

---

## ملاحظات مهمة

1. **Google ID Token vs Sanctum Token:**
   - Google ID Token: `eyJhbGciOiJSUzI1NiIsImtpZCI6...` (JWT format)
   - Sanctum Token: `14|5Lnp5otRJmC18jKJk7BCYrqrmEaLdhEnPDMDgTZAcc35a1e1` (id|token format)

2. **Authorization Code:**
   - صالح لدقائق قليلة فقط
   - لازم تستخدمه فوراً بعد ما تجيبه

3. **Google ID Token:**
   - صالح لفترة أطول
   - يمكن استخدامه مباشرة

4. **Security:**
   - استخدم HTTPS في production
   - تحقق من الـ state في OAuth flow
   - لا تشارك الـ tokens في logs أو errors

