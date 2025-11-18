# كيفية الحصول على Google ID Token

## الطريقة 1: Google Identity Services (الأسهل - للفرونت إند)

### الخطوة 1: إضافة Google Script في HTML
```html
<!DOCTYPE html>
<html>
<head>
    <title>Google Sign-In</title>
</head>
<body>
    <!-- Google Identity Services -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    
    <!-- Google Sign-In Button -->
    <div id="g_id_onload"
         data-client_id="YOUR_GOOGLE_CLIENT_ID"
         data-callback="handleCredentialResponse">
    </div>
    <div class="g_id_signin" 
         data-type="standard" 
         data-size="large" 
         data-theme="outline" 
         data-text="sign_in_with" 
         data-shape="rectangular" 
         data-logo_alignment="left">
    </div>

    <script>
        function handleCredentialResponse(response) {
            // response.credential هو Google ID token (JWT)
            const googleIdToken = response.credential;
            
            console.log('Google ID Token:', googleIdToken);
            // Token format: eyJhbGciOiJSUzI1NiIsImtpZCI6...
            
            // أرسله للـ API
            sendToBackend(googleIdToken);
        }

        function sendToBackend(token) {
            fetch('http://127.0.0.1:8000/api/google-login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    token: token
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Login successful:', data);
                // حفظ Sanctum token
                localStorage.setItem('access_token', data.token);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
```

### الخطوة 2: الحصول على Google Client ID
1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/)
2. أنشئ مشروع جديد أو اختر مشروع موجود
3. اذهب إلى **APIs & Services** > **Credentials**
4. اضغط **Create Credentials** > **OAuth client ID**
5. اختر **Web application**
6. أضف **Authorized JavaScript origins**: `http://localhost:8000`
7. أضف **Authorized redirect URIs**: `http://localhost:8000/api/google/callback`
8. انسخ **Client ID** واستخدمه في الكود

---

## الطريقة 2: OAuth Flow الكامل (للـ Backend أو Mobile Apps)

### الخطوة 1: الحصول على Google Auth URL
```bash
curl --location 'http://127.0.0.1:8000/api/google-auth-url'
```

**Response:**
```json
{
  "success": true,
  "data": {
    "url": "https://accounts.google.com/o/oauth2/auth?client_id=...&redirect_uri=...&response_type=code&scope=...",
    "state": "random_state_value"
  }
}
```

### الخطوة 2: توجيه المستخدم للـ URL
- افتح الـ `url` في المتصفح
- المستخدم يسجل دخول Google
- Google ترجع المستخدم على `/api/google/callback?code=AUTHORIZATION_CODE&state=STATE_VALUE`

### الخطوة 3: الـ Backend يعالج الـ Callback تلقائياً
- الـ backend يستبدل الـ authorization code بـ access token و ID token
- يرجع Sanctum token للمستخدم

---

## الطريقة 3: استخدام Google OAuth 2.0 Playground (للاختبار فقط)

### الخطوة 1: اذهب إلى [Google OAuth 2.0 Playground](https://developers.google.com/oauthplayground/)

### الخطوة 2: اختر Scopes
- ✅ `openid`
- ✅ `https://www.googleapis.com/auth/userinfo.email`
- ✅ `https://www.googleapis.com/auth/userinfo.profile`

### الخطوة 3: Authorize APIs
- اضغط **Authorize APIs**
- سجل دخول Google
- وافق على الـ permissions

### الخطوة 4: Exchange authorization code for tokens
- اضغط **Exchange authorization code for tokens**
- ستحصل على:
  - `access_token`
  - `id_token` ← هذا هو Google ID Token المطلوب
  - `refresh_token`

### الخطوة 5: استخدم الـ ID Token
```bash
curl --location 'http://127.0.0.1:8000/api/google-login' \
--header 'Content-Type: application/json' \
--data '{
    "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6..."  # ID Token من Playground
}'
```

---

## الطريقة 4: استخدام Postman (للاختبار)

### الخطوة 1: إنشاء OAuth 2.0 Request في Postman
1. افتح Postman
2. أنشئ Request جديد
3. اختر **Authorization** tab
4. اختر **OAuth 2.0**
5. املأ البيانات:
   - **Grant Type**: Authorization Code
   - **Callback URL**: `http://127.0.0.1:8000/api/google/callback`
   - **Auth URL**: `https://accounts.google.com/o/oauth2/auth`
   - **Access Token URL**: `https://oauth2.googleapis.com/token`
   - **Client ID**: Google Client ID
   - **Client Secret**: Google Client Secret
   - **Scope**: `openid email profile`

### الخطوة 2: Get New Access Token
- اضغط **Get New Access Token**
- سجل دخول Google
- ستحصل على tokens

### الخطوة 3: استخدام ID Token
- انسخ **ID Token** من الـ response
- استخدمه في `/api/google-login`

---

## مثال كامل - React/Vue/Angular

### React Example:
```jsx
import { useEffect } from 'react';

function GoogleSignIn() {
  useEffect(() => {
    // Load Google Identity Services
    const script = document.createElement('script');
    script.src = 'https://accounts.google.com/gsi/client';
    script.async = true;
    script.defer = true;
    document.body.appendChild(script);

    script.onload = () => {
      window.google.accounts.id.initialize({
        client_id: 'YOUR_GOOGLE_CLIENT_ID',
        callback: handleCredentialResponse,
      });

      window.google.accounts.id.renderButton(
        document.getElementById('google-signin-button'),
        { theme: 'outline', size: 'large' }
      );
    };

    return () => {
      document.body.removeChild(script);
    };
  }, []);

  function handleCredentialResponse(response) {
    const googleIdToken = response.credential;
    
    // Send to backend
    fetch('http://127.0.0.1:8000/api/google-login', {
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
      console.log('Login successful:', data);
      localStorage.setItem('access_token', data.token);
    });
  }

  return <div id="google-signin-button"></div>;
}
```

---

## ملاحظات مهمة

1. **Google ID Token Format:**
   - JWT (JSON Web Token)
   - 3 أجزاء مفصولة بنقط: `header.payload.signature`
   - يبدأ بـ `eyJ` (base64 encoded JSON)

2. **Token Validity:**
   - Google ID tokens صالحة لمدة ساعة تقريباً
   - يمكن استخدامها مباشرة للـ login

3. **Security:**
   - لا تشارك Google ID tokens في logs
   - استخدم HTTPS في production
   - تحقق من الـ token في الـ backend دائماً

4. **Testing:**
   - للاختبار السريع: استخدم Google OAuth 2.0 Playground
   - للاختبار في Postman: استخدم OAuth 2.0 flow
   - للـ production: استخدم Google Identity Services في الفرونت إند

---

## Troubleshooting

### مشكلة: "Invalid token"
- تأكد إن الـ token JWT format (3 أجزاء مفصولة بنقط)
- تأكد إن الـ token يبدأ بـ `eyJ`

### مشكلة: "Token expired"
- Google ID tokens صالحة لمدة ساعة فقط
- احصل على token جديد

### مشكلة: "Client ID mismatch"
- تأكد إن الـ Client ID صحيح
- تأكد إن الـ redirect URI متطابق مع الـ configured في Google Console

