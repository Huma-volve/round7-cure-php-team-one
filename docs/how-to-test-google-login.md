# كيفية اختبار Google Login - دليل سريع

## 🚀 الطريقة الأسهل: استخدام ملف HTML للاختبار

### الخطوة 1: افتح ملف الاختبار
افتح الملف `test-google-login.html` في المتصفح

### الخطوة 2: أدخل Google Client ID
1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/)
2. اختر مشروعك أو أنشئ مشروع جديد
3. اذهب إلى **APIs & Services** > **Credentials**
4. انسخ **Client ID** (يبدأ بـ `...apps.googleusercontent.com`)
5. الصقه في الملف HTML

### الخطوة 3: اضغط "Sign in with Google"
- سيفتح نافذة تسجيل الدخول
- سجل دخول بحساب Google
- سيتم الحصول على Google ID Token تلقائياً
- سيتم إرساله للـ API تلقائياً وستظهر النتيجة

---

## 🔧 الطريقة 2: Google OAuth 2.0 Playground (للاختبار السريع)

### الخطوة 1: اذهب إلى Playground
[Google OAuth 2.0 Playground](https://developers.google.com/oauthplayground/)

### الخطوة 2: اختر Scopes
في الجانب الأيسر، اختر:
- ✅ `openid`
- ✅ `https://www.googleapis.com/auth/userinfo.email`
- ✅ `https://www.googleapis.com/auth/userinfo.profile`

### الخطوة 3: Authorize APIs
1. اضغط **Authorize APIs** (أعلى اليسار)
2. سجل دخول Google
3. وافق على الـ permissions

### الخطوة 4: Exchange authorization code
1. اضغط **Exchange authorization code for tokens**
2. ستحصل على:
   - `access_token`
   - **`id_token`** ← هذا هو Google ID Token المطلوب
   - `refresh_token`

### الخطوة 5: استخدم ID Token
انسخ الـ `id_token` واستخدمه في:

```bash
curl --location 'http://127.0.0.1:8000/api/google-login' \
  --header 'Content-Type: application/json' \
  --data '{
    "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6..."
  }'
```

---

## 🌐 الطريقة 3: استخدام Google Identity Services في المتصفح

### إنشاء ملف HTML بسيط:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Google Login Test</title>
</head>
<body>
    <div id="google-signin-button"></div>
    
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        window.onload = function() {
            google.accounts.id.initialize({
                client_id: 'YOUR_GOOGLE_CLIENT_ID',
                callback: handleCredentialResponse
            });
            
            google.accounts.id.renderButton(
                document.getElementById('google-signin-button'),
                { theme: 'outline', size: 'large' }
            );
        };
        
        function handleCredentialResponse(response) {
            const googleIdToken = response.credential;
            console.log('Google ID Token:', googleIdToken);
            
            // إرسال للـ API
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
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
```

---

## 📱 الطريقة 4: من الموبايل (React Native)

```javascript
import { GoogleSignin } from '@react-native-google-signin/google-signin';

GoogleSignin.configure({
  webClientId: 'YOUR_GOOGLE_CLIENT_ID',
  offlineAccess: true,
});

async function signInWithGoogle() {
  try {
    await GoogleSignin.hasPlayServices();
    const userInfo = await GoogleSignin.signIn();
    
    // ✅ هذا هو Google ID Token
    const googleIdToken = userInfo.idToken;
    console.log('Google ID Token:', googleIdToken);
    
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
    console.log('Login result:', data);
  } catch (error) {
    console.error('Error:', error);
  }
}
```

---

## 🔍 كيفية التحقق من Google ID Token

### الشكل الصحيح:
```
eyJhbGciOiJSUzI1NiIsImtpZCI6IjEyMzQ1Njc4OTAiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIxMjM0NTY3ODkwLWFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXVkIjoiMTIzNDU2Nzg5MC1hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsInN1YiI6IjExNTUyNDUzOTI1NzM4MzY0ODEzNyIsImVtYWlsIjoiaG9va3NoYW1vc2liYTIwMTU1NUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6Ik1vaGFtZWQgU2FtaXIiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSjFhcXEyMFk2QXY2SnE2ZlNveHVUZWJ5WkpvY3BrUG5seTR2X090RzNNVU1YT1FST3A9czk2LWMiLCJpYXQiOjE3MzQ1Njg0NjQsImV4cCI6MTczNDU3MjA2NH0.abc123def456...
```

### الخصائص:
- ✅ **3 أجزاء** مفصولة بنقاط (`.`)
- ✅ **يبدأ بـ `eyJ`**
- ✅ **طويل** (أكثر من 500 حرف)

---

## 🧪 اختبار سريع بـ curl

```bash
# استبدل YOUR_GOOGLE_ID_TOKEN بالـ token الفعلي
curl --location 'http://127.0.0.1:8000/api/google-login' \
  --header 'Content-Type: application/json' \
  --header 'Accept: application/json' \
  --data '{
    "token": "YOUR_GOOGLE_ID_TOKEN"
  }'
```

---

## 📋 قائمة التحقق

- [ ] حصلت على Google Client ID من Google Cloud Console
- [ ] أضفت Client ID في ملف HTML أو التطبيق
- [ ] حصلت على Google ID Token (JWT format)
- [ ] الـ token يبدأ بـ `eyJ`
- [ ] الـ token له 3 أجزاء مفصولة بنقاط
- [ ] أرسلت الـ token للـ API `/api/google-login`
- [ ] حصلت على Sanctum Token في الـ response

---

## ⚠️ أخطاء شائعة

### خطأ: "Invalid token type"
**السبب:** أرسلت Sanctum token بدل Google ID token
**الحل:** استخدم Google ID Token من Google Sign-In SDK

### خطأ: "Wrong number of segments"
**السبب:** الـ token مش JWT format
**الحل:** تأكد إن الـ token له 3 أجزاء مفصولة بنقاط

### خطأ: "Token expired"
**السبب:** الـ token منتهي الصلاحية
**الحل:** احصل على token جديد (Google ID tokens صالحة لمدة ساعة)

### خطأ: "Client ID mismatch"
**السبب:** الـ Client ID مش متطابق
**الحل:** تأكد إن الـ Client ID صحيح في Google Console

---

## 🎯 التوصية

للاختبار السريع: استخدم ملف `test-google-login.html` - أسهل طريقة!

