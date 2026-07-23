# KareOns Field Force ERP — API Reference (v1)

Base URL: `https://<your-domain>/api/v1`

All endpoints return JSON. Authentication uses **Laravel Sanctum** bearer tokens.

---

## 1. Conventions

### Request headers
| Header | Value | Required |
|---|---|---|
| `Accept` | `application/json` | Always |
| `Content-Type` | `application/json` | For POST/PUT/PATCH with a JSON body |
| `Authorization` | `Bearer <token>` | All routes except `POST /login` |

> File uploads (attendance selfies) must be sent as `multipart/form-data`, not JSON.

### Standard response envelope
Every response — success or error — uses the same shape:

```jsonc
// Success
{
  "success": true,
  "message": "Human readable message",
  "data": { /* object, array, or paginated block */ }
}

// Error
{
  "success": false,
  "message": "Human readable message",
  "errors": { "field": ["validation message"] } // present only on 422
}
```

### Paginated `data` block
List endpoints return Laravel's pagination structure inside `data`:

```jsonc
{
  "success": true,
  "message": "...",
  "data": {
    "data": [ /* array of resources */ ],
    "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
    "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 74 }
  }
}
```
Common list query params: `?per_page=15`, plus per-endpoint filters documented below.

### HTTP status codes
| Code | Meaning |
|---|---|
| 200 | OK |
| 201 | Created |
| 401 | Unauthenticated (missing/invalid token, or bad login) |
| 403 | Authenticated but not allowed (wrong role / not your record) |
| 404 | Resource not found |
| 422 | Validation failed (see `errors`) |
| 429 | Too many requests (rate limit) |
| 500 | Server error (message is generic in production) |

### Rate limits
- `POST /login`: **10 requests / minute / IP**
- All authenticated routes: **120 requests / minute / user**

### Roles
Two roles gate access: `Admin` and `MR`. Admin-only routes 403 for MRs and vice-versa.

---

## 2. Authentication

### `POST /login`
No auth required.

```jsonc
// Request
{ "email": "mr@kareons.com", "password": "secret" }

// 200
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "1|xxxxxxxxxxxxxxxxxxxx",
    "role": "MR",
    "user": {
      "id": 5, "employee_code": "MR001", "name": "Rahul",
      "email": "mr@kareons.com", "mobile": "9876543210",
      "photo_url": "https://.../storage/photos/5.jpg",
      "status": true, "role": "MR"
    }
  }
}
```
Invalid credentials → `401 { "success": false, "message": "Invalid email or password." }`

### `POST /logout`
Revokes the current token. → `200`

### `GET /user`
Returns the authenticated user (UserResource).

### Profile
| Method | Path | Body |
|---|---|---|
| `GET` | `/profile` | — |
| `PUT` | `/profile` | `name`, `mobile`, `address`, … |
| `PUT` | `/profile/password` | `current_password`, `password`, `password_confirmation` |

### Notifications (shared by Admin & MR)
| Method | Path | Notes |
|---|---|---|
| `GET` | `/notifications` | Paginated history (`?per_page=20`) |
| `GET` | `/notifications/feed` | `{ unread_count, notifications: [latest 10] }` for a bell/dropdown |
| `POST` | `/notifications/read-all` | Marks all as read |
| `POST` | `/notifications/{id}/read` | Marks one as read; returns its `url` |

Each notification item: `{ id, type, icon, title, message, url, is_read, created_at }`.

---

## 3. MR (Medical Representative) endpoints
Prefix: `/mr` — requires role `MR`.

### 3.0 Dashboard
`GET /mr/dashboard` — today's home-screen summary for the logged-in MR:
```jsonc
{
  "attendance": { "checked_in": true, "checked_out": false, "check_in_time": "…",
                  "check_out_time": null, "is_late": false, "working_hours": "3h 20m" },
  "stats": { "visits_today": 4, "target_visits": 12, "orders_today": 2, "samples_given_today": 15 },
  "sample_stock": { "assigned": 200, "distributed": 60, "remaining": 140, "low_stock": 1 },
  "daily_report": { "submitted": false, "status": null }
}
```

### 3.1 Attendance
| Method | Path | Notes |
|---|---|---|
| `GET` | `/mr/attendance` | Paginated history |
| `GET` | `/mr/attendance/today` | Today's record (or null) |
| `POST` | `/mr/attendance/check-in` | `multipart/form-data` |
| `POST` | `/mr/attendance/check-out` | `multipart/form-data` |

**Check-in body (`multipart/form-data`):**
| Field | Type | Rule |
|---|---|---|
| `selfie` | file (image) | required, ≤ 5 MB |
| `lat` | number | −90..90 |
| `lng` | number | −180..180 |
| `accuracy` | number | meters |
| `address` | string | ≤ 1000 |
| `device_info` | object | optional |

Returns an `AttendanceResource` with `check_in`, `check_out`, `working_hours_formatted`.
Rules: cannot check in twice/day; cannot check out without checking in.

### 3.2 Doctor Visits
| Method | Path |
|---|---|
| `GET` | `/mr/visits` |
| `GET` | `/mr/visits/{visit}` |
| `POST` | `/mr/visits` |

**`POST /mr/visits`** — creates a full visit in one call (doctor + discussion + products + optional samples + optional order). Requires an active check-in for today.

```jsonc
{
  "lat": 19.07, "lng": 72.87, "accuracy": 12, "address": "Andheri, Mumbai",
  "doctor_name": "Dr. Smith", "clinic_name": "City Clinic",
  "specialization": "Cardiology", "phone": "9876543210",
  "area": "Andheri", "doctor_address": "…",
  "discussion_summary": "Discussed cardiac range",
  "doctor_response": "Positive",
  "competitor_medicines": "BrandX", "remarks": "Follow up next week",
  "products": [
    { "product_id": 1, "interest_level": "High", "remarks": "" }
  ],
  "samples": [ { "product_id": 1, "quantity": 5 } ],          // optional
  "orders":  [ { "product_id": 1, "quantity": 10 } ],          // optional
  "order_remarks": "Deliver next visit"                        // optional
}
```
`interest_level` ∈ `Very High | High | Medium | Low | Not Interested`.
Distributing more samples than remaining → `400`.

### 3.3 Product Discussions (attach to an existing visit)
| Method | Path |
|---|---|
| `POST` | `/mr/product-discussions` |
| `GET` | `/mr/product-discussions/{visit}` |

```jsonc
// POST body
{
  "doctor_visit_id": 12,
  "products": [ { "product_id": 1, "interest_level": "High", "remarks": "" } ]
}
```
Returns the visit's discussed products. Only the owning MR may modify the visit (else `400`).

### 3.4 Sample Distributions
| Method | Path |
|---|---|
| `POST` | `/mr/sample-distributions` |
| `GET` | `/mr/sample-distributions` |

```jsonc
// POST body
{ "doctor_visit_id": 12, "samples": [ { "product_id": 1, "quantity": 5 } ] }
```
Enforces remaining stock; insufficient stock → `400` with the reason. `GET` returns the MR's distributions (paginated).

### 3.5 Orders
| Method | Path |
|---|---|
| `POST` | `/mr/orders` |
| `GET` | `/mr/orders` |
| `GET` | `/mr/orders/{order}` |

```jsonc
// POST body — order is always tied to a visit
{
  "doctor_visit_id": 12,
  "remarks": "Bulk order",
  "items": [ { "product_id": 1, "quantity": 10 } ]
}
```
New orders start as `Pending`. Returns an `OrderResource`.

### 3.6 Daily Report
| Method | Path |
|---|---|
| `POST` | `/mr/daily-report` |
| `GET` | `/mr/daily-report/history` |

```jsonc
// POST body — compiles today's stats + saves manual sections
{
  "today_summary": "Visited 8 doctors …",   // required
  "problems_faced": "Traffic delays",         // optional
  "tomorrow_plan": "Cover Bandra zone"        // required
}
```
Requires today's attendance **check-out** to be complete (else `400`). One report per MR per day; resubmission → `400`.

### 3.7 Samples (read-only)
`GET /mr/samples` — the MR's assigned samples with `assigned`, `distributed`, `remaining`.

---

## 4. Admin endpoints
Requires role `Admin`.

### 4.1 Users (MRs)
Full REST resource: `GET|POST /users`, `GET|PUT|DELETE /users/{user}`,
plus `POST /users/{user}/toggle-status`
and `POST /users/{user}/reset-password` (`{ password, password_confirmation }` — admin reset, no current-password needed).

### 4.2 Products
Full REST resource: `/products`, plus `POST /products/{product}/toggle-status`.

### 4.3 Samples
| Method | Path | Notes |
|---|---|---|
| `GET` | `/samples` | All assignments |
| `GET` | `/samples/{sample}` | One assignment |
| `POST` | `/samples` | Assign/increase (array of `{product_id, quantity}`) |
| `POST` | `/samples/adjust` | Reduce/return/adjust |

### 4.4 Attendance & Visits (read-only monitoring)
| Method | Path |
|---|---|
| `GET` | `/attendance`, `/attendance/{attendance}` |
| `GET` | `/visits`, `/visits/{visit}` |

### 4.5 Orders
| Method | Path | Notes |
|---|---|---|
| `GET` | `/orders` | Filters: `status`, `user_id`, `search` (doctor/MR name), `sort` (`created_at\|status\|doctor_name\|id`), `order` (`asc\|desc`), `per_page` |
| `GET` | `/orders/{order}` | Full order with items + status history |
| `PATCH` | `/orders/{order}/status` | `{ "status": "Pending\|Reviewed\|Completed" }` — records status history |

### 4.6 Daily Reports
| Method | Path |
|---|---|
| `GET` | `/daily-reports` (filters: `date`, `user_id`) |
| `GET` | `/daily-reports/{dailyReport}` |
| `PATCH` | `/daily-reports/{dailyReport}/review` — marks a `Submitted` report as `Reviewed` |
| `GET` | `/daily-report/summary?date=YYYY-MM-DD` |

### 4.7 Dashboard
| Method | Path | Returns |
|---|---|---|
| `GET` | `/dashboard` | Counters: active MRs, today's visits/orders, pending orders |
| `GET` | `/dashboard/charts` | 7-day visits & orders series |
| `GET` | `/dashboard/recent-activities` | Latest 10 activity logs |

### 4.8 Reports (analytics export)
`GET /reports/{type}` where `type` ∈ `attendance | visits | orders | samples`.
Query: `start_date`, `end_date` (default: this month → today).

### 4.9 Settings
| Method | Path |
|---|---|
| `GET` | `/settings/company` |
| `PUT` | `/settings/company` |

### 4.10 Activity Logs
`GET /activity-logs` (filters: `module`, `user_id`), `GET /activity-logs/{activityLog}`.

---

## 5. Quick start (mobile)

```bash
# 1. Login
curl -X POST https://your-domain/api/v1/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"mr@kareons.com","password":"secret"}'

# 2. Use the token
curl https://your-domain/api/v1/mr/attendance/today \
  -H "Accept: application/json" -H "Authorization: Bearer 1|xxxx"
```
