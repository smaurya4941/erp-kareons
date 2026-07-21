Based on everything we've discussed over the past few days (your Laravel experience, Flutter learning, future mobile app, offline support, and the ERP requirements), I recommend the following final production-ready tech stack.

Overall Architecture
Internet
│
│
Laravel Backend (API)
│
┌───────────────┴───────────────┐
│ │
Admin Web Panel Flutter Mobile App
(Blade) (Offline First)
│ │
└───────────────┬───────────────┘
│
MySQL Database

One Backend
One Database
One API

Everything uses the same backend.

Backend
Framework

✅ Laravel 12

Why?

You already know Laravel well.
Excellent ecosystem.
Secure.
Queue support.
Scheduler.
Notifications.
Easy REST API development.
PHP

PHP 8.3+

Database

MySQL 8+

Why MySQL?

Stable
Fast
Easy backup
Laravel optimized
Easy hosting

No need for PostgreSQL for this project.

Authentication

Laravel Sanctum

Why?

Web + Mobile support
Simple
Secure
Official Laravel package
Permissions

Use spatie/laravel-permission

Roles

Admin

MR

Later you can add

Manager

Accounts

without changing the system.

API

REST API

Example

/api/login

/api/attendance

/api/orders

/api/doctor-visits

/api/products

/api/sample-assignments
Admin Website
Laravel Blade

No React.

No Vue.

Reason

Faster development
Easy maintenance
Less complexity
SEO not important
Internal software
CSS

Tailwind CSS

JavaScript

Alpine.js

For

Modals
Dropdowns
Tabs
Filters

No need for React.

Data Tables

Yajra DataTables

For

Attendance
Orders
Reports
Charts

ApexCharts

Dashboard becomes professional.

Maps

OpenStreetMap (Leaflet)

Show

Attendance
Doctor Visit
Location
Storage

Laravel Storage

Initially

storage/app/public

Later

AWS S3

No code changes needed.

Notifications

Laravel Notifications

Initially

Database Notification

Later

Firebase Push

Queue

Laravel Queue

Database Driver initially.

Later

Redis.

Caching

Initially

File Cache

Later

Redis

Logging

Laravel Log

Later

Sentry

Mobile

Flutter Latest Stable

State Management

Riverpod

Don't use Provider.

Riverpod is cleaner, more scalable, and easier to test.

Routing

go_router

HTTP

Dio

Local Storage

flutter_secure_storage

For

Token

User

Settings
Offline Database

Isar ⭐⭐⭐⭐⭐

This is my recommendation.

Don't use Hive.

Don't use SharedPreferences.

Don't use SQLite directly.

Isar is modern, fast, and works very well for offline-first apps.

Connectivity

connectivity_plus

GPS

geolocator

Camera

camera

Image Picker

image_picker

Permissions

permission_handler

Background Sync

workmanager

Automatically sync

Attendance

Orders

Reports

Visits

when internet comes back.

Face Attendance

Initially

Camera Selfie

GPS

Later

Add Face Verification.

Reason

Face Recognition requires more work and increases complexity.

Your MVP can capture a selfie with the attendance record. Later you can compare it against the employee's registered face if needed.

QR Code

Not required.

OCR

Not required.

AI

Not required.

File Upload

Laravel Media Library (Spatie)

Useful for

Selfies
Product Images
Reports
Development Tools

VS Code

Laravel Herd/XAMPP (local)

Postman

Git

GitHub

Composer

Node.js

API Testing

Postman

Deployment
Web

Hostinger VPS

or

DigitalOcean

Mobile

Android APK

Later

Play Store

Folder Structure
Laravel
app/

Models

Services

Repositories

Actions

Policies

Http

Controllers

Api

Web

Requests

Resources

Traits

Helpers
Feature Modules
Attendance

Authentication

Doctor Visits

Orders

Products

Reports

Sample Management

Dashboard

Users

Keep every feature separate.

Database Tables
users

roles

model_has_roles

products

sample_assignments

sample_distributions

doctor_visits

doctor_visit_products

orders

order_items

attendance

daily_reports

activity_logs
API Response Format

Always use

{
"success": true,
"message": "Attendance marked successfully.",
"data": {}
}

Never return raw models.

Git Branches
main

development

feature/\*
Coding Standard

Laravel Pint

PSR-12

Future Ready

Later you can add

Inventory
Doctor CRM
Distributor Portal
Sales
Billing
Accounts
Manager
HR
Payroll

without changing your architecture.

Final Tech Stack
Category Technology
Backend Laravel 12
PHP PHP 8.3+
Database MySQL 8
Authentication Laravel Sanctum
Roles & Permissions Spatie Laravel Permission
Frontend Blade
CSS Tailwind CSS
JS Alpine.js
Charts ApexCharts
Tables Yajra DataTables
Maps OpenStreetMap (Leaflet)
Storage Laravel Storage (S3-ready)
Queue Laravel Queue
Cache File Cache (Redis-ready)
Mobile Flutter
State Management Riverpod
HTTP Client Dio
Routing go_router
Offline Database Isar
Secure Storage flutter_secure_storage
GPS geolocator
Camera camera
Connectivity connectivity_plus
Background Sync workmanager
API Testing Postman
Version Control Git + GitHub
One architectural change I'd strongly recommend

Although you're starting by building the website first, do not write business logic directly inside your Blade controllers.

Instead, organize the backend like this:

Controller
↓
Form Request (Validation)
↓
Service Layer (Business Logic)
↓
Repository (Database)
↓
Models

Then expose the same business logic through REST APIs.

This means:

The Blade admin panel uses the same services internally.
The Flutter app later consumes the APIs without requiring duplicate logic.
When you add offline sync, you only build the API layer—not rewrite the backend.

This approach takes a little more discipline upfront, but it will save a lot of time once you start developing the Flutter application.
