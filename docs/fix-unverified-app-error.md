# حل مشكلة "Unverified App" و "Access blocked: Authorization Error"

## 🔴 المشكلة
```
Access blocked: Authorization Error
Error 400: invalid_request

You can't sign in to this app because it doesn't comply with Google's OAuth 2.0 policy
```

## 🔍 السبب
الـ OAuth app بتاعك لم يتم التحقق منه من قبل Google. Google بتحتاج تعرف إن الـ app آمن.

---

## ✅ الحل (هناك عدة طرق)

### الطريقة 1: للتطوير والاختبار المحلي (الأسهل)

#### الخطوة 1: اضغط "Learn more about this error"
في رسالة الخطأ، اضغط على الرابط

#### الخطوة 2: اختر "advanced" أو انقر على الرابط الخاص بـ Developer
ستظهر لك خيارات إضافية

#### الخطوة 3: اضغط "Go to Cure (unsafe)"
هذا فقط للاختبار المحلي والتطوير

---

### الطريقة 2: إضافة نفسك كـ Test User (الموصى به للتطوير)

#### الخطوة 1: اذهب إلى Google Cloud Console
https://console.cloud.google.com/

#### الخطوة 2: اختر المشروع

#### الخطوة 3: اذهب إلى **APIs & Services > OAuth consent screen**

#### الخطوة 4: في قسم "Test users"، اضغط **Add users**

#### الخطوة 5: أضف بريدك الإلكتروني
```
hookshamosiba201555@gmail.com
```

#### الخطوة 6: اضغط **Save**

#### الخطوة 7: جرب مرة ثانية

---

### الطريقة 3: نشر الـ App للإنتاج (للـ Production)

إذا كنت تريد نشر الـ app للعموم، لازم تمرر Google OAuth verification:

#### الخطوة 1: اذهب إلى OAuth consent screen
**APIs & Services > OAuth consent screen**

#### الخطوة 2: اختر **External** (للعموم)

#### الخطوة 3: ملء بيانات الـ App:
- **App name**: Cure
- **User support email**: support@curehealthcare.com
- **App logo**: أضف شعار الـ app
- **Developer contact information**: بريدك الإلكتروني

#### الخطوة 4: أضف Scopes:
- ✅ `openid`
- ✅ `email`
- ✅ `profile`

#### الخطوة 5: أضف Test Users:
```
hookshamosiba201555@gmail.com
```

#### الخطوة 6: اضغط **Save and continue**

#### الخطوة 7: انتظر Google تتحقق من الـ app

---

## 📋 خطوات سريعة للتطوير المحلي

1. اذهب إلى Google Cloud Console
2. اختر المشروع
3. اذهب إلى **APIs & Services > OAuth consent screen**
4. اختر **User Type**: External
5. اضغط **Create**
6. اضغط **Add users** في Test users
7. أضف بريدك: `hookshamosiba201555@gmail.com`
8. اضغط **Save**
9. جرب مرة ثانية

---

## ⚙️ تحقق من الإعدادات

### في Google Cloud Console:

1. **Credentials > OAuth 2.0 Client ID**
   - ✅ Type: Web application
   - ✅ Authorized JavaScript origins: `http://localhost:8000`
   - ✅ Authorized redirect URIs: `http://localhost:8000/api/google/callback`

2. **OAuth consent screen**
   - ✅ App name: Cure
   - ✅ User support email: ملء
   - ✅ Test users: أضفت بريدك

3. **في .env الخاص بك:**
   ```env
   GOOGLE_CLIENT_ID=i63215826461-6jpquivkb3h2is7lcqha8hkmd4dq69ia.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=YOUR_SECRET
   GOOGLE_REDIRECT_URI=http://localhost:8000/api/google/callback
   ```

---

## 🧪 لو لم ينجح الحل

### الحل الفوري: استخدم Google OAuth 2.0 Playground

بدل ما تستخدم الـ app الخاص بك، استخدم:
https://developers.google.com/oauthplayground/

#### الخطوات:
1. اختر الـ scopes:
   - ✅ `openid`
   - ✅ `email`
   - ✅ `profile`

2. اضغط **Authorize APIs**

3. سجل دخول بـ `hookshamosiba201555@gmail.com`

4. اضغط **Exchange authorization code for tokens**

5. انسخ الـ `id_token`

6. استخدمه في الـ API:
   ```bash
   curl --location 'http://127.0.0.1:8000/api/google-login' \
     --header 'Content-Type: application/json' \
     --data '{
       "token": "eyJhbGciOiJSUzI1NiIs..."
     }'
   ```

---

## 🎯 التوصيات

### للتطوير والاختبار:
1. أضف نفسك كـ Test User ← **الأسهل والأفضل**
2. أو استخدم Google OAuth 2.0 Playground

### للـ Production:
1. اتبع خطوات Google OAuth verification
2. أضف بيانات كاملة عن الـ app
3. انتظر Google تتحقق من الـ app (قد تأخذ أيام)

---

## 📚 روابط مهمة

- [Google OAuth Consent Screen](https://console.cloud.google.com/apis/credentials/consent)
- [OAuth 2.0 Playground](https://developers.google.com/oauthplayground/)
- [Google OAuth Documentation](https://developers.google.com/identity/protocols/oauth2)

---

## ✅ قائمة المراجعة

- [ ] اذهبت إلى OAuth consent screen
- [ ] اخترت User Type: External
- [ ] ملأت بيانات الـ app
- [ ] أضفت Test users
- [ ] أضفت بريدك: hookshamosiba201555@gmail.com
- [ ] ضغطت Save
- [ ] جربت مرة ثانية

