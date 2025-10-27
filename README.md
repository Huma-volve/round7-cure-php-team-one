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

### 🧑‍⚕️ For Doctors
- Account creation and activation via Admin
- Secure login (Email / Mobile / Google / OTP)
- Manage appointment availability
- View and manage bookings (Accept / Cancel / Reschedule)
- View patient reviews and respond
- Manage profile (Name, Specialty, Clinic, Price)
- Revenue and booking reports
- Real-time chat with patients
- Receive notifications for new bookings and reviews

### 🧑‍💻 For Admins (Web Dashboard)
- Secure login with 2FA (OTP)
- Manage users and doctors (Create, Edit, Delete, Suspend)
- Manage bookings, payments, and disputes
- System monitoring (Logs / Reports)
- Manage FAQs and Policies content
- Real-time notifications and alerts
- Flexible permissions using Spatie Laravel Permission (Roles: Admin, Doctor, Patient)

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

### Test Endpoints

#### 1. Test Role & Permissions (Unprotected)
```http
GET /api/test-role
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
Authorization: Bearer {token}
```

**Response:**
```json
{
    "ok": true,
    "area": "admin only"
}
```

### Future Endpoints (To be implemented)

```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/user
GET    /api/doctors
GET    /api/doctors/{id}
POST   /api/bookings
GET    /api/bookings
GET    /api/bookings/{id}
POST   /api/payments
GET    /api/reviews
POST   /api/reviews
POST   /api/chat/send
GET    /api/notifications
GET    /api/faqs
GET    /api/policies
```

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

# Code formatting
./vendor/bin/pint

# Run tests
php artisan test
```

---

## 🧩 Testing

### With Postman

1. **Create Environment**:
   - Variable: `base_url` = `http://localhost:8000`
   - Variable: `token` = (auto-filled)

2. **Test Role Endpoint**:
   ```
   GET {{base_url}}/api/test-role
   ```

3. **Admin Dashboard**:
   ```
   GET {{base_url}}/api/admin/dashboard
   Headers:
   - Authorization: Bearer {{token}}
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
│   └── Controllers/     # API Controllers
├── Models/               # Eloquent Models
└── Providers/

config/
├── auth.php             # Authentication guards
├── permission.php       # Spatie Permission config

database/
├── migrations/          # Database migrations
└── seeders/             # Database seeders

routes/
└── api.php              # API Routes

storage/
└── logs/                # Application logs
```

---

## 🧾 License

This project is licensed under the **MIT License**.

---

**Last Updated**: October 26, 2025  
**Version**: 1.0.0
