# اختبار POST /api/google-login مع الموبايل

## 🎯 الهدف
اختبار الـ endpoint الذي يسجل دخول المستخدم بـ Google ID Token

---

## 🔧 الطريقة 1: Postman (الأسهل للاختبار)

### الخطوة 1: فتح Postman
- افتح Postman
- أنشئ request جديد
- اختر **POST**
- أدخل URL: `http://127.0.0.1:8000/api/google-login`

### الخطوة 2: الحصول على Google ID Token

#### من Google OAuth 2.0 Playground:
1. اذهب إلى: https://developers.google.com/oauthplayground/
2. اختر Scopes:
   - ✅ `openid`
   - ✅ `https://www.googleapis.com/auth/userinfo.email`
   - ✅ `https://www.googleapis.com/auth/userinfo.profile`
3. اضغط **Authorize APIs**
4. سجل دخول بحسابك
5. اضغط **Exchange authorization code for tokens**
6. انسخ الـ `id_token` (الجزء اللي يبدأ بـ `eyJ...`)

### الخطوة 3: في Postman

#### Headers:
```
Content-Type: application/json
Accept: application/json
```

#### Body (raw JSON):
```json
{
  "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEyMzQ1Njc4OTAiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIxMjM0NTY3ODkwLWFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXVkIjoiMTIzNDU2Nzg5MC1hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsInN1YiI6IjExNTUyNDUzOTI1NzM4MzY0ODEzNyIsImVtYWlsIjoiaG9va3NoYW1vc2liYTIwMTU1NUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6Ik1vaGFtZWQgU2FtaXIiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSjFhcXEyMFk2QXY2SnE2ZlNveHVUZWJ5WkpvY3BrUG5seTR2X090RzNNVU1YT1FST3A9czk2LWMiLCJpYXQiOjE3MzQ1Njg0NjQsImV4cCI6MTczNDU3MjA2NH0.abc123def456..."
}
```

### الخطوة 4: اضغط Send

### ✅ النتيجة الناجحة:
```json
{
  "message": "Login successful with Google",
  "token": "3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd",
  "user": {
    "id": 50,
    "name": "Mohamed Samir",
    "email": "hookshamosiba201555@gmail.com",
    "mobile": "google-115524539257383648137",
    "email_verified_at": "2025-11-18T16:34:24.000000Z",
    "profile_photo": "https://lh3.googleusercontent.com/a/...",
    "updated_at": "2025-11-18T16:34:24.000000Z",
    "created_at": "2025-11-18T16:34:24.000000Z",
    "google_id": "115524539257383648137"
  }
}
```

---

## 🔧 الطريقة 2: curl (من Terminal)

```bash
# 1. احصل على Google ID Token (من Playground)

# 2. استبدل YOUR_GOOGLE_ID_TOKEN بـ الـ token الفعلي
curl --location 'http://127.0.0.1:8000/api/google-login' \
  --header 'Content-Type: application/json' \
  --header 'Accept: application/json' \
  --data '{
    "token": "YOUR_GOOGLE_ID_TOKEN"
  }'
```

### مثال كامل:
```bash
curl --location 'http://127.0.0.1:8000/api/google-login' \
  --header 'Content-Type: application/json' \
  --header 'Accept: application/json' \
  --data '{
    "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEyMzQ1Njc4OTAiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIxMjM0NTY3ODkwLWFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXVkIjoiMTIzNDU2Nzg5MC1hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsInN1YiI6IjExNTUyNDUzOTI1NzM4MzY0ODEzNyIsImVtYWlsIjoiaG9va3NoYW1vc2liYTIwMTU1NUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6Ik1vaGFtZWQgU2FtaXIiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSjFhcXEyMFk2QXY2SnE2ZlNveHVUZWJ5WkpvY3BrUG5seTR2X090RzNNVU1YT1FST3A9czk2LWMiLCJpYXQiOjE3MzQ1Njg0NjQsImV4cCI6MTczNDU3MjA2NH0.abc123def456..."
  }'
```

---

## 📱 الطريقة 3: React Native

### التثبيت:
```bash
npm install @react-native-google-signin/google-signin
```

### الإعداد:
```javascript
import { GoogleSignin } from '@react-native-google-signin/google-signin';

// في أول الـ app
GoogleSignin.configure({
  webClientId: 'YOUR_GOOGLE_CLIENT_ID',
  offlineAccess: true,
});
```

### الاختبار:
```javascript
async function testGoogleLogin() {
  try {
    // 1. تسجيل الدخول عبر Google
    await GoogleSignin.hasPlayServices();
    const userInfo = await GoogleSignin.signIn();
    
    console.log('User Info:', userInfo);
    console.log('ID Token:', userInfo.idToken);
    
    // 2. إرسال ID Token للـ API
    const response = await fetch('http://127.0.0.1:8000/api/google-login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        token: userInfo.idToken
      })
    });
    
    const data = await response.json();
    
    if (response.ok) {
      console.log('✅ Login Successful!');
      console.log('Sanctum Token:', data.token);
      console.log('User:', data.user);
      
      // 3. حفظ الـ token
      await AsyncStorage.setItem('access_token', data.token);
      
      // 4. توجيه المستخدم
      navigation.navigate('Home');
    } else {
      console.error('❌ Login Failed:', data);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}
```

---

## 🤖 الطريقة 4: Android (Kotlin)

### التثبيت:
في `build.gradle`:
```gradle
implementation 'com.google.android.gms:play-services-auth:20.0.0'
```

### الكود:
```kotlin
import com.google.android.gms.auth.api.signin.GoogleSignIn
import com.google.android.gms.auth.api.signin.GoogleSignInOptions
import com.google.android.gms.auth.api.signin.GoogleSignInAccount
import com.google.android.gms.common.api.ApiException
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONObject

class GoogleLoginActivity : AppCompatActivity() {
    
    private lateinit var googleSignInClient: GoogleSignInClient
    private val RC_SIGN_IN = 1001
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // إعداد Google Sign-In
        val gso = GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
            .requestIdToken("YOUR_GOOGLE_CLIENT_ID")
            .requestEmail()
            .build()
        
        googleSignInClient = GoogleSignIn.getClient(this, gso)
    }
    
    fun testGoogleLogin() {
        val signInIntent = googleSignInClient.signInIntent
        startActivityForResult(signInIntent, RC_SIGN_IN)
    }
    
    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        
        if (requestCode == RC_SIGN_IN) {
            val task = GoogleSignIn.getSignedInAccountFromIntent(data)
            try {
                val account = task.getResult(ApiException::class.java)
                
                // الحصول على ID Token
                val idToken = account?.idToken
                if (idToken != null) {
                    // إرسال للـ API
                    sendTokenToApi(idToken)
                }
            } catch (e: ApiException) {
                Log.e("GoogleLogin", "Sign-in failed", e)
            }
        }
    }
    
    private fun sendTokenToApi(idToken: String) {
        val client = OkHttpClient()
        val json = JSONObject()
        json.put("token", idToken)
        
        val requestBody = json.toString()
            .toRequestBody("application/json".toMediaType())
        
        val request = Request.Builder()
            .url("http://127.0.0.1:8000/api/google-login")
            .post(requestBody)
            .addHeader("Content-Type", "application/json")
            .addHeader("Accept", "application/json")
            .build()
        
        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                Log.e("API", "Error:", e)
            }
            
            override fun onResponse(call: Call, response: Response) {
                val responseBody = response.body?.string()
                val jsonResponse = JSONObject(responseBody)
                
                if (response.isSuccessful) {
                    Log.d("API", "✅ Login Successful!")
                    val token = jsonResponse.getString("token")
                    val user = jsonResponse.getJSONObject("user")
                    
                    Log.d("API", "Sanctum Token: $token")
                    Log.d("API", "User: $user")
                    
                    // حفظ الـ token
                    saveToken(token)
                } else {
                    Log.e("API", "❌ Login Failed: $jsonResponse")
                }
            }
        })
    }
    
    private fun saveToken(token: String) {
        val sharedPref = getSharedPreferences("auth", Context.MODE_PRIVATE)
        sharedPref.edit().apply {
            putString("access_token", token)
            apply()
        }
    }
}
```

---

## 🍎 الطريقة 5: iOS (Swift)

### التثبيت:
في `Podfile`:
```ruby
pod 'GoogleSignIn', '~> 7.0'
```

### الكود:
```swift
import GoogleSignIn
import Alamofire

class GoogleLoginViewController: UIViewController {
    
    func testGoogleLogin() {
        let signInConfig = GIDConfiguration(clientID: "YOUR_GOOGLE_CLIENT_ID")
        GIDSignIn.sharedInstance.configuration = signInConfig
        
        GIDSignIn.sharedInstance.signIn(withPresenting: self) { result, error in
            guard let user = result?.user,
                  let idToken = user.idToken?.tokenString else {
                print("Sign-in error: \(error?.localizedDescription ?? "Unknown")")
                return
            }
            
            // إرسال ID Token للـ API
            self.sendTokenToApi(idToken: idToken)
        }
    }
    
    private func sendTokenToApi(idToken: String) {
        let url = "http://127.0.0.1:8000/api/google-login"
        let parameters: [String: Any] = [
            "token": idToken
        ]
        
        AF.request(url,
                   method: .post,
                   parameters: parameters,
                   encoding: JSONEncoding.default,
                   headers: ["Content-Type": "application/json", "Accept": "application/json"])
            .responseJSON { response in
                switch response.result {
                case .success(let json):
                    if let dict = json as? [String: Any] {
                        print("✅ Login Successful!")
                        
                        if let token = dict["token"] as? String {
                            print("Sanctum Token: \(token)")
                        }
                        
                        if let user = dict["user"] as? [String: Any] {
                            print("User: \(user)")
                        }
                        
                        // حفظ الـ token
                        if let token = dict["token"] as? String {
                            UserDefaults.standard.set(token, forKey: "access_token")
                        }
                    }
                    
                case .failure(let error):
                    print("❌ Login Failed: \(error)")
                }
            }
    }
}
```

---

## ✅ علامات نجاح الاختبار

### يجب أن تحصل على:
- ✅ HTTP Status 200
- ✅ `"message": "Login successful with Google"`
- ✅ Sanctum Token (صيغة: `id|token`)
- ✅ User data مع `google_id`

### مثال النتيجة الناجحة:
```json
{
  "message": "Login successful with Google",
  "token": "3|NMtSaTwkcvbMgFevW220VDNDRcHs7ZUIkNY5DAe8897a86dd",
  "user": {
    "id": 50,
    "email": "hookshamosiba201555@gmail.com",
    "name": "Mohamed Samir",
    "google_id": "115524539257383648137",
    "profile_photo": "https://..."
  }
}
```

---

## ❌ أخطاء شائعة وحلولها

### خطأ: "Invalid token type"
```json
{
  "message": "Invalid token type",
  "error": "The provided token appears to be a Laravel Sanctum token..."
}
```
**الحل:** تأكد إنك بتبعت Google ID Token، مش Sanctum token

### خطأ: "Invalid token format - not a JWT"
```json
{
  "message": "Invalid token format - not a JWT",
  "token_format": {
    "parts_count": 1
  }
}
```
**الحل:** الـ token لازم يكون JWT (3 أجزاء مفصولة بنقاط)

### خطأ: "Token too short"
```json
{
  "message": "Token too short",
  "token_length": 50
}
```
**الحل:** انسخ الـ token كامل من Playground

### خطأ: "Invalid Google token"
```json
{
  "message": "Invalid Google token",
  "error": "Unable to verify Google ID token..."
}
```
**الحل:** 
- الـ token منتهي الصلاحية (احصل على token جديد)
- الـ Client ID غير متطابق

---

## 📋 خطوات الاختبار الكاملة

### للاختبار السريع (Postman):
1. ✅ افتح Postman
2. ✅ اذهب إلى Google Playground واحصل على ID Token
3. ✅ أنشئ POST request
4. ✅ الصق الـ token في Body
5. ✅ اضغط Send
6. ✅ تحقق من النتيجة

### للموبايل الفعلي:
1. ✅ ثبّت Google Sign-In SDK
2. ✅ أعد Google Sign-In
3. ✅ اكتب function للـ sign-in
4. ✅ احصل على ID Token
5. ✅ أرسله للـ API
6. ✅ احفظ Sanctum Token
7. ✅ استخدمه في الـ requests التالية

---

## 🎯 التوصية

**للاختبار السريع:** استخدم Postman مع Google OAuth 2.0 Playground
**للموبايل الفعلي:** استخدم Google Sign-In SDK الخاص بكل منصة

