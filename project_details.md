# KareOns Field Force ERP (MVP)

## Objective
The ERP should completely digitize the daily work of a Medical Representative (MR). 

Instead of using WhatsApp, Excel, phone calls, and paper reports, everything happens inside the ERP.

### Daily MR Cycle
```
MR Login
   │
Attendance (Face + GPS)
   │
Today's Dashboard
   │
Visit Doctor
   │
Enter Doctor Details
   │
Discuss Products
   │
Give Samples
   │
Take Order
   │
Submit Visit Report
   │
Repeat for other doctors
   │
Daily Summary
   │
Checkout (Face + GPS)
```
Everything is visible to the Admin.

---

## Roles
Only two roles:

### 1. Admin
Admin controls everything.
- Manage MRs
- Manage Products
- Assign Samples
- View Attendance
- View Orders
- View Visit Reports
- View Daily Reports
- View Sample Stock
- View Analytics

### 2. Medical Representative (MR)
MR has restricted access:
- Login
- Mark Attendance
- Visit Doctors
- Give Samples
- Take Orders
- Submit Reports
- View Assigned Samples
- View Previous Visits

---

## Modules

### MODULE 1 — Authentication
Simple login.
- Email
- Password

**Redirection after login:**
- Admin &rarr; Admin Dashboard
- MR &rarr; MR Dashboard

### MODULE 2 — Employee Management (Admin)
Admin can create MR accounts.
- **Fields:**
  - Employee Code
  - Name
  - Photo
  - Mobile
  - Email
  - Address
  - Joining Date
  - Password
  - Status

### MODULE 3 — Product Management
Admin creates products.
- **Fields:**
  - Product Name
  - Category
  - Strength
  - Pack Size
  - Description
  - MRP
  - Image
  - Status
- *Note:* No sales inventory tracking yet. Only product profiles (e.g., Pain Relief Oil, Calcium Tablet, Herbal Syrup, Diabetes Capsule).

### MODULE 4 — Sample Assignment
One of the most important modules. Admin decides how many samples each MR receives.
- **Example:**
  - MR Rahul receives:
    - Pain Relief Oil: 20 Samples
    - Calcium Tablet: 50 Samples
    - Diabetes Capsule: 30 Samples
- **ERP Tracking:**
  - MR
  - Product
  - Assigned Quantity
  - Remaining Quantity
  - Distributed Quantity
- **Rules:**
  - MR cannot distribute more than assigned.
  - This acts as a separate "Sample Inventory" and does not affect future sales inventory.

### MODULE 5 — Attendance (Most Important)
Attendance must be reliable.

**Check-In Flow:**
```
MR opens app
   ↓
Tap Check In
   ↓
Front Camera Opens
   ↓
Face Verified (or Selfie Captured)
   ↓
GPS Captured
   ↓
Time Captured
   ↓
Attendance Saved
```

**Data Captured:**
- Employee
- Date
- Check In Time
- Latitude
- Longitude
- Address
- Selfie
- Device

**Check-Out Flow:**
- Same process (Selfie, GPS, Time).
- System calculates and saves **Working Hours**.

### MODULE 6 — MR Dashboard
When the MR logs in, they see:
- Today's Attendance
- Assigned Samples
- Doctors Visited Today
- Orders Taken
- Samples Distributed
- Pending Reports
- Checkout Status

### MODULE 7 — Doctor Visit
No permanent Doctor CRM is required initially. Instead, every visit creates a doctor entry for that visit.

**Start Visit Flow:**
- MR taps **New Doctor Visit**.
- **Form Fields:**
  - Doctor Name *
  - Clinic/Hospital Name
  - Specialization
  - Phone (Optional)
  - Address
  - City
  - Area
  - GPS Location
  - Visit Date
  - Visit Time

> [!NOTE]
> Even though a full Doctor CRM isn't requested yet, we will save every doctor as a unique record in the database. If a doctor with the same phone number (or same name + clinic) already exists, reuse that record instead of creating a duplicate. This will prevent data duplication and make it easy to migrate to a full Doctor CRM later.

### MODULE 8 — Discussion Details
After entering doctor details, the MR fills:
- Purpose of Visit
- Discussion Summary
- Doctor Response
- Interested Products
- Competitor Medicines Mentioned
- Remarks

### MODULE 9 — Product Discussion
MR selects products discussed.
- **Checkboxes:** Pain Relief Oil, Herbal Syrup, Calcium Tablet, Diabetes Capsule
- **Optional fields:** Doctor Interest (Low / Medium / High)

### MODULE 10 — Sample Distribution
MR clicks **Give Samples**.
- ERP only shows assigned products with a remaining count > 0.
- MR enters the quantity to give.
- System updates the remaining quantity.
- If remaining is 0, the system blocks distribution.
- **Data visible to Admin:** Doctor, MR, Product, Quantity, Date.

### MODULE 11 — Order Collection
MR takes order records (simplified).
- **Fields:** Product, Quantity, Remarks.
- *Note:* No inventory deduction, stock update, invoice, or billing. This is purely an order record.
- **Statuses:** Pending, Confirmed, Completed, Cancelled. Admin can change statuses, which does not affect stock.

### MODULE 12 — Visit Report
When the MR finishes the visit, one report is saved containing:
- Doctor Details
- GPS & Time
- Products Discussed
- Samples Given
- Orders Taken
- Discussion Summary
- Remarks

*One visit = One complete report.*

### MODULE 13 — Daily Report
At the end of the day, the MR clicks **End Day**.
- The system automatically compiles: Attendance, Doctors Visited, Orders, Samples, and Working Hours.
- MR manually adds: Today's Summary, Problems Faced, Plan for Tomorrow.

### MODULE 14 — Admin Dashboard
Admin gets a high-level view of the organization:
- **Key Metrics:** MR Present Today, MR Checked Out, Doctors Visited Today, Orders Collected Today, Samples Distributed Today, Pending Orders, Total Active MRs.
- **Charts:** Daily visits, Orders by product, Sample distribution by product, Attendance trend.

### MODULE 15 — Attendance Monitoring
Admin can open any attendance record to view:
- MR Name, Check-In/Check-Out Time, Working Hours, Check-In/Out Selfies, GPS Location (Map View), Address, and Device info.

### MODULE 16 — Visit Monitoring
Admin can filter visits by Date, MR, Product, and Area.
- Shows Doctor Name, Clinic, Products Discussed, Samples Given, Orders Taken, Discussion Summary, Location, and Visit Time.

### MODULE 17 — Sample Monitoring
Dedicated admin page displaying a table of MR sample stocks:
- Columns: Product, Assigned, Distributed, Remaining.
- **Admin Actions:** Assign new samples, Increase quantities, Reduce quantities (if returned), View assignment history.

### MODULE 18 — Orders
Simple order management panel for Admins.
- **Fields:** Order Number, Date, MR Name, Doctor Name, Products, Quantity, Remarks, Status.
- **Statuses:** Pending, Reviewed, Completed, Cancelled.

---

## Suggested Database Tables
- `users` (stores credentials, roles)
- `roles`
- `employees` (extends users with MR-specific details)
- `products`
- `sample_assignments`
- `sample_distribution`
- `attendance`
- `doctor_visits`
- `doctor_visit_products`
- `orders`
- `order_items`
- `daily_reports`
- `activity_logs`

---

## Recommended Development Order

### Phase 1
- Authentication & Roles (Admin, MR)
- Employee Management
- Product Management

### Phase 2
- Sample Assignment
- MR Dashboard

### Phase 3
- Attendance (GPS + Face + Check-In/Out)
- Attendance Reports

### Phase 4
- Doctor Visit
- Discussion Report
- Product Discussion

### Phase 5
- Sample Distribution
- Order Collection

### Phase 6
- Daily Report
- Admin Dashboard
- Analytics & Filters
