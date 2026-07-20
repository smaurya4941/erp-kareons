KareOns Field Force ERP - Final Development Roadmap (Version 1.0)
Phase 0 — Project Setup (2–3 Days)
Goal

Prepare a clean, scalable foundation.

Tasks
Create Laravel 12 project
Configure MySQL
Install Tailwind CSS
Install Laravel Sanctum
Install Spatie Permission
Configure Storage
Configure Queue
Configure Mail
Configure Google Maps API
Create common layouts
Create sidebar
Create top navbar
Create reusable UI components
Create helper functions
Create global settings
Output

✅ Clean project ready for development

Phase 1 — Authentication & User Management
Goal

Secure login system.

Modules
Login
Email Login
Password Login
Remember Me
Logout
User Management

Admin can

Create MR
Edit MR
Delete MR
Activate/Deactivate MR
Reset Password

Fields

Employee Code

Name

Photo

Mobile

Email

Address

Joining Date

Status
Roles

Only

Admin

MR
Dashboard

Separate

Admin Dashboard

MR Dashboard

Output

✅ User Management Completed

Phase 2 — Product Management

Admin manages products.

Fields

Product Name

Category

Strength

Pack Size

Image

Description

Status

Features

Add
Edit
Delete
Search
Filter
Output

✅ Product Module Ready

Phase 3 — Sample Assignment

One of the most important modules.

Admin assigns samples.

Example

MR

Rahul

↓

Pain Relief Oil

20

↓

Calcium Tablet

50

Features

Assign Samples
Increase Samples
Reduce Samples
Assignment History
Remaining Samples

Tables

sample_assignments

sample_transactions
Output

✅ Sample Assignment Ready

Phase 4 — Attendance Module ⭐⭐⭐⭐⭐

Most critical module.

Check In

MR

↓

Open App/Web

↓

Take Selfie

↓

Capture GPS

↓

Check In

Store

Selfie

Latitude

Longitude

Address

Time

Device
Check Out

Same process.

Admin

Can view

Selfie
Map
Working Hours
Attendance History
Output

✅ Attendance Module Complete

Phase 5 — Doctor Visit Module ⭐⭐⭐⭐⭐

Main working module.

MR

↓

New Visit

↓

Doctor Details

↓

Discussion

↓

Products

↓

Samples

↓

Order

↓

Submit

Doctor Details

Doctor Name

Clinic

Specialization

Phone

Area

Address

GPS

Discussion

Summary

Remarks

Competitor Medicine

Doctor Response
Output

✅ Visit Module Ready

Phase 6 — Product Discussion

MR selects

Products Discussed

Example

Pain Relief Oil

Herbal Syrup

Calcium Tablet

Optional

Interest Level

Remarks
Output

✅ Product Discussion Ready

Phase 7 — Sample Distribution

MR gives samples.

System checks

Remaining Quantity

If

Remaining = 0

Cannot distribute.

Stores

Doctor

MR

Product

Quantity

Date

Admin sees complete history.

Output

✅ Sample Distribution Ready

Phase 8 — Order Collection

Very simple.

MR enters

Doctor

Products

Quantity

Remarks

No inventory deduction.

Only

Pending Order.

Admin changes status

Pending

↓

Reviewed

↓

Completed

Output

✅ Order Module Ready

Phase 9 — Daily Report

MR clicks

End Day

Automatically

Attendance

Doctors

Orders

Samples

MR enters

Today's Summary

Problems

Tomorrow Plan
Output

✅ Daily Report Ready

Phase 10 — Admin Dashboard

Cards

Today's Attendance

Today's Visits

Orders

Samples

Present MRs

Pending Orders

Charts

Attendance Trend

Daily Visits

Orders

Sample Distribution

Recent Activities

Live Counters

Output

✅ Dashboard Ready

Phase 11 — Reports

Reports

Attendance

Visit

Orders

Samples

MR Performance

Daily Report

All Reports should support

Search
Filter
Export CSV
Export Excel
Print
Output

✅ Reports Ready

Phase 12 — Settings

Settings

Company

Profile

Password

General Settings

Google Maps Key

System Settings

Output

✅ Settings Ready

Phase 13 — Activity Logs

Store every important action.

Example

Admin Assigned Samples

MR Created Order

MR Checked In

MR Submitted Report
Output

✅ Logs Ready

Phase 14 — API Development

Now create APIs.

Do NOT wait until Flutter starts.

Create API while building every module.

Example

/api/login

/api/products

/api/attendance

/api/doctor-visits

/api/orders

/api/sample-assignments

/api/profile
Output

✅ API Ready

Phase 15 — Testing

Test every workflow.

Examples:

Wrong GPS
Duplicate attendance
Multiple check-ins
Sample over-distribution
Missing doctor details
Large image uploads
Session timeout
Invalid permissions
Output

✅ Stable ERP

Final Module Flow
Authentication
│
▼
User Management
│
▼
Product Management
│
▼
Sample Assignment
│
▼
Attendance
│
▼
Doctor Visit
│
▼
Product Discussion
│
▼
Sample Distribution
│
▼
Order Collection
│
▼
Daily Report
│
▼
Dashboard
│
▼
Reports
│
▼
Settings
│
▼
Activity Logs
│
▼
REST API
│
▼
Flutter App
Database Growth Order
Users
│
Products
│
Sample Assignments
│
Attendance
│
Doctor Visits
│
Visit Products
│
Sample Distribution
│
Orders
│
Order Items
│
Daily Reports
│
Activity Logs
Estimated Timeline (Single Developer)
Phase Duration
Project Setup 2–3 days
Authentication & Users 3 days
Product Management 2 days
Sample Assignment 3 days
Attendance 5–6 days
Doctor Visit 6–7 days
Product Discussion 2 days
Sample Distribution 3 days
Order Collection 3 days
Daily Report 2 days
Dashboard 4–5 days
Reports 5 days
Settings & Logs 2–3 days
APIs Built alongside each phase
Testing & Bug Fixes 7–10 days

Estimated total: about 7–9 weeks for a polished MVP if you're working on it consistently as a single developer.
