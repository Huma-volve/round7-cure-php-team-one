# 🩺 Doctor Appointment System - Backend API

**Backend:** Laravel 12 (PHP 8.2+)  
**Frontend:** Mobile App (Flutter / React Native)  
**Database:** MySQL / PostgreSQL

A comprehensive REST API backend for a doctor appointment booking system that connects patients with doctors through a mobile application, including a web dashboard for admins.

## 📖 Overview

The Doctor Appointment System is a full-stack healthcare platform that enables patients to search for doctors, book appointments, make payments, and communicate with healthcare providers in real-time. The system includes role-based access control (RBAC) using Spatie Laravel Permission for managing user roles and permissions.

## 🚀 Features

### 👩‍⚕️ For Patients
- User registration and authentication (Email / Mobile / Google / OTP)
- Search for doctors by specialty or location (Google Maps API)
- Manage favorite doctors (Add/Remove)
- Book appointments with multiple payment methods (PayPal / Stripe / Cash)
- View upcoming and past bookings
- Write reviews after sessions
- Real-time chat with doctors (Laravel WebSocket)
- Account settings (Edit profile / Change password / Delete account)
- Receive notifications (FCM / APNS)
- **Multi-language support** (Arabic / English) for all API responses

### 🧑‍⚕️ For Doctors
- Account creation and activation via Admin
- Secure login (Email / Mobile / Google / OTP)
- Manage appointment availability
- View and manage bookings (Accept / Cancel / Reschedule)
- View patient reviews and respond
- Manage profile (Name, Specialty, Clinic, Price, Consultation Type)
- Revenue and booking reports
- Real-time chat with patients
- Receive notifications for new bookings and reviews
- **Multi-language support** (Arabic / English) for all API responses

### 🧑‍💻 For Admins (Web Dashboard)
- Secure login with 2FA (OTP)
- Manage users and doctors (Create, Edit, Delete, Suspend)
- Manage bookings, payments, and disputes
- System monitoring (Logs / Reports)
- Manage FAQs and Policies content
- Real-time notifications and alerts
- Flexible permissions using Spatie Laravel Permission (Roles: Admin, Doctor, Patient)
- **Server maintenance endpoints** with API key protection
- **Multi-language support** (Arabic / English) for all API responses

---

## ⚙️ Tech Stack

| Layer | Technology |
|-------|-------------|
| **Backend** | PHP 8.2+, Laravel 12 |
| **Database** | MySQL / PostgreSQL |
| **Frontend (Mobile)** | Flutter / React Native |
| **Authentication** | Laravel Sanctum (Stateless Tokens) |
| **Payments** | PayPal API, Stripe API |
| **Maps / Location** | Google Maps API |
| **Notifications** | Firebase Cloud Messaging (FCM), Apple Push Notification Service (APNS) |
| **Real-time Chat** | Laravel Echo + WebSockets |
| **Hosting / Scalability** | AWS / Laravel Vapor |
| **Security** | HTTPS, encryption, GDPR/HIPAA compliance |
| **Permission Management** | Spatie Laravel Permission |
| **Localization** | Laravel Localization (Arabic / English) |
| **API Security** | API Key Protection for Maintenance Endpoints |

---

## 🧩 System Modules

| Module | Description |
|--------|--------------|
| **Authentication** | User login and verification via OTP or Google |
| **Doctors Management** | Add and update doctor profiles and specialties |
| **Patients Management** | Manage patient profiles and booking history |
| **Bookings** | Create, cancel, and reschedule appointments |
| **Payments** | Process payments via PayPal or Stripe |
| **Reviews** | Write reviews after sessions |
| **Chat** | Real-time messaging between doctor and patient |
| **Notifications** | Notifications for bookings and updates |
| **Favorites** | Save favorite doctors |
| **System Logs** | Event logging and auditing |

---

## 🔒 Security & Compliance

- HTTPS + Stateless API authentication (Sanctum)
- User data and password encryption
- GDPR/HIPAA compliance for health data protection
- Role-based access control using **Spatie Laravel Permission**
- **API Key protection** for server maintenance endpoints (stored in database)
- Daily automated data backups
- SQL injection protection via Eloquent ORM
- XSS protection in API responses
- Rate limiting enabled

---

## 🧠 Architecture

- **Layered Architecture** (Controllers → Services → Models)
- **RESTful API** for mobile applications
- **RBAC** using Spatie Laravel Permission
- **Event Broadcasting** with WebSockets
- **Queue Jobs** for sending notifications and delayed tasks
- **API Guard**: `api` guard for stateless authentication

---

## 📊 Database Schema

### Tables

- **users**: User accounts with location and profile data
- **doctors**: Doctor profiles with specialty, license, and pricing
- **patients**: Patient medical information
- **bookings**: Appointment bookings
- **payments**: Payment transactions
- **reviews**: Patient reviews and ratings
- **chats**: Real-time messaging
- **notifications**: User notifications
- **favorites**: Saved favorite doctors
- **faqs**: Frequently asked questions
- **policies**: System policies
- **system_logs**: System event logging

---

## 🧾 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL or PostgreSQL
- Node.js and NPM (for assets)

### Setup Instructions

```bash
# Clone the repository
git clone https://github.com/your-org/round7-cure-php-team-one.git
cd round7-cure-php-team-one

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database_name
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations and seeders
php artisan migrate:fresh --seed

# Start the development server
php artisan serve
```

The application will be available at `http://localhost:8000`

---

## 🧑‍💻 Default Roles & Permissions

### Roles (Spatie Laravel Permission)

The system comes with three default roles using `api` guard:

#### Admin Role
**Permissions**: Full access
- `manage_bookings`
- `create_doctor`
- `view_reports`
- `handle_refunds`
- `chat_with_patient`

#### Doctor Role
**Permissions**: Limited
- `chat_with_patient`
- `manage_bookings`

#### Patient Role
**Permissions**: None (regular user)

### Default Admin Account

After running `php artisan migrate:fresh --seed`, you'll have:

```
Email: admin@example.com
Password: password123
Mobile: 0550000000
Role: admin
```

---

## 📱 API Endpoints

### 🌐 Localization

All API endpoints support **multi-language responses** (Arabic / English). Set the `Accept-Language` header:

```http
Accept-Language: ar  # Arabic
Accept-Language: en  # English (default)
```

**Supported translations:**
- Success/Error messages
- Validation error messages
- Status labels (pending, confirmed, cancelled, etc.)
- All API response messages

### Test Endpoints

#### 1. Test Role & Permissions (Unprotected)
```http
GET /api/test-role
Headers:
  Accept: application/json
  Accept-Language: ar|en
```

**Response:**
```json
{
    "user_id": 1,
    "roles": ["admin"],
    "has_admin": true
}
```

#### 2. Admin Dashboard (Protected)
```http
GET /api/admin/dashboard
Headers:
  Authorization: Bearer {token}
  Accept: application/json
  Accept-Language: ar|en
```

**Response:**
```json
{
    "ok": true,
    "area": "admin only"
}
```

### Server Maintenance Endpoints (Admin Only)

These endpoints require **Admin role** + **API Key** (except generate-api-key):

#### Generate API Key
```http
POST /api/admin/server/generate-api-key
Headers:
  Authorization: Bearer {admin-token}
  Accept: application/json
  Content-Type: application/json
```

**Response:**
```json
{
    "status": true,
    "message": "Maintenance API key generated and saved successfully",
    "api_key": "a2da26d4b11abab7a6a9a8439ee93ec095cde1167ab907e3751c9e88575f7702",
    "warning": "Save this key securely!",
    "usage": {
        "header": "X-API-Key",
        "value": "a2da26d4b11abab7a6a9a8439ee93ec095cde1167ab907e3751c9e88575f7702"
    }
}
```

#### Check API Key Status
```http
GET /api/admin/server/api-key-status
Headers:
  Authorization: Bearer {admin-token}
  Accept: application/json
```

#### Composer Update
```http
POST /api/admin/server/composer-update
Headers:
  Authorization: Bearer {admin-token}
  X-API-Key: {maintenance-api-key}
  Accept: application/json
  Content-Type: application/json
```

#### Other Maintenance Endpoints
- `POST /api/admin/server/composer-dumpautoload`
- `POST /api/admin/server/optimize-clear`
- `POST /api/admin/server/migrate-fresh-seed` (requires `confirm: true` in body)
- `POST /api/admin/server/run-all`

**Note:** The API key is stored in the database (`settings` table) and can be generated using the endpoint above. No need to add it to `.env` file.

### Main API Endpoints

```
# Authentication
POST   /api/register
POST   /api/login
POST   /api/logout
POST   /api/verifyEmailOtp
POST   /api/resend-verify-otp
POST   /api/forgot-password/send-otp
POST   /api/forgot-password/verify-otp
POST   /api/forgot-password/reset
POST   /api/google-login

# User Profile
GET    /api/user
PUT    /api/updateProfile
POST   /api/mobile/request-change
POST   /api/mobile/verify-change

# Doctors
GET    /api/doctors
GET    /api/doctors/{id}
GET    /api/home?latitude={lat}&longitude={lng}&search={term}

# Bookings
POST   /api/bookings
GET    /api/bookings
GET    /api/bookings/{id}
PUT    /api/bookings/{id}/reschedule
DELETE /api/bookings/{id}

# Payments
POST   /api/payments
GET    /api/payments
POST   /api/payments/{id}/confirm

# Reviews
GET    /api/reviews
POST   /api/reviews

# Favorites
POST   /api/favorites/toggle/{doctor}
GET    /api/favorites
GET    /api/favorites/check/{doctor}

# Notifications
GET    /api/notifications
GET    /api/doctor/notifications
POST   /api/doctor/notifications/{id}/read

# Specialties
GET    /api/specialties

# Search
POST   /api/store-search-history
```

**For detailed API documentation, see:**
- `api.md` - Main API documentation
- `BOOKING_API_DOCUMENTATION.md` - Booking endpoints
- `PAYMENTS_API_DOCUMENTATION.md` - Payment endpoints
- `Cure_API.postman_collection.json` - Postman collection

---

## 🔧 Development Commands

```bash
# Run migrations
php artisan migrate

# Fresh migration with seeders
php artisan migrate:fresh --seed

# Run seeders only
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Code formatting
./vendor/bin/pint

# Run tests
php artisan test

# Generate maintenance API key (via API)
# POST /api/admin/server/generate-api-key
```

### Server Maintenance (via API)

After generating the API key, you can use these endpoints:

```bash
# Update composer dependencies
POST /api/admin/server/composer-update
Headers: X-API-Key: {your-api-key}

# Regenerate autoload files
POST /api/admin/server/composer-dumpautoload
Headers: X-API-Key: {your-api-key}

# Clear all caches
POST /api/admin/server/optimize-clear
Headers: X-API-Key: {your-api-key}

# Fresh migration with seeding (⚠️ WARNING: Drops all tables)
POST /api/admin/server/migrate-fresh-seed
Headers: X-API-Key: {your-api-key}
Body: { "confirm": true }

# Run all maintenance commands
POST /api/admin/server/run-all
Headers: X-API-Key: {your-api-key}
Body: { "confirm": false }
```

---

## 🧩 Testing

### With Postman

1. **Import Collection**:
   - Import `Cure_API.postman_collection.json` into Postman

2. **Set Environment Variables**:
   - `base_url` = `http://localhost:8000`
   - `access_token` = (your authentication token)
   - `locale` = `ar` or `en` (for localization)
   - `maintenance_api_key` = (generated API key for maintenance endpoints)

3. **Test Authentication**:
   ```
   POST {{base_url}}/api/login
   Body: {
     "email": "admin@example.com",
     "password": "password123"
   }
   ```
   Copy the token from response and set it in `access_token` variable.

4. **Test Localization**:
   ```
   GET {{base_url}}/api/test-role
   Headers:
   - Accept-Language: {{locale}}
   ```

5. **Generate Maintenance API Key** (Admin only):
   ```
   POST {{base_url}}/api/admin/server/generate-api-key
   Headers:
   - Authorization: Bearer {{access_token}}
   ```
   Copy the `api_key` from response and set it in `maintenance_api_key` variable.

6. **Test Maintenance Endpoints**:
   ```
   POST {{base_url}}/api/admin/server/composer-update
   Headers:
   - Authorization: Bearer {{access_token}}
   - X-API-Key: {{maintenance_api_key}}
   ```

---

## 🧩 Code Examples

### Check User Role (Laravel Controller)

```php
use App\Models\User;

$user = auth()->user();

// Check if user has role
if ($user->hasRole('admin')) {
    // Admin logic
}

// Check multiple roles
if ($user->hasAnyRole(['admin', 'doctor'])) {
    // Either admin or doctor
}
```

### Middleware Usage

```php
// Protect route with specific role
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin-only', function () {
        return response()->json(['message' => 'Admin only']);
    });
});
```

---

## 🧩 Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| Payment failure | Laravel Retry Queues |
| Data breach | Encryption + Access Control |
| Scalability issues | Caching + Load Balancing |

---

## 🧾 Performance & Scalability

- API response time under 2 seconds
- Support for 100+ concurrent users initially
- Redis caching
- Scalable on **Laravel Vapor** or **AWS EC2**

---

## 📂 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/         # API Controllers
│   ├── Middleware/      # Custom Middleware
│   │   ├── SetLocaleMiddleware.php
│   │   └── VerifyApiKey.php
│   ├── Requests/        # Form Requests (Validation)
│   ├── Resources/       # API Resources
│   └── Traits/          # Reusable Traits
│       └── ApiResponseTrait.php
├── Models/              # Eloquent Models
├── Services/            # Business Logic Services
└── Constants/           # Constants (BookingStatus, etc.)

config/
├── auth.php             # Authentication guards
├── permission.php       # Spatie Permission config
└── app.php              # Application config

database/
├── migrations/          # Database migrations
└── seeders/             # Database seeders

lang/
├── ar/                  # Arabic translations
│   ├── messages.php
│   └── validation.php
└── en/                  # English translations
    ├── messages.php
    └── validation.php

routes/
├── api.php              # Main API Routes
├── api/
│   ├── patient.php      # Patient endpoints
│   ├── doctor.php       # Doctor endpoints
│   ├── admin.php        # Admin endpoints
│   └── shared.php       # Shared endpoints
└── web.php              # Web routes

storage/
└── logs/                # Application logs
```

---

## 🧾 License

This project is licensed under the **MIT License**.

---

## 🌍 Localization

The API supports **Arabic** and **English** languages for all responses:

- **Messages**: Success/Error messages are translated
- **Validation**: Validation error messages are translated
- **Status Labels**: Booking status labels (pending, confirmed, etc.) are translated
- **Default Language**: English (if no language is specified)

### Usage

Set the `Accept-Language` header in your requests:

```http
Accept-Language: ar  # Arabic
Accept-Language: en  # English (default)
```

### Translation Files

- `lang/ar/messages.php` - Arabic messages
- `lang/ar/validation.php` - Arabic validation messages
- `lang/en/messages.php` - English messages
- `lang/en/validation.php` - English validation messages

---

**Last Updated**: November 7, 2025  
**Version**: 1.1.0
