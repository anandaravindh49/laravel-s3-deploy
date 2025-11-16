# VMS Quick Start Guide

## ⚡ 5-Minute Setup

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Seed Sample Data
```bash
php artisan db:seed --class=VisitorManagementSeeder
```

### Step 3: Start Queue Worker (New Terminal)
```bash
php artisan queue:work
```

### Step 4: Start Laravel Server
```bash
php artisan serve
```

### Step 5: Access Admin Dashboard
- **URL**: http://localhost:8000
- **Email**: admin@vms.local
- **Password**: password

---

## 🔑 Default Credentials

| Field | Value |
|-------|-------|
| Email | admin@vms.local |
| Password | password |

---

## 📱 API Testing with cURL

### 1. Submit Visitor Request (Public - No Auth Required)
```bash
curl -X POST http://localhost:8000/api/public/request \
  -H "Content-Type: application/json" \
  -d '{
    "visitor_name": "Test Visitor",
    "visitor_phone": "+91-1234567890",
    "purpose_of_visit": "Meeting",
    "visit_date": "2025-11-17",
    "host_department": "Sales",
    "host_person_name": "John"
  }'
```

### 2. List Devices (Requires Auth)
```bash
curl -X GET http://localhost:8000/api/devices \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Get Pending Requests
```bash
curl -X GET http://localhost:8000/api/requests \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Approve Request
```bash
curl -X POST http://localhost:8000/api/requests/1/approve \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{}'
```

### 5. Check-in Visitor
```bash
curl -X POST http://localhost:8000/api/gate/checkin \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "badge_number": "VMS-ABC123",
    "device_id": 1
  }'
```

---

## 🗂️ File Structure Summary

```
VMS System Components:
├── Migrations (7 tables)
├── Models (7 models with relationships)
├── Controllers (5 controllers with 20+ endpoints)
├── Services (5 services with business logic)
├── Events (4 events + 4 listeners)
├── Jobs (3 scheduled jobs)
├── Routes (20 API endpoints)
└── Documentation (2 guides)
```

---

## 🎯 Key Features Implemented

### Device Management
- ✅ Create, read, update, delete devices
- ✅ Real-time sync status checking
- ✅ Device logs tracking
- ✅ Automatic user list synchronization

### Public Request Handling
- ✅ Public form submission
- ✅ ID proof upload support
- ✅ Admin approval/rejection workflow
- ✅ Request audit trail

### Visitor Management
- ✅ Automatic badge generation
- ✅ Device user synchronization
- ✅ Access revocation
- ✅ Validity date tracking

### Gate Operations
- ✅ Check-in processing
- ✅ Check-out processing
- ✅ Visitor history tracking
- ✅ Active visitors on device

### Audit & Compliance
- ✅ Complete audit trail
- ✅ User action logging
- ✅ Change tracking
- ✅ Activity reports

---

## 📊 Database Tables

| Table | Rows | Purpose |
|-------|------|---------|
| devices | 3 | Physical gates/turnstiles |
| device_logs | N | Sync and operation logs |
| device_users | N | Users synced to devices |
| public_requests | N | Visitor request forms |
| approved_visitors | N | Approved visitor records |
| gate_logs | N | Check-in/check-out logs |
| audit_logs | N | Complete audit trail |

---

## ⚙️ Background Jobs

| Job | Schedule | Purpose |
|-----|----------|---------|
| ProcessDeviceLogs | Every minute | Pull device logs |
| SyncApprovedUsers | Every 5 min | Sync users to devices |
| AutoCheckout | Daily 00:00 | Auto-checkout expired visitors |
| AuditCleanup | Monthly | Delete old audit logs |

---

## 🚨 Troubleshooting

### Queue Not Processing
```bash
# Check if queue is running
php artisan queue:work

# Check queue:failed
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Scheduler Not Running
```bash
# Add to crontab:
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1

# Verify scheduler
php artisan schedule:list
```

### Database Errors
```bash
# Reset migrations
php artisan migrate:reset

# Fresh migration
php artisan migrate:fresh --seed
```

---

## 📚 Documentation Files

| File | Content |
|------|---------|
| VMS_API_DOCUMENTATION.md | Complete API reference with examples |
| VMS_COMPLETE_DOCUMENTATION.md | Architecture, models, services, best practices |
| VMS_QUICK_START_GUIDE.md | This file - quick setup and testing |

---

## 🔌 API Endpoints Reference

### Public
```
POST /api/public/request
```

### Devices
```
GET    /api/devices
POST   /api/devices
GET    /api/devices/{id}
PATCH  /api/devices/{id}
DELETE /api/devices/{id}
GET    /api/devices/{id}/logs
POST   /api/devices/{id}/sync
```

### Requests
```
GET /api/requests
GET /api/requests/{id}
```

### Approval
```
POST /api/requests/{id}/approve
POST /api/requests/{id}/reject
POST /api/requests/{id}/revoke
```

### Gate
```
POST /api/gate/checkin
POST /api/gate/checkout
GET  /api/gate/visitors/{id}/history
GET  /api/gate/devices/{id}/active-visitors
```

### Audit
```
GET /api/audit/logs
GET /api/audit/report
GET /api/audit/resource-history
```

---

## 💾 Sample Data in Seeder

After running `db:seed --class=VisitorManagementSeeder`:

**Devices:**
- GATE-MAIN-001 @ 192.168.1.100
- GATE-SIDE-001 @ 192.168.1.101
- TURNSTILE-LOBBY-001 @ 192.168.1.102

**Sample Requests:**
- Rajesh Kumar (pending approval)
- Priya Singh (approved, active)

**Sample Device User:**
- John Employee (EMP-001)

---

## 🔐 Security Checklist

- ✅ All admin endpoints require authentication
- ✅ Input validation on all endpoints
- ✅ Audit logging for all actions
- ✅ Database queries use Eloquent ORM (SQL injection safe)
- ✅ CORS and CSRF protection enabled
- ✅ Rate limiting can be added to public endpoints
- ✅ Sanctum tokens for API authentication

---

## 📞 Common Tasks

### Add New Device
```php
Device::create([
    'device_id' => 'GATE-NEW-001',
    'name' => 'New Gate',
    'location' => 'Location',
    'device_type' => 'gate',
    'ip_address' => '192.168.1.x',
]);
```

### Approve Visitor Request
```php
$approvalService->approveRequest($publicRequest, auth()->id());
```

### Check-in Visitor
```php
$gateService->checkIn($visitor, $device);
```

### Get Audit Trail
```php
$auditService->getLogs(['module' => 'request']);
```

### Revoke Visitor Access
```php
$approvalService->revokeAccess($visitor);
```

---

## 🎓 Learning Path

1. **Start with Models** - Understand data structure
2. **Review Services** - See business logic
3. **Check Controllers** - Understand API endpoints
4. **Study Events** - Learn event-driven architecture
5. **Review Jobs** - Understand background tasks
6. **Test API** - Use cURL or Postman

---

## 🚀 Next Steps

1. ✅ Run migrations and seed data
2. ✅ Start queue worker
3. ✅ Test API endpoints
4. ✅ Review audit logs
5. ✅ Configure cron scheduler
6. ✅ Deploy to production

---

**Ready to go!** 🎉 Your VMS is fully operational with all production-grade features.

