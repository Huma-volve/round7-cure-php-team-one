# Complete API Documentation

## Table of Contents
1. [Authentication - Google OAuth](#authentication---google-oauth)
2. [FAQs](#faqs)
3. [Contact Us](#contact-us)
4. [Contact Info](#contact-info)
5. [Payment Methods](#payment-methods)
6. [Patient Bookings](#patient-bookings)
7. [Doctor Bookings](#doctor-bookings)
8. [Payment Processing](#payment-processing)

---

## Authentication - Google OAuth

### Overview
The authentication system supports Google OAuth 2.0 with multiple scenarios:
1. **Full Authorization Code Flow** - Web/Mobile redirect-based login
2. **ID Token Direct Login** - Mobile app direct token submission
3. **Fetch Google Data** - Verify and extract user data from Google tokens
4. **Get Authenticated User Data** - Retrieve stored Google data for logged-in users

---

### 1. Get Google Auth URL
**Generate an OAuth authorization URL for the frontend/mobile app**

```
GET /api/google-auth-url
```

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Query Parameters (Optional):**
| Parameter | Type | Description |
|-----------|------|-------------|
| state | string | Optional state parameter for security. Can be used for deep linking in mobile apps (e.g., `mobile://callback` or `app://auth`) |

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/google-auth-url" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "url": "https://accounts.google.com/o/oauth2/v2/auth?client_id=...",
    "state": "random_state_value"
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Google Client ID not configured",
  "error": "GOOGLE_CLIENT_ID is not set in .env file"
}
```

**Use Cases:**
- Frontend: Open the URL in browser for user to consent
- Mobile App: Open URL in in-app browser or system browser

---

### 2. Handle Google OAuth Callback
**Process the authorization code from Google and return authentication token**

```
POST /api/google/callback
```

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "code": "4/0AX4XfWg...",
  "state": "random_state_value"
}
```

**Field Validation:**
| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| code | string | required | Authorization code from Google OAuth |
| state | string | nullable | State parameter for CSRF protection |

**Request Example:**
```bash
curl -X POST "http://localhost:8000/api/google/callback" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "code": "4/0AX4XfWg...",
    "state": "random_state_value"
  }'
```

**Response (200 OK):**
```json
{
  "message": "Login successful with Google",
  "token": "1|abcdef123456",
  "user": {
    "id": 50,
    "email": "user@gmail.com",
    "name": "John Doe",
    "mobile": "google-115524539257383648137",
    "google_id": "115524539257383648137",
    "profile_photo": "https://lh3.googleusercontent.com/...",
    "email_verified_at": "2025-11-18T16:34:24.000000Z",
    "created_at": "2025-11-18T16:34:24.000000Z",
    "updated_at": "2025-11-18T16:34:24.000000Z"
  }
}
```

**Error Responses:**

**(400) - Code Exchange Failed:**
```json
{
  "success": false,
  "message": "Unable to exchange authorization code with Google",
  "error": "Error details from Google"
}
```

**(400) - No ID Token:**
```json
{
  "success": false,
  "message": "Google response did not include an ID token"
}
```

**(401) - Token Verification Failed:**
```json
{
  "success": false,
  "message": "Unable to verify Google ID token"
}
```

**Flow for Mobile Apps:**
1. Open auth URL with state: `mobile://callback`
2. User consents in browser
3. Google redirects to callback endpoint with code
4. App receives code and calls this endpoint
5. App stores the returned token locally

---

### 3. Direct Google Login with ID Token
**Login using Google ID Token (JWT) directly from mobile app**

```
POST /api/google-login
```

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {sanctum_token} (optional for API route)
```

**Request Body:**
```json
{
  "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6ImE1NzMz..."
}
```

**Field Validation:**
| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| token | string | required, JWT format | Google ID Token (3 parts: header.payload.signature) |

**Request Example:**
```bash
curl -X POST "http://localhost:8000/api/google-login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6ImE1NzMzYmJi..."
  }'
```

**Response (200 OK):**
```json
{
  "message": "Login successful with Google",
  "token": "1|xyz123abc456",
  "user": {
    "id": 50,
    "email": "user@gmail.com",
    "name": "John Doe",
    "mobile": "google-115524539257383648137",
    "google_id": "115524539257383648137",
    "profile_photo": "https://lh3.googleusercontent.com/...",
    "email_verified_at": "2025-11-18T16:34:24.000000Z",
    "created_at": "2025-11-18T16:34:24.000000Z",
    "updated_at": "2025-11-18T16:34:24.000000Z"
  }
}
```

**Error Responses:**

**(400) - Sanctum Token Sent Instead of Google Token:**
```json
{
  "success": false,
  "message": "Invalid token type",
  "error": "The provided token appears to be a Laravel Sanctum token, not a Google ID token",
  "hint": "Google ID tokens must be in JWT format (3 parts separated by dots, starting with eyJ...)",
  "received_token_preview": "1|abcdef...",
  "token_length": 45
}
```

**(400) - Invalid JWT Format:**
```json
{
  "success": false,
  "message": "Invalid token format - not a JWT",
  "error": "Google ID tokens must be in JWT format with exactly 3 parts separated by dots",
  "hint": "The token you sent has 1 part(s), but Google ID tokens must have exactly 3 parts",
  "token_format": {
    "parts_count": 1,
    "starts_with_eyJ": false,
    "length": 45,
    "preview": "1|abcdef..."
  },
  "expected_format": "eyJ... (3 parts separated by dots)"
}
```

**(401) - Token Verification Failed:**
```json
{
  "success": false,
  "message": "Invalid Google token",
  "error": "Unable to verify Google ID token. The token may be expired, invalid, or Client ID does not match",
  "hint": "Make sure you are sending a fresh Google ID token from Google Sign-In SDK",
  "token_info": {
    "audience": "407408718192.apps.googleusercontent.com",
    "issuer": "https://accounts.google.com",
    "expires_at": "2025-11-18 20:35:07",
    "is_expired": true,
    "configured_client_id": "656321582640.apps.googleusercontent.com",
    "client_id_match": false
  }
}
```

**Mobile App Integration:**
```javascript
// Using Google Identity Services (Web)
google.accounts.id.initialize({
  client_id: 'YOUR_CLIENT_ID.apps.googleusercontent.com'
});

// Get ID token
google.accounts.id.requestIdToken({
  callback: async (response) => {
    const token = response.credential; // This is the JWT
    
    // Send to backend
    const result = await fetch('/api/google-login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ token })
    });
    
    const data = await result.json();
    localStorage.setItem('auth_token', data.token);
  }
});
```

---

### 4. Fetch Google User Data from Token
**Verify a Google token and extract user information (useful before login)**

```
POST /api/google/user-data
```

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6ImE1NzMz...",
  "code": null
}
```

**Field Validation:**
| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| token | string | required | Google ID Token (JWT) or Authorization Code |
| code | string | nullable | Alternative authorization code parameter |

**Request Example:**
```bash
curl -X POST "http://localhost:8000/api/google/user-data" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "token": "eyJhbGciOiJSUzI1NiIsImtpZCI6ImE1NzMzYmJi..."
  }'
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "email": "user@gmail.com",
    "name": "John Doe",
    "given_name": "John",
    "family_name": "Doe",
    "picture": "https://lh3.googleusercontent.com/...",
    "google_id": "115524539257383648137",
    "email_verified": true,
    "locale": "en",
    "full_payload": {
      "iss": "https://accounts.google.com",
      "aud": "407408718192.apps.googleusercontent.com",
      "sub": "115524539257383648137",
      "email": "user@gmail.com",
      "email_verified": true,
      "name": "John Doe",
      "given_name": "John",
      "family_name": "Doe",
      "picture": "https://lh3.googleusercontent.com/...",
      "locale": "en"
    }
  }
}
```

**Error Responses:**

**(400) - Invalid Token Type:**
```json
{
  "success": false,
  "message": "Invalid token type",
  "error": "The provided token appears to be a Laravel Sanctum token, not a Google ID token",
  "hint": "If you want to get Google data for authenticated user, use GET /api/google/my-data instead"
}
```

**(401) - Token Verification Failed:**
```json
{
  "success": false,
  "message": "Invalid Google token"
}
```

**Use Cases:**
- Validate Google token before creating account
- Extract user information for preview
- Verify token authenticity

---

### 5. Get Authenticated User Google Data
**Retrieve stored Google data for the currently authenticated user**

```
GET /api/google/my-data
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/google/my-data" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "email": "user@gmail.com",
    "name": "John Doe",
    "picture": "https://lh3.googleusercontent.com/...",
    "google_id": "115524539257383648137",
    "email_verified_at": "2025-11-18T16:34:24.000000Z",
    "profile_photo": "https://lh3.googleusercontent.com/...",
    "mobile": "google-115524539257383648137",
    "has_google_account": true
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

**Use Cases:**
- Display logged-in user's Google profile
- Check if user has a Google account linked
- Retrieve stored profile information

---

## FAQs

### List All FAQs with Pagination

```
GET /api/faqs
```

**Headers:**
```
Accept: application/json
Accept-Language: en (or ar for Arabic)
```

**Query Parameters:**
| Parameter | Type | Default | Max | Description |
|-----------|------|---------|-----|-------------|
| limit | integer | 50 | 100 | Number of FAQs per page |
| page | integer | 1 | - | Page number for pagination |

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/faqs?limit=20&page=1" \
  -H "Accept: application/json" \
  -H "Accept-Language: en"
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "question": "How do I book an appointment?",
      "answer": "You can book an appointment through our mobile app or website...",
      "translations": {
        "en": {
          "question": "How do I book an appointment?",
          "answer": "You can book an appointment through our mobile app or website..."
        },
        "ar": {
          "question": "كيف أحجز موعداً؟",
          "answer": "يمكنك حجز موعد من خلال تطبيقنا الجوال..."
        }
      },
      "locale": "en",
      "is_active": true,
      "display_order": 1,
      "created_at": "2025-11-18T10:00:00.000000Z",
      "updated_at": "2025-11-18T10:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/faqs?page=1",
    "last": "http://localhost:8000/api/faqs?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/faqs?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "path": "http://localhost:8000/api/faqs",
    "per_page": 20,
    "to": 20,
    "total": 100,
    "locale": "en"
  }
}
```

---

### Get Single FAQ

```
GET /api/faqs/{id}
```

**Headers:**
```
Accept: application/json
Accept-Language: en
```

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/faqs/1" \
  -H "Accept: application/json" \
  -H "Accept-Language: en"
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "question": "How do I book an appointment?",
    "answer": "You can book an appointment through our mobile app or website...",
    "translations": {
      "en": {
        "question": "How do I book an appointment?",
        "answer": "You can book an appointment through our mobile app..."
      },
      "ar": {
        "question": "كيف أحجز موعداً؟",
        "answer": "يمكنك حجز موعد من خلال تطبيقنا الجوال..."
      }
    },
    "locale": "en",
    "is_active": true,
    "display_order": 1,
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T10:00:00.000000Z"
  },
  "meta": {
    "locale": "en"
  }
}
```

**Error Response (404):**
```json
{
  "message": "Not Found",
  "status": 404
}
```

---

## Contact Us

### Submit Support Ticket / Contact Form

```
POST /api/contact
```

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "01012345678",
  "subject": "Issue with booking",
  "message": "I'm having trouble with my recent booking...",
  "priority": "high",
  "source": "mobile_app"
}
```

**Field Validation:**
| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| name | string | required, max:120 | Contact person name |
| email | string | required, email, max:255 | Valid email address |
| phone | string | nullable, max:50 | Contact phone number |
| subject | string | required, max:255 | Ticket subject |
| message | string | required | Detailed message/description |
| priority | string | nullable, in:low,medium,high | Ticket priority (default: medium) |
| source | string | nullable, max:50 | Source of contact (mobile_app, website, etc.) |

**Request Example:**
```bash
curl -X POST "http://localhost:8000/api/contact" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "01012345678",
    "subject": "Issue with booking",
    "message": "I am having trouble with my recent booking",
    "priority": "high",
    "source": "mobile_app"
  }'
```

**Response (201 Created):**
```json
{
  "status": true,
  "message": "Support ticket created successfully",
  "data": {
    "id": 125,
    "subject": "Issue with booking",
    "priority": "high",
    "message": "I am having trouble with my recent booking",
    "contact_name": "John Doe",
    "contact_email": "john@example.com",
    "contact_phone": "01012345678",
    "source": "mobile_app",
    "status": "open",
    "created_at": "2025-11-18T10:00:00.000000Z"
  }
}
```

**Error Response (422 - Validation Failed):**
```json
{
  "message": "The given data was invalid",
  "errors": {
    "email": ["The email field must be a valid email address"],
    "subject": ["The subject field is required"]
  }
}
```

---

## Contact Info

### Get Contact Information & Social Media Links

```
GET /api/contact-info
```

**Headers:**
```
Accept: application/json
```

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/contact-info" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Success",
  "data": {
    "brand": {
      "name": "Cure Medical",
      "tagline": "Your Health is Our Priority",
      "logo": "http://localhost:8000/storage/logos/cure-logo.png"
    },
    "contact": {
      "phone": "+20 100 123 4567",
      "email": "support@cure.medical",
      "address": "123 Medical Street, Cairo, Egypt"
    },
    "socials": {
      "facebook": "https://facebook.com/cure.medical",
      "whatsapp": "https://wa.me/201001234567",
      "youtube": "https://youtube.com/@cure.medical",
      "linkedin": "https://linkedin.com/company/cure-medical"
    }
  }
}
```

---

## Payment Methods

### List All Payment Methods for Patient

```
GET /api/payment-methods
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/payment-methods" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Payment methods list fetched successfully",
  "data": [
    {
      "id": 1,
      "provider": "card",
      "brand": "VISA",
      "last4": "4242",
      "exp_month": 12,
      "exp_year": 2026,
      "gateway": "stripe",
      "token": "hash_token_xyz",
      "is_default": true,
      "metadata": {
        "cardholder_name": "John Doe",
        "masked_card": "****4242"
      },
      "created_at": "2025-11-18T10:00:00.000000Z",
      "updated_at": "2025-11-18T10:00:00.000000Z"
    },
    {
      "id": 2,
      "provider": "card",
      "brand": "MASTERCARD",
      "last4": "5555",
      "exp_month": 6,
      "exp_year": 2027,
      "gateway": "stripe",
      "token": "hash_token_abc",
      "is_default": false,
      "metadata": {
        "cardholder_name": "Jane Doe",
        "masked_card": "****5555"
      },
      "created_at": "2025-11-17T15:30:00.000000Z",
      "updated_at": "2025-11-17T15:30:00.000000Z"
    }
  ]
}
```

---

### Add Payment Method

```
POST /api/payment-methods
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "cardholder_name": "John Doe",
  "card_number": "4242424242424242",
  "brand": "VISA",
  "exp_month": 12,
  "exp_year": 2026,
  "cvv": "123",
  "gateway": "stripe",
  "is_default": true
}
```

**Field Validation:**
| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| cardholder_name | string | required, max:120 | Name on card |
| card_number | string | required, 12-19 digits | Card number (without spaces) |
| brand | string | nullable, max:60 | Card brand (VISA, MASTERCARD, AMEX) |
| exp_month | integer | required, 1-12 | Expiration month |
| exp_year | integer | required | Expiration year (current year to +20 years) |
| cvv | string | required, 3-4 digits | Card security code |
| gateway | string | nullable, max:60 | Payment gateway (stripe, paypal) |
| is_default | boolean | nullable | Set as default payment method |

**Request Example:**
```bash
curl -X POST "http://localhost:8000/api/payment-methods" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "cardholder_name": "John Doe",
    "card_number": "4242424242424242",
    "brand": "VISA",
    "exp_month": 12,
    "exp_year": 2026,
    "cvv": "123",
    "gateway": "stripe",
    "is_default": true
  }'
```

**Response (201 Created):**
```json
{
  "status": true,
  "message": "Payment method created successfully",
  "data": {
    "id": 3,
    "provider": "card",
    "brand": "VISA",
    "last4": "4242",
    "exp_month": 12,
    "exp_year": 2026,
    "gateway": "stripe",
    "token": "hash_token_new",
    "is_default": true,
    "metadata": {
      "cardholder_name": "John Doe",
      "masked_card": "****4242"
    },
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T10:00:00.000000Z"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid",
  "errors": {
    "card_number": ["The card number must be between 12 and 19 digits"],
    "exp_year": ["The exp year must be between 2025 and 2045"]
  }
}
```

---

### Set Payment Method as Default

```
PUT /api/payment-methods/{id}/set-default
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Example:**
```bash
curl -X PUT "http://localhost:8000/api/payment-methods/1/set-default" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Payment method set as default successfully",
  "data": {
    "id": 1,
    "provider": "card",
    "brand": "VISA",
    "last4": "4242",
    "exp_month": 12,
    "exp_year": 2026,
    "gateway": "stripe",
    "token": "hash_token_xyz",
    "is_default": true,
    "metadata": {
      "cardholder_name": "John Doe",
      "masked_card": "****4242"
    },
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T10:00:00.000000Z"
  }
}
```

---

### Delete Payment Method

```
DELETE /api/payment-methods/{id}
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

**Request Example:**
```bash
curl -X DELETE "http://localhost:8000/api/payment-methods/1" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Payment method deleted successfully"
}
```

**Error Response (403):**
```json
{
  "message": "Unauthorized - Payment method belongs to another user"
}
```

---

## Patient Bookings

### Book an Appointment

```
POST /api/bookings
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "doctor_id": 1,
  "date_time": "2025-12-01T14:00:00",
  "payment_method": "stripe",
  "return_url": "https://app.example.com/payment/return",
  "cancel_url": "https://app.example.com/payment/cancel"
}
```

**Field Validation:**
| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| doctor_id | integer | required, exists:doctors,id | Doctor ID to book with |
| date_time | datetime | required, after:now | Appointment date and time |
| payment_method | string | required, in:cash,stripe,paypal | Payment method |
| return_url | string | nullable, url, required if payment_method=paypal | Return URL after payment |
| cancel_url | string | nullable, url, required if payment_method=paypal | Cancel URL if payment fails |

**Request Example:**
```bash
curl -X POST "http://localhost:8000/api/bookings" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "doctor_id": 1,
    "date_time": "2025-12-01T14:00:00",
    "payment_method": "stripe",
    "return_url": "https://app.example.com/payment/return",
    "cancel_url": "https://app.example.com/payment/cancel"
  }'
```

**Response (201 Created):**
```json
{
  "status": true,
  "message": "Appointment booked successfully",
  "data": {
    "booking": {
      "id": 50,
      "date_time": "2025-12-01 14:00:00",
      "date_time_formatted": "01 Dec 2025 02:00 PM",
      "status": "pending",
      "status_label": "Pending Confirmation",
      "payment_method": "stripe",
      "price": 150.00,
      "doctor": {
        "id": 1,
        "specialty": "Cardiology",
        "license_number": "LIC123456",
        "clinic_address": "123 Medical St, Cairo",
        "location": {
          "lat": 30.0444,
          "lng": 31.2357
        },
        "session_price": 150.00,
        "average_rating": 4.8,
        "reviews_count": 120,
        "user": {
          "id": 2,
          "name": "Dr. Ahmed Mohamed",
          "email": "ahmed@example.com",
          "mobile": "+201001234567",
          "profile_photo": "https://..."
        }
      },
      "patient": {
        "id": 1,
        "user": {
          "id": 50,
          "name": "John Doe",
          "email": "john@example.com",
          "mobile": "+201234567890"
        }
      },
      "can_cancel": true,
      "can_reschedule": true,
      "created_at": "2025-11-18T10:00:00.000000Z",
      "updated_at": "2025-11-18T10:00:00.000000Z"
    },
    "payment": {
      "provider": "stripe",
      "payment_id": "pi_1234567890",
      "client_secret": "pi_1234567890_secret_xyz",
      "approve_url": "https://checkout.stripe.com/pay/...",
      "status": "pending"
    }
  }
}
```

**For Cash Payment (No Payment Gateway):**
```json
{
  "status": true,
  "message": "Appointment booked successfully",
  "data": {
    "booking": {
      "id": 50,
      "date_time": "2025-12-01 14:00:00",
      "status": "pending",
      "payment_method": "cash",
      "price": 150.00
    }
  }
}
```

---

### Get Patient's Bookings

```
GET /api/my-bookings
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status (pending, confirmed, cancelled) |
| upcoming_only | boolean | Only show future appointments |
| date | date | Filter by specific date (YYYY-MM-DD) |
| page | integer | Page number for pagination |
| limit | integer | Items per page |

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/my-bookings?status=confirmed&upcoming_only=true&page=1" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Bookings fetched successfully",
  "data": [
    {
      "id": 50,
      "date_time": "2025-12-01 14:00:00",
      "date_time_formatted": "01 Dec 2025 02:00 PM",
      "status": "confirmed",
      "status_label": "Confirmed",
      "payment_method": "stripe",
      "price": 150.00,
      "doctor": {
        "id": 1,
        "specialty": "Cardiology",
        "session_price": 150.00,
        "average_rating": 4.8,
        "user": {
          "id": 2,
          "name": "Dr. Ahmed Mohamed",
          "profile_photo": "https://..."
        }
      },
      "can_cancel": true,
      "can_reschedule": true,
      "created_at": "2025-11-18T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 5,
    "last_page": 1
  }
}
```

---

### Get Booking Details

```
GET /api/bookings/{id}
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/bookings/50" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Booking details fetched",
  "data": {
    "id": 50,
    "date_time": "2025-12-01 14:00:00",
    "date_time_formatted": "01 Dec 2025 02:00 PM",
    "status": "confirmed",
    "status_label": "Confirmed",
    "payment_method": "stripe",
    "price": 150.00,
    "doctor": {
      "id": 1,
      "specialty": "Cardiology",
      "license_number": "LIC123456",
      "clinic_address": "123 Medical St, Cairo",
      "location": {
        "lat": 30.0444,
        "lng": 31.2357
      },
      "session_price": 150.00,
      "average_rating": 4.8,
      "reviews_count": 120,
      "user": {
        "id": 2,
        "name": "Dr. Ahmed Mohamed",
        "email": "ahmed@example.com",
        "mobile": "+201001234567",
        "profile_photo": "https://..."
      }
    },
    "patient": {
      "id": 1,
      "user": {
        "id": 50,
        "name": "John Doe",
        "email": "john@example.com",
        "mobile": "+201234567890"
      }
    },
    "payment": {
      "id": 30,
      "booking_id": 50,
      "amount": 150.00,
      "transaction_id": "txn_1234567890",
      "gateway": "stripe",
      "status": "success",
      "created_at": "2025-11-18T10:00:00.000000Z"
    },
    "can_cancel": true,
    "can_reschedule": true,
    "created_at": "2025-11-18T10:00:00.000000Z"
  }
}
```

**Error Response (404):**
```json
{
  "status": false,
  "message": "Booking not found"
}
```

**Error Response (403):**
```json
{
  "status": false,
  "message": "This booking does not belong to you"
}
```

---

### Reschedule Booking

```
PUT /api/bookings/{id}/reschedule
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "date_time": "2025-12-05T15:30:00"
}
```

**Field Validation:**
| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| date_time | datetime | required, after:now | New appointment date and time |

**Request Example:**
```bash
curl -X PUT "http://localhost:8000/api/bookings/50/reschedule" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "date_time": "2025-12-05T15:30:00"
  }'
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Booking rescheduled successfully",
  "data": {
    "id": 50,
    "date_time": "2025-12-05 15:30:00",
    "date_time_formatted": "05 Dec 2025 03:30 PM",
    "status": "rescheduled",
    "status_label": "Rescheduled",
    "payment_method": "stripe",
    "price": 150.00,
    "can_cancel": true,
    "can_reschedule": true,
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T11:30:00.000000Z"
  }
}
```

---

### Cancel Booking

```
DELETE /api/bookings/{id}
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

**Request Example:**
```bash
curl -X DELETE "http://localhost:8000/api/bookings/50" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Booking cancelled successfully",
  "data": {
    "id": 50,
    "date_time": "2025-12-01 14:00:00",
    "status": "cancelled",
    "status_label": "Cancelled",
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T11:45:00.000000Z"
  }
}
```

---

## Doctor Bookings

### Get All Doctor Bookings

```
GET /api/doctor/bookings
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status (pending, confirmed, cancelled) |
| upcoming_only | boolean | Only show future appointments |
| page | integer | Page number |
| limit | integer | Items per page |

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/doctor/bookings?status=pending&upcoming_only=true" \
  -H "Authorization: Bearer 1|doctor_token_123" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Bookings fetched successfully",
  "data": [
    {
      "id": 50,
      "date_time": "2025-12-01 14:00:00",
      "date_time_formatted": "01 Dec 2025 02:00 PM",
      "status": "pending",
      "status_label": "Pending Confirmation",
      "payment_method": "stripe",
      "price": 150.00,
      "patient": {
        "id": 1,
        "user": {
          "id": 50,
          "name": "John Doe",
          "email": "john@example.com",
          "mobile": "+201234567890",
          "profile_photo": "https://..."
        }
      },
      "can_cancel": true,
      "can_reschedule": true,
      "created_at": "2025-11-18T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 5
  }
}
```

---

### Get Doctor Booking Details

```
GET /api/doctor/bookings/{id}
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/doctor/bookings/50" \
  -H "Authorization: Bearer 1|doctor_token_123" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Booking details fetched",
  "data": {
    "id": 50,
    "date_time": "2025-12-01 14:00:00",
    "date_time_formatted": "01 Dec 2025 02:00 PM",
    "status": "pending",
    "status_label": "Pending Confirmation",
    "payment_method": "stripe",
    "price": 150.00,
    "patient": {
      "id": 1,
      "user": {
        "id": 50,
        "name": "John Doe",
        "email": "john@example.com",
        "mobile": "+201234567890",
        "profile_photo": "https://..."
      }
    },
    "can_cancel": true,
    "can_reschedule": true,
    "created_at": "2025-11-18T10:00:00.000000Z"
  }
}
```

---

### Confirm Booking

```
POST /api/doctor/bookings/{id}/confirm
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Example:**
```bash
curl -X POST "http://localhost:8000/api/doctor/bookings/50/confirm" \
  -H "Authorization: Bearer 1|doctor_token_123" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Booking confirmed successfully",
  "data": {
    "id": 50,
    "date_time": "2025-12-01 14:00:00",
    "date_time_formatted": "01 Dec 2025 02:00 PM",
    "status": "confirmed",
    "status_label": "Confirmed",
    "payment_method": "stripe",
    "price": 150.00,
    "patient": {
      "id": 1,
      "user": {
        "id": 50,
        "name": "John Doe"
      }
    },
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T11:00:00.000000Z"
  }
}
```

---

### Cancel Booking (Doctor)

```
POST /api/doctor/bookings/{id}/cancel
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Example:**
```bash
curl -X POST "http://localhost:8000/api/doctor/bookings/50/cancel" \
  -H "Authorization: Bearer 1|doctor_token_123" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Booking cancelled successfully",
  "data": {
    "id": 50,
    "date_time": "2025-12-01 14:00:00",
    "status": "cancelled",
    "status_label": "Cancelled",
    "price": 150.00,
    "updated_at": "2025-11-18T11:15:00.000000Z"
  }
}
```

---

### Reschedule Booking (Doctor)

```
POST /api/doctor/bookings/{id}/reschedule
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "date_time": "2025-12-08T10:00:00"
}
```

**Request Example:**
```bash
curl -X POST "http://localhost:8000/api/doctor/bookings/50/reschedule" \
  -H "Authorization: Bearer 1|doctor_token_123" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "date_time": "2025-12-08T10:00:00"
  }'
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Booking rescheduled successfully",
  "data": {
    "id": 50,
    "date_time": "2025-12-08 10:00:00",
    "date_time_formatted": "08 Dec 2025 10:00 AM",
    "status": "rescheduled",
    "status_label": "Rescheduled",
    "price": 150.00,
    "updated_at": "2025-11-18T11:30:00.000000Z"
  }
}
```

---

### Get Available Slots for Doctor

```
GET /api/doctors/{doctorId}/available-slots
```

**Headers:**
```
Accept: application/json
```

**Query Parameters:**
| Parameter | Type | Default | Max | Description |
|-----------|------|---------|-----|-------------|
| days | integer | 14 | 90 | Number of days to check ahead |

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/doctors/1/available-slots?days=30" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Available slots fetched successfully",
  "data": {
    "doctor": {
      "id": 1,
      "specialty": "Cardiology",
      "license_number": "LIC123456",
      "clinic_address": "123 Medical St",
      "location": {
        "lat": 30.0444,
        "lng": 31.2357
      },
      "session_price": 150.00,
      "average_rating": 4.8,
      "reviews_count": 120,
      "user": {
        "id": 2,
        "name": "Dr. Ahmed Mohamed",
        "email": "ahmed@example.com",
        "mobile": "+201001234567"
      }
    },
    "available_slots": {
      "2025-12-01": {
        "date": "2025-12-01",
        "day_name": "Monday",
        "slots": [
          {
            "datetime": "2025-12-01T09:00:00",
            "time": "09:00 AM"
          },
          {
            "datetime": "2025-12-01T10:00:00",
            "time": "10:00 AM"
          },
          {
            "datetime": "2025-12-01T14:00:00",
            "time": "02:00 PM"
          }
        ]
      },
      "2025-12-02": {
        "date": "2025-12-02",
        "day_name": "Tuesday",
        "slots": [
          {
            "datetime": "2025-12-02T09:00:00",
            "time": "09:00 AM"
          },
          {
            "datetime": "2025-12-02T11:00:00",
            "time": "11:00 AM"
          }
        ]
      }
    },
    "availability": {
      "saturday": "09:00-17:00",
      "sunday": "09:00-17:00",
      "monday": "09:00-17:00",
      "tuesday": "09:00-17:00"
    },
    "period": {
      "days": 30,
      "from": "2025-11-18",
      "to": "2025-12-18"
    }
  }
}
```

---

### Get All Doctors

```
GET /api/doctors
```

**Headers:**
```
Accept: application/json
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| specialty | string | Filter by specialty name |
| rating | float | Filter by minimum average rating |
| page | integer | Page number |
| limit | integer | Items per page |
| search | string | Search by name or specialty |

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/doctors?specialty=Cardiology&rating=4.0&page=1" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "specialty": "Cardiology",
      "license_number": "LIC123456",
      "clinic_address": "123 Medical St, Cairo",
      "location": {
        "lat": 30.0444,
        "lng": 31.2357
      },
      "session_price": 150.00,
      "average_rating": 4.8,
      "reviews_count": 120,
      "availability": {
        "saturday": "09:00-17:00",
        "sunday": "09:00-17:00",
        "monday": "09:00-17:00"
      },
      "consultation": "clinic",
      "user": {
        "id": 2,
        "name": "Dr. Ahmed Mohamed",
        "email": "ahmed@example.com",
        "mobile": "+201001234567",
        "profile_photo": "https://..."
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 45,
    "last_page": 3
  }
}
```

---

## Payment Processing

### Complete Payment Processing Flow

The payment system supports three payment methods: **Stripe**, **PayPal**, and **Cash**.

---

### Create Payment Intent

```
POST /api/payments/create-intent
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "booking_id": 50,
  "gateway": "stripe",
  "currency": "USD",
  "amount": "150.00",
  "description": "Booking #50 with Dr. Ahmed Mohamed",
  "return_url": "https://app.example.com/payment/return",
  "cancel_url": "https://app.example.com/payment/cancel"
}
```

**Field Validation:**
| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| booking_id | integer | required, exists:bookings,id | Associated booking ID |
| gateway | string | required, in:stripe,paypal,cash | Payment gateway |
| currency | string | required, size:3 | Currency code (USD, EGP, etc.) |
| amount | decimal | required, min:0.5 | Payment amount |
| description | string | nullable, max:255 | Payment description |
| return_url | string | url, required if gateway=paypal | Return URL for PayPal |
| cancel_url | string | url, required if gateway=paypal | Cancel URL for PayPal |

**Request Example - Stripe:**
```bash
curl -X POST "http://localhost:8000/api/payments/create-intent" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "booking_id": 50,
    "gateway": "stripe",
    "currency": "USD",
    "amount": "150.00",
    "description": "Booking #50 with Dr. Ahmed Mohamed"
  }'
```

**Response (201 Created) - Stripe:**
```json
{
  "status": true,
  "message": "Payment intent created successfully",
  "data": {
    "id": 30,
    "booking_id": 50,
    "amount": 150.00,
    "transaction_id": "pi_1234567890",
    "gateway": "stripe",
    "status": "pending",
    "approve_url": "https://checkout.stripe.com/pay/cs_test_xyz...",
    "client_secret": "pi_1234567890_secret_xyz",
    "payment_intent_id": "pi_1234567890",
    "created_at": "2025-11-18T10:00:00.000000Z"
  }
}
```

**Request Example - PayPal:**
```bash
curl -X POST "http://localhost:8000/api/payments/create-intent" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "booking_id": 50,
    "gateway": "paypal",
    "currency": "USD",
    "amount": "150.00",
    "description": "Booking #50 with Dr. Ahmed Mohamed",
    "return_url": "https://app.example.com/payment/return",
    "cancel_url": "https://app.example.com/payment/cancel"
  }'
```

**Response (201 Created) - PayPal:**
```json
{
  "status": true,
  "message": "Payment intent created successfully",
  "data": {
    "id": 31,
    "booking_id": 50,
    "amount": 150.00,
    "transaction_id": "PAYID-1234567890",
    "gateway": "paypal",
    "status": "pending",
    "approve_url": "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-xyz",
    "payment_intent_id": "PAYID-1234567890",
    "created_at": "2025-11-18T10:00:00.000000Z"
  }
}
```

**Request Example - Cash:**
```bash
curl -X POST "http://localhost:8000/api/payments/create-intent" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "booking_id": 50,
    "gateway": "cash",
    "currency": "USD",
    "amount": "150.00",
    "description": "Booking #50 with Dr. Ahmed Mohamed"
  }'
```

**Response (201 Created) - Cash:**
```json
{
  "status": true,
  "message": "Payment intent created successfully",
  "data": {
    "id": 32,
    "booking_id": 50,
    "amount": 150.00,
    "transaction_id": "cash_1234567890",
    "gateway": "cash",
    "status": "pending",
    "created_at": "2025-11-18T10:00:00.000000Z"
  }
}
```

---

### Confirm Payment

```
POST /api/payments/confirm
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "gateway": "stripe",
  "payment_id": "pi_1234567890"
}
```

**Field Validation:**
| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| gateway | string | required, in:stripe,paypal,cash | Payment gateway |
| payment_id | string | required | Payment/Transaction ID from gateway |

**Request Example - Stripe Confirmation:**
```bash
curl -X POST "http://localhost:8000/api/payments/confirm" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "gateway": "stripe",
    "payment_id": "pi_1234567890"
  }'
```

**Response (200 OK) - Stripe Success:**
```json
{
  "status": true,
  "message": "Payment confirmed successfully",
  "data": {
    "status": "success",
    "provider": "stripe",
    "payment_id": "pi_1234567890",
    "successful": true,
    "payment_updated": true
  }
}
```

**Request Example - PayPal Confirmation:**
```bash
curl -X POST "http://localhost:8000/api/payments/confirm" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "gateway": "paypal",
    "payment_id": "PAYID-1234567890"
  }'
```

**Response (200 OK) - PayPal Success:**
```json
{
  "status": true,
  "message": "Payment confirmed successfully",
  "data": {
    "status": "approved",
    "provider": "paypal",
    "payment_id": "PAYID-1234567890",
    "successful": true,
    "payment_updated": true
  }
}
```

**Request Example - Cash Payment:**
```bash
curl -X POST "http://localhost:8000/api/payments/confirm" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "gateway": "cash",
    "payment_id": "cash_1234567890"
  }'
```

**Response (200 OK) - Cash Success:**
```json
{
  "status": true,
  "message": "Payment confirmed successfully",
  "data": {
    "status": "success",
    "provider": "cash",
    "payment_id": "cash_1234567890",
    "successful": true,
    "payment_updated": true
  }
}
```

**Error Response - PayPal Failed:**
```json
{
  "status": true,
  "message": "Payment confirmed successfully",
  "data": {
    "status": "failed",
    "provider": "paypal",
    "payment_id": "PAYID-xyz",
    "successful": false,
    "payment_updated": true,
    "error_message": "The buyer account is not eligible to receive digital goods"
  }
}
```

---

### Get Payment Details

```
GET /api/payments/{id}
```

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/payments/30" \
  -H "Authorization: Bearer 1|xyz123abc456" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Payment fetched successfully",
  "data": {
    "id": 30,
    "booking_id": 50,
    "amount": 150.00,
    "transaction_id": "pi_1234567890",
    "gateway": "stripe",
    "status": "success",
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T10:05:00.000000Z"
  }
}
```

---

## Complete Payment Scenarios

### Scenario 1: Stripe Payment Flow

**Step 1: Patient Books Appointment**
```
POST /api/bookings
Body: {
  "doctor_id": 1,
  "date_time": "2025-12-01T14:00:00",
  "payment_method": "stripe"
}
Response: {
  "booking": { "id": 50, "status": "pending", "price": 150 },
  "payment": {
    "payment_id": "pi_1234567890",
    "client_secret": "pi_...",
    "approve_url": "https://checkout.stripe.com/pay/..."
  }
}
```

**Step 2: Frontend/Mobile App Redirects to Stripe**
- Use `approve_url` to redirect user to Stripe Checkout
- Or use `client_secret` to create payment element on app

**Step 3: Patient Completes Payment on Stripe**
- Stripe handles payment processing
- Returns confirmation with payment intent ID

**Step 4: Confirm Payment**
```
POST /api/payments/confirm
Body: {
  "gateway": "stripe",
  "payment_id": "pi_1234567890"
}
Response: {
  "status": "success",
  "successful": true
}
```

---

### Scenario 2: PayPal Payment Flow

**Step 1: Patient Books Appointment**
```
POST /api/bookings
Body: {
  "doctor_id": 1,
  "date_time": "2025-12-01T14:00:00",
  "payment_method": "paypal",
  "return_url": "https://app.example.com/payment/return",
  "cancel_url": "https://app.example.com/payment/cancel"
}
Response: {
  "booking": { "id": 50 },
  "payment": {
    "approve_url": "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-xyz"
  }
}
```

**Step 2: Redirect to PayPal**
- Redirect user to `approve_url`
- PayPal handles authentication and payment

**Step 3: PayPal Redirects Back**
- User approves → redirected to `return_url`
- PayPal includes PayerID in URL

**Step 4: Confirm Payment**
```
POST /api/payments/confirm
Body: {
  "gateway": "paypal",
  "payment_id": "PAYID-xyz"
}
Response: {
  "status": "approved",
  "successful": true
}
```

---

### Scenario 3: Cash Payment Flow

**Step 1: Patient Books Appointment**
```
POST /api/bookings
Body: {
  "doctor_id": 1,
  "date_time": "2025-12-01T14:00:00",
  "payment_method": "cash"
}
Response: {
  "booking": { 
    "id": 50, 
    "status": "pending",
    "price": 150
  }
}
```

**Note:** No payment object returned for cash. Doctor confirms booking when patient pays.

**Step 2: Patient Pays Doctor in Cash**
- Doctor receives payment in cash

**Step 3: Confirm Payment (Optional)**
```
POST /api/payments/confirm
Body: {
  "gateway": "cash",
  "payment_id": "cash_1234567890"
}
Response: {
  "status": "success",
  "successful": true
}
```

---

## Error Handling

### Common Error Responses

**401 - Unauthorized (Missing Authentication Token)**
```json
{
  "message": "Unauthenticated"
}
```

**403 - Forbidden (Insufficient Permissions)**
```json
{
  "status": false,
  "message": "This booking does not belong to you"
}
```

**404 - Not Found**
```json
{
  "status": false,
  "message": "Booking not found"
}
```

**422 - Validation Error**
```json
{
  "message": "The given data was invalid",
  "errors": {
    "doctor_id": ["The doctor_id field is required"],
    "date_time": ["The date_time must be a date after now"]
  }
}
```

**500 - Internal Server Error**
```json
{
  "status": false,
  "message": "Internal server error",
  "error": "Error details (only in debug mode)"
}
```

---

## Rate Limiting

- **Booking Creation**: 10 requests per minute per user
- **Payment Operations**: 5 requests per minute per user
- **General API**: 60 requests per minute per user

---

## Pagination

All list endpoints support pagination:

**Query Parameters:**
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 15, max: 100)

**Response Meta:**
```json
{
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 150,
    "last_page": 10
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

---

## Localization

Add `Accept-Language` header to get responses in different languages:
- `en` - English
- `ar` - Arabic

Example:
```bash
curl -X GET "http://localhost:8000/api/faqs" \
  -H "Accept-Language: ar"
```

---

## Version History

- **v1.0** (2025-11-18)
  - Initial API documentation
  - Google OAuth 2.0 implementation
  - Patient and Doctor booking systems
  - Payment processing (Stripe, PayPal, Cash)
  - Payment methods management
  - FAQs, Contact, and Contact Info endpoints

