# أنواع الـ Tokens في Google OAuth

## 🔑 الفرق بين Google ID Token و Sanctum Token

### ❌ الخطأ الشائع

```bash
# ❌ خطأ - هذا Sanctum token، مش Google ID token
curl --location 'http://127.0.0.1:8000/api/google-login' \
  --header 'Content-Type: application/json' \
  --data '{
    "token": "3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd"
  }'
```

**النتيجة:**
```json
{
  "message": "Invalid token type",
  "error": "The provided token appears to be a Laravel Sanctum token, not a Google ID token."
}
```

---

## ✅ Google ID Token (المطلوب)

### الشكل:
```
eyJhbGciOiJSUzI1NiIsImtpZCI6IjEyMzQ1Njc4OTAiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIxMjM0NTY3ODkwLWFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXVkIjoiMTIzNDU2Nzg5MC1hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsInN1YiI6IjExNTUyNDUzOTI1NzM4MzY0ODEzNyIsImVtYWlsIjoiaG9va3NoYW1vc2liYTIwMTU1NUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6Ik1vaGFtZWQgU2FtaXIiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSjFhcXEyMFk2QXY2SnE2ZlNveHVUZWJ5WkpvY3BrUG5seTR2X090RzNNVU1YT1FST3A9czk2LWMiLCJpYXQiOjE3MzQ1Njg0NjQsImV4cCI6MTczNDU3MjA2NH0.abc123def456...
```

### الخصائص:
- ✅ **3 أجزاء** مفصولة بنقاط (`.`)
- ✅ **يبدأ بـ `eyJ`** (base64 encoded JSON header)
- ✅ **طويل** (أكثر من 500 حرف عادة)
- ✅ **من Google** مباشرة
- ✅ **يستخدم مرة واحدة** للـ login فقط

---

## ❌ Sanctum Token (غير صحيح)

### الشكل:
```
3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd
```

### الخصائص:
- ❌ **جزء واحد** أو جزئين مفصولين بـ `|`
- ❌ **قصير** (أقل من 100 حرف عادة)
- ❌ **من Laravel** بعد تسجيل الدخول
- ❌ **يستخدم** في كل الـ API requests بعد الـ login

---

## 📱 كيفية الحصول على Google ID Token في الموبايل

### React Native

```javascript
import { GoogleSignin } from '@react-native-google-signin/google-signin';

// إعداد Google Sign-In
GoogleSignin.configure({
  webClientId: 'YOUR_GOOGLE_CLIENT_ID', // من Google Console
  offlineAccess: true,
});

// تسجيل الدخول
async function signInWithGoogle() {
  try {
    await GoogleSignin.hasPlayServices();
    const userInfo = await GoogleSignin.signIn();
    
    // ✅ هذا هو Google ID Token (JWT)
    const googleIdToken = userInfo.idToken;
    console.log('Google ID Token:', googleIdToken);
    // Output: eyJhbGciOiJSUzI1NiIsImtpZCI6...
    
    // إرسال للـ API
    const response = await fetch('http://127.0.0.1:8000/api/google-login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        token: googleIdToken  // ✅ Google ID Token
      })
    });
    
    const data = await response.json();
    
    // ✅ هذا هو Sanctum Token (يستخدمه بعدين)
    const sanctumToken = data.token;
    console.log('Sanctum Token:', sanctumToken);
    // Output: 3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd
    
    // حفظ Sanctum Token للاستخدام المستقبلي
    await AsyncStorage.setItem('access_token', sanctumToken);
    
  } catch (error) {
    console.error('Error:', error);
  }
}
```

### Android (Kotlin)

```kotlin
import com.google.android.gms.auth.api.signin.GoogleSignIn
import com.google.android.gms.auth.api.signin.GoogleSignInOptions
import com.google.android.gms.auth.api.signin.GoogleSignInAccount
import com.google.android.gms.common.api.ApiException

// إعداد Google Sign-In
val gso = GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
    .requestIdToken("YOUR_GOOGLE_CLIENT_ID")  // ✅ مهم: requestIdToken
    .requestEmail()
    .build()

val googleSignInClient = GoogleSignIn.getClient(this, gso)

// بدء تسجيل الدخول
val signInIntent = googleSignInClient.signInIntent
startActivityForResult(signInIntent, RC_SIGN_IN)

// في onActivityResult
override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
    super.onActivityResult(requestCode, resultCode, data)
    
    if (requestCode == RC_SIGN_IN) {
        val task = GoogleSignIn.getSignedInAccountFromIntent(data)
        try {
            val account = task.getResult(ApiException::class.java)
            
            // ✅ هذا هو Google ID Token (JWT)
            val googleIdToken = account.idToken
            Log.d("Google", "ID Token: $googleIdToken")
            
            // إرسال للـ API
            sendTokenToApi(googleIdToken)
            
        } catch (e: ApiException) {
            Log.e("Google", "Sign-in failed", e)
        }
    }
}

fun sendTokenToApi(googleIdToken: String) {
    // API call
    val requestBody = JSONObject().apply {
        put("token", googleIdToken)  // ✅ Google ID Token
    }
    
    // ... HTTP request
}
```

### iOS (Swift)

```swift
import GoogleSignIn

// إعداد Google Sign-In
let signInConfig = GIDConfiguration(clientID: "YOUR_GOOGLE_CLIENT_ID")
GIDSignIn.sharedInstance.configuration = signInConfig

// تسجيل الدخول
GIDSignIn.sharedInstance.signIn(withPresenting: self) { result, error in
    guard let user = result?.user,
          let idToken = user.idToken?.tokenString else {
        print("Error: \(error?.localizedDescription ?? "Unknown error")")
        return
    }
    
    // ✅ هذا هو Google ID Token (JWT)
    print("Google ID Token: \(idToken)")
    
    // إرسال للـ API
    sendTokenToApi(idToken: idToken)
}

func sendTokenToApi(idToken: String) {
    let url = URL(string: "http://127.0.0.1:8000/api/google-login")!
    var request = URLRequest(url: url)
    request.httpMethod = "POST"
    request.setValue("application/json", forHTTPHeaderField: "Content-Type")
    
    let body: [String: Any] = [
        "token": idToken  // ✅ Google ID Token
    ]
    
    request.httpBody = try? JSONSerialization.data(withJSONObject: body)
    
    // ... URLSession request
}
```

---

## 🔄 Flow الكامل

```
1. الموبايل → Google Sign-In SDK
   ↓
2. Google → يعيد Google ID Token (JWT)
   ↓
3. الموبايل → يرسل Google ID Token للـ API
   POST /api/google-login
   {
     "token": "eyJhbGciOiJSUzI1NiIs..."  ← Google ID Token
   }
   ↓
4. Backend → يتحقق من Google ID Token
   ↓
5. Backend → ينشئ/يحدث المستخدم
   ↓
6. Backend → يرجع Sanctum Token
   {
     "token": "3|NMtSaTwkcvbMgFevW220..."  ← Sanctum Token
   }
   ↓
7. الموبايل → يحفظ Sanctum Token
   ↓
8. الموبايل → يستخدم Sanctum Token في كل الـ API requests
   Authorization: Bearer 3|NMtSaTwkcvbMgFevW220...
```

---

## ✅ مثال صحيح

```bash
# 1. الحصول على Google ID Token من الموبايل
# (يتم في التطبيق باستخدام Google Sign-In SDK)

# 2. إرسال Google ID Token للـ API
curl --location 'http://127.0.0.1:8000/api/google-login' \
  --header 'Content-Type: application/json' \
  --data '{
    "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEyMzQ1Njc4OTAiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIxMjM0NTY3ODkwLWFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXVkIjoiMTIzNDU2Nzg5MC1hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsInN1YiI6IjExNTUyNDUzOTI1NzM4MzY0ODEzNyIsImVtYWlsIjoiaG9va3NoYW1vc2liYTIwMTU1NUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6Ik1vaGFtZWQgU2FtaXIiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSjFhcXEyMFk2QXY2SnE2ZlNveHVUZWJ5WkpvY3BrUG5seTR2X090RzNNVU1YT1FST3A9czk2LWMiLCJpYXQiOjE3MzQ1Njg0NjQsImV4cCI6MTczNDU3MjA2NH0.abc123def456..."
  }'

# 3. الـ Response (Sanctum Token)
{
  "message": "Login successful with Google",
  "token": "3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd",
  "user": { ... }
}

# 4. استخدام Sanctum Token في الـ requests التالية
curl --location 'http://127.0.0.1:8000/api/user' \
  --header 'Authorization: Bearer 3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd'
```

---

## ❌ مثال خاطئ

```bash
# ❌ خطأ - استخدام Sanctum Token بدل Google ID Token
curl --location 'http://127.0.0.1:8000/api/google-login' \
  --header 'Content-Type: application/json' \
  --data '{
    "token": "3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd"
  }'

# النتيجة:
{
  "success": false,
  "message": "Invalid token type",
  "error": "The provided token appears to be a Laravel Sanctum token, not a Google ID token."
}
```

---

## 📝 ملخص

| النوع | الشكل | الاستخدام | من أين |
|------|------|----------|--------|
| **Google ID Token** | `eyJ...` (JWT, 3 أجزاء) | للـ login فقط | Google Sign-In SDK |
| **Sanctum Token** | `id\|token` (جزء واحد) | لكل الـ API requests | Laravel بعد الـ login |

---

## 🔍 كيفية التحقق من نوع الـ Token

### Google ID Token:
```javascript
// في JavaScript
const token = "eyJhbGciOiJSUzI1NiIs...";
const parts = token.split('.');
console.log(parts.length); // 3
console.log(token.startsWith('eyJ')); // true
console.log(token.length); // > 500
```

### Sanctum Token:
```javascript
// في JavaScript
const token = "3|NMtSaTwkcvbMgFevW220...";
console.log(token.includes('|')); // true
console.log(token.length); // < 100
```

---

## ⚠️ ملاحظات مهمة

1. **Google ID Token** يستخدم **مرة واحدة فقط** للـ login
2. **Sanctum Token** يستخدم في **كل الـ API requests** بعد الـ login
3. **لا تخلط** بين الاثنين!
4. **Google ID Token** يأتي من **Google Sign-In SDK** فقط
5. **Sanctum Token** يأتي من **Laravel** بعد تسجيل الدخول الناجح

