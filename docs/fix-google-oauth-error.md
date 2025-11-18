# حل مشكلة "Access blocked: Authorization Error"

## 🔴 الخطأ
```
Access blocked: Authorization Error
Error 400: invalid_request
```

## 🔍 السبب
- الـ Client ID غير صحيح
- الـ Redirect URI غير مسجل في Google Console
- الـ Authorized JavaScript Origins غير صحيح

---

## ✅ الحل

### الخطوة 1: اذهب إلى Google Cloud Console
https://console.cloud.google.com/

### الخطوة 2: اختر المشروع
- اختر المشروع نفسه الـ Client ID بتاعه

### الخطوة 3: اذهب إلى Credentials
- **APIs & Services** > **Credentials**

### الخطوة 4: اضغط على OAuth Client ID
- اختر **Web application** (أو الـ existing client)

### الخطوة 5: أضف Authorized Origins

في قسم **Authorized JavaScript origins**، أضف:

```
http://localhost:8000
http://127.0.0.1:8000
http://localhost:3000
http://127.0.0.1:3000
```

### الخطوة 6: أضف Redirect URIs

في قسم **Authorized redirect URIs**، أضف:

```
http://localhost:8000/api/google/callback
http://127.0.0.1:8000/api/google/callback
http://localhost:3000/auth/google/callback
http://127.0.0.1:3000/auth/google/callback
```

### الخطوة 7: اضغط Save
- اضغط **Save**

### الخطوة 8: جديد جديد الصفحة

لو الخطأ ما راح:
1. اضغط F5 لـ refresh المتصفح
2. امسح الـ cookies: Settings > Clear browsing data > Cookies
3. جرب مرة ثانية

---

## 🎯 للتطبيق المحمول

إذا كنت تستخدم تطبيق محمول (React Native, Android, iOS)، لا تحتاج Redirect URI:

1. اختر **Android** أو **iOS** من قائمة Application type
2. أضف Package Name و SHA-1 Certificate Fingerprint
3. لا تحتاج إلى Redirect URI

---

## ⚙️ التحقق من الإعدادات

### تحقق من:
- ✅ اسم المشروع صحيح
- ✅ Client ID صحيح (يبدأ بـ `...apps.googleusercontent.com`)
- ✅ Origins و Redirect URIs مضافة بشكل صحيح
- ✅ لا توجد مسافات زائدة في الـ URLs
- ✅ جميع الـ URLs تبدأ بـ `http://` أو `https://`

---

## 🧪 اختبر بعد الإصلاح

### في المتصفح:
```javascript
// في الـ browser console
google.accounts.id.initialize({
    client_id: 'YOUR_CLIENT_ID',  // أدخل الـ Client ID صحيح
    callback: function(response) {
        console.log('Success:', response);
    }
});
```

### أو استخدم الملف:
افتح `test-google-login.html` وأدخل الـ Client ID صحيح

---

## 📋 قائمة المراجعة

- [ ] اذهبت إلى Google Cloud Console
- [ ] اختريت المشروع الصحيح
- [ ] اختريت OAuth 2.0 Client ID (Web application)
- [ ] أضفت `http://localhost:8000` في Authorized JavaScript origins
- [ ] أضفت `http://localhost:8000/api/google/callback` في Redirect URIs
- [ ] ضغطت Save
- [ ] refreshed الصفحة
- [ ] امسحت الـ cookies
- [ ] جربت مرة ثانية

---

## 🆘 لو المشكلة ما راحت

### تحقق من:

1. **هل الـ Client ID صحيح؟**
   - انسخه من Google Console
   - تأكد إنه يبدأ بـ `...apps.googleusercontent.com`

2. **هل الـ Origins صحيح؟**
   - للـ localhost: `http://localhost:8000`
   - للـ 127.0.0.1: `http://127.0.0.1:8000`
   - لا تضع port رقم مختلف

3. **هل الـ Redirect URI صحيح؟**
   - `http://localhost:8000/api/google/callback`
   - تأكد إن الـ backend API يستقبل هذا الـ path

4. **هل الـ Backend يعمل؟**
   ```bash
   php artisan serve
   ```
   تأكد إن الـ server يعمل على `http://127.0.0.1:8000`

5. **هل الـ .env صحيح؟**
   ```env
   GOOGLE_CLIENT_ID=YOUR_CLIENT_ID
   GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET
   GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/api/google/callback
   ```

---

## 🔗 روابط مهمة

- [Google Cloud Console](https://console.cloud.google.com/)
- [OAuth 2.0 Credentials](https://console.cloud.google.com/apis/credentials)
- [Google Identity Documentation](https://developers.google.com/identity/protocols/oauth2)

