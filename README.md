# KareOns Field Force ERP

KareOns is a modern, enterprise-grade Field Force Management System designed specifically for the pharmaceutical and healthcare sectors. It provides comprehensive tools to track, manage, and analyze the daily operations of Medical Representatives (MRs) while giving management a real-time, birds-eye view of field activities.

## 🚀 Key Features

### 1. Robust Authentication & Access Control
- Secure login and user session management.
- **Role-Based Access Control (RBAC):** Strict separation of duties between Administrators and Medical Representatives (MRs).
- Admins have full access to organization settings, master data, and analytics, while MRs are restricted to their specific operational workflows.

### 2. Intelligent Admin Dashboard
- **Real-Time KPIs:** At-a-glance metrics for present/absent staff, total doctor visits, received orders, and distributed samples.
- **Top Performers:** Instant leaderboard showing the most active and successful representatives.
- **Live Activity Feed:** A continuously updating timeline of critical actions performed across the organization.
- **Dynamic Date Filtering:** View metrics for 'Today', 'Yesterday', 'Last 7 Days', or 'This Month'.

### 3. Medical Representative (MR) Management
- Complete lifecycle management of field staff.
- Store detailed profiles including contact information, employee codes, and identification photos.
- Ability to activate, deactivate, and track the status of individual representatives.

### 4. Product Master Catalog
- Centralized management of the organization's product catalog.
- Support for detailed product attributes including generic names, brand categories, strengths, pack sizes, pricing, and images.
- Instant toggle controls for product availability and stock status.

### 5. Advanced Attendance System
- **Geo-fenced Check-ins:** Field representatives must capture their GPS coordinates upon checking in and checking out.
- **Photo Verification:** Mandatory selfie capture during attendance check-in to ensure authenticity.
- **Strict Validations:** Automated prevention of duplicate check-ins or checking out without a prior check-in.

### 6. Doctor Visit Tracking
- Detailed logging of every interaction between an MR and a healthcare professional.
- Track discussion topics, promoted products, and feedback.
- Ensures representatives can only log visits while officially checked-in for the day.

### 7. Order Collection & Processing
- Streamlined order capture from doctors and pharmacies directly in the field.
- Track multi-item orders, quantities, and pricing.
- Automated status progression (Pending -> Reviewed -> Completed).

### 8. Sample Distribution Management
- Monitor the exact flow of promotional samples given to doctors.
- Inventory constraints ensure representatives cannot distribute more samples than they have been assigned.

### 9. Comprehensive Daily Reporting
- Automated generation of End-of-Day (EOD) reports summarizing an MR's entire day (hours worked, visits made, orders collected, samples distributed).
- Provides management with a unified view of daily productivity without manual data consolidation.

### 10. Advanced Analytics & Reports Module
- Deep-dive analytics into business performance.
- Search, filter, and export data across all major modules (Attendance, Visits, Orders, Samples).
- View historical trends and individual performance metrics.

### 11. Complete System Audit Logs
- **Activity Logging:** A background audit trail records every significant action taken by any user.
- Tracks 'Who', 'What', and 'When' for accountability, security, and compliance.
- Non-editable, system-generated logs ensure complete data integrity.

### 12. Organization Settings
- Global configuration for the ERP system.
- Manage company profile, branding, contact details, and core system preferences.

### 13. API-First Architecture
- Designed with a fully integrated REST API foundation.
- Completely ready for future expansion into dedicated mobile applications (iOS/Android) or integration with third-party software without needing backend rewrites.

### 14. Premium SaaS User Interface
- **State-of-the-art Design:** Features a beautiful, modern UI with glassmorphism effects, dynamic micro-animations, and fluid transitions.
- **Responsive Layouts:** Perfectly tailored layouts that look stunning on both desktop monitors and mobile devices.
- **Enhanced Usability:** Clean data tables with contextual badges, frosted glass navigation bars, and intuitive, distraction-free forms.

---

## 🔒 Security & Data Integrity
The software enforces strict business logic at the core level to prevent data corruption or invalid operations, such as:
- Preventing MRs from viewing sensitive global company data.
- Enforcing mandatory prerequisites (e.g., an MR cannot create a doctor visit if they haven't submitted their daily attendance).
- Protecting endpoints and routes against unauthorized cross-role access.

---

## 🧰 Tech Stack
- **Backend:** Laravel 11 (PHP 8.2+)
- **Auth:** Laravel Sanctum (API tokens) + session auth (web)
- **RBAC:** spatie/laravel-permission
- **Frontend:** Blade + Vite
- **Database:** MySQL/PostgreSQL in production (SQLite for local/tests)

---

## ⚙️ Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed        # creates schema + seed admin/MR
npm run dev                       # or `npm run build` for production assets
php artisan serve
```

Run the test suite:
```bash
php artisan test
```

---

## 🚀 Production Deployment

### 1. Environment (`.env`)
```dotenv
APP_ENV=production
APP_DEBUG=false                   # NEVER true in production
APP_URL=https://your-domain.com
APP_KEY=                          # php artisan key:generate

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=kareons
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=public            # or s3 for uploaded selfies/photos
LOG_LEVEL=error
```

### 2. Deploy steps
```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --force        # creates roles + default admin
php artisan storage:link          # exposes uploaded selfies/photos
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Runtime
- Point the web server (Nginx/Apache) document root at `public/`.
- Run the queue worker (activity logs, async jobs): `php artisan queue:work --daemon`.
- Serve over HTTPS; Sanctum bearer tokens must not travel over plain HTTP.

### 4. Default admin account
Seeding creates: **`admin@kareons.com` / `Kareons@2026`**.
> ⚠️ Change this password immediately after the first login in any real deployment.

### 5. Hardening already in place
- API returns a consistent JSON error envelope (no HTML error leakage); stack traces are hidden unless `APP_DEBUG=true`.
- Login is rate-limited (10/min/IP); authenticated API is rate-limited (120/min/user).
- Role middleware guards every Admin/MR route; ownership checks prevent cross-user access.

---

## 📡 API

A full REST API (`/api/v1`) powers mobile/third-party clients. See
[docs/API.md](docs/API.md) for the complete endpoint reference, request/response
shapes, auth flow, and rate limits.

Quick check that all routes resolve:
```bash
php artisan route:list --path=api
```
