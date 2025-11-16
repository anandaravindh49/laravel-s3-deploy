# Visitor Management System (VMS) - Complete Documentation

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Folder Structure](#folder-structure)
3. [Database Schema](#database-schema)
4. [Models](#models)
5. [Services](#services)
6. [Controllers](#controllers)
7. [Events & Listeners](#events--listeners)
8. [Jobs & Scheduling](#jobs--scheduling)
9. [API Routes](#api-routes)
10. [Installation Guide](#installation-guide)
11. [Usage Examples](#usage-examples)
12. [Best Practices](#best-practices)

---

## 🎯 Project Overview

A production-ready **Visitor Management System (VMS)** built with Laravel 10+ featuring:

✅ **Device Management** - Manage gates, turnstiles, and scanners  
✅ **Public Request Forms** - Self-service visitor request submission  
✅ **Admin Approval Workflow** - Review and approve/reject visitor requests  
✅ **Gate Operations** - Real-time check-in/check-out tracking  
✅ **Device Synchronization** - Automatic sync of approved users to devices  
✅ **Comprehensive Audit Logging** - Full audit trail for compliance  
✅ **Event System** - Event-driven architecture for loose coupling  
✅ **Scheduled Jobs** - Automated background tasks  
✅ **Robust Error Handling** - Production-grade error management  

---

## 📁 Folder Structure

```
app/
├── Console/
│   └── Kernel.php                  # Scheduler configuration
├── Events/
│   ├── PublicRequestApproved.php
│   ├── PublicRequestRejected.php
│   ├── VisitorCheckedIn.php
│   └── VisitorCheckedOut.php
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── DeviceController.php
│           ├── PublicRequestController.php
│           ├── ApprovalController.php
│           ├── GateController.php
│           └── AuditController.php
├── Jobs/
│   ├── ProcessDeviceLogs.php
│   ├── SyncApprovedUsersToDevice.php
│   └── AutoCheckoutExpiredVisitors.php
├── Listeners/
│   ├── LogPublicRequestApproval.php
│   ├── LogPublicRequestRejection.php
│   ├── LogVisitorCheckIn.php
│   └── LogVisitorCheckOut.php
├── Models/
│   ├── Device.php
│   ├── DeviceLog.php
│   ├── DeviceUser.php
│   ├── PublicRequest.php
│   ├── ApprovedVisitor.php
│   ├── GateLog.php
│   └── AuditLog.php
├── Policies/
│   └── (Authorization policies can be added here)
└── Services/
    ├── DeviceSyncService.php
    ├── PublicRequestService.php
    ├── ApprovalService.php
    ├── GateService.php
    └── AuditService.php

database/
├── migrations/
│   ├── 2025_11_16_000001_create_devices_table.php
│   ├── 2025_11_16_000002_create_device_logs_table.php
│   ├── 2025_11_16_000003_create_device_users_table.php
│   ├── 2025_11_16_000004_create_public_requests_table.php
│   ├── 2025_11_16_000005_create_approved_visitors_table.php
│   ├── 2025_11_16_000006_create_gate_logs_table.php
│   └── 2025_11_16_000007_create_audit_logs_table.php
└── seeders/
    └── VisitorManagementSeeder.php
```

---

## 🗄️ Database Schema

### Devices Table
```sql
CREATE TABLE devices (
    id BIGINT PRIMARY KEY,
    device_id VARCHAR UNIQUE,
    name VARCHAR,
    location VARCHAR,
    device_type ENUM('gate', 'turnstile', 'scanner'),
    status ENUM('active', 'inactive', 'maintenance'),
    ip_address VARCHAR,
    firmware_version VARCHAR,
    last_sync_at TIMESTAMP,
    metadata JSON,
    timestamps
);
```

### Device Users Table
```sql
CREATE TABLE device_users (
    id BIGINT PRIMARY KEY,
    device_id BIGINT (FK),
    external_user_id VARCHAR,
    name VARCHAR,
    id_proof VARCHAR,
    access_level ENUM('visitor', 'employee', 'contractor'),
    status ENUM('active', 'revoked', 'expired'),
    valid_from DATE,
    valid_until DATE,
    synced_at TIMESTAMP,
    timestamps,
    UNIQUE(device_id, external_user_id)
);
```

### Public Requests Table
```sql
CREATE TABLE public_requests (
    id BIGINT PRIMARY KEY,
    visitor_name VARCHAR,
    visitor_email VARCHAR,
    visitor_phone VARCHAR,
    id_proof_type VARCHAR,
    id_proof_number VARCHAR,
    id_proof_image TEXT,
    purpose_of_visit TEXT,
    visit_date DATE,
    visit_time TIME,
    host_department VARCHAR,
    host_person_name VARCHAR,
    status ENUM('pending', 'approved', 'rejected'),
    approved_by BIGINT (FK),
    rejection_reason TEXT,
    approved_at TIMESTAMP,
    additional_notes TEXT,
    timestamps
);
```

### Approved Visitors Table
```sql
CREATE TABLE approved_visitors (
    id BIGINT PRIMARY KEY,
    public_request_id BIGINT (FK),
    visitor_name VARCHAR,
    visitor_phone VARCHAR,
    id_proof_type VARCHAR,
    id_proof_number VARCHAR,
    id_proof_image TEXT,
    purpose_of_visit TEXT,
    visit_date DATE,
    visit_time TIME,
    host_department VARCHAR,
    host_person_name VARCHAR,
    badge_number VARCHAR,
    status ENUM('pending', 'active', 'checkout', 'expired'),
    metadata JSON,
    timestamps,
    UNIQUE(public_request_id)
);
```

### Gate Logs Table
```sql
CREATE TABLE gate_logs (
    id BIGINT PRIMARY KEY,
    approved_visitor_id BIGINT (FK),
    device_id BIGINT (FK),
    visitor_name VARCHAR,
    badge_number VARCHAR,
    event_type ENUM('checkin', 'checkout'),
    event_at TIMESTAMP,
    ip_address VARCHAR,
    metadata JSON,
    timestamps
);
```

### Audit Logs Table
```sql
CREATE TABLE audit_logs (
    id BIGINT PRIMARY KEY,
    user_id BIGINT (FK),
    module VARCHAR,
    action VARCHAR,
    auditable_type VARCHAR,
    auditable_id BIGINT,
    old_data JSON,
    new_data JSON,
    ip_address VARCHAR,
    user_agent TEXT,
    created_at TIMESTAMP
);
```

---

## 🏗️ Models

### Device Model
- **Relationships**: hasMany(logs, users, gateLogs)
- **Methods**: isOnline(), getApprovedUsersList()
- **Scopes**: active()

### DeviceLog Model
- **Relationships**: belongsTo(device)
- **Scopes**: failed(), recent()

### DeviceUser Model
- **Relationships**: belongsTo(device)
- **Methods**: isValid()
- **Scopes**: active(), byDevice()

### PublicRequest Model
- **Relationships**: hasOne(approvedVisitor), belongsTo(user:approvedBy)
- **Methods**: canBeApproved(), canBeRejected()
- **Scopes**: pending(), approved(), forDate()

### ApprovedVisitor Model
- **Relationships**: belongsTo(publicRequest), hasMany(gateLogs)
- **Methods**: hasCheckedIn(), hasCheckedOut(), getLastCheckIn(), markAsExpired()
- **Scopes**: active(), byDate()

### GateLog Model
- **Relationships**: belongsTo(approvedVisitor, device)
- **Methods**: isCheckIn(), isCheckOut()
- **Scopes**: checkIns(), checkOuts(), onDate()

### AuditLog Model
- **Relationships**: belongsTo(user)
- **Methods**: getChanges()
- **Scopes**: forModule(), forAction(), forUser(), recent()

---

## 🔧 Services

### DeviceSyncService
Handles device synchronization:
- `syncApprovedUsersToDevice(Device)` - Push approved users to device
- `pullDeviceLogs(Device)` - Pull logs from device

### PublicRequestService
Manages visitor requests:
- `createRequest(array)` - Create new request
- `getPendingRequests()` - Get pending requests with pagination
- `getRequestDetails(PublicRequest)` - Get detailed request info

### ApprovalService
Handles approval workflow:
- `approveRequest(PublicRequest, userId)` - Approve request and create visitor
- `rejectRequest(PublicRequest, reason, userId)` - Reject request
- `revokeAccess(ApprovedVisitor)` - Revoke visitor access

### GateService
Manages gate operations:
- `checkIn(ApprovedVisitor, Device, metadata)` - Process check-in
- `checkOut(ApprovedVisitor, Device, metadata)` - Process check-out
- `getVisitorHistory(ApprovedVisitor)` - Get visitor movement history
- `getActiveVisitorsOnDevice(Device)` - Get currently present visitors
- `autoCheckoutExpiredVisitors()` - Auto checkout past due visitors

### AuditService
Provides audit functionality:
- `getLogs(filters)` - Get filtered audit logs
- `getResourceHistory(type, id)` - Get history for specific resource
- `getChangesSummary(AuditLog)` - Get change details
- `getActivityReport(filters)` - Get activity statistics

---

## 📡 Controllers

### DeviceController
- `index()` - List all devices
- `show(Device)` - Get device details
- `store(Request)` - Create device
- `update(Request, Device)` - Update device
- `destroy(Device)` - Delete device
- `getLogs(Device)` - Get device logs
- `sync(Device)` - Sync with device

### PublicRequestController
- `index()` - Get pending requests (admin)
- `store(Request)` - Submit public request
- `show(PublicRequest)` - Get request details

### ApprovalController
- `approve(Request, PublicRequest)` - Approve request
- `reject(Request, PublicRequest)` - Reject request
- `revoke(Request, PublicRequest)` - Revoke access

### GateController
- `checkIn(Request)` - Process check-in
- `checkOut(Request)` - Process check-out
- `getHistory(ApprovedVisitor)` - Get visitor history
- `getActiveVisitors(Device)` - Get active visitors on device

### AuditController
- `index(Request)` - Get audit logs
- `report(Request)` - Get activity report
- `resourceHistory(Request)` - Get resource history

---

## 🎪 Events & Listeners

### Events
1. **PublicRequestApproved**
   - Triggered when request is approved
   - Listener: LogPublicRequestApproval

2. **PublicRequestRejected**
   - Triggered when request is rejected
   - Listener: LogPublicRequestRejection

3. **VisitorCheckedIn**
   - Triggered when visitor checks in
   - Listener: LogVisitorCheckIn

4. **VisitorCheckedOut**
   - Triggered when visitor checks out
   - Listener: LogVisitorCheckOut

Each listener automatically creates audit log entries.

---

## ⚙️ Jobs & Scheduling

### Jobs

**ProcessDeviceLogs** - Every minute
- Pulls fresh logs from all active devices
- Processes gate events

**SyncApprovedUsersToDevice** - Every 5 minutes
- Syncs approved visitor list to all devices
- Updates device user database

**AutoCheckoutExpiredVisitors** - Daily at 00:00
- Auto-checks out visitors whose visit date has passed
- Prevents invalid access

**Audit Log Cleanup** - Monthly
- Deletes audit logs older than 6 months
- Maintains database performance

### Enabling Scheduler
Add to crontab:
```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🛣️ API Routes

All routes require Sanctum authentication except public request submission.

```php
// Public
POST /api/public/request

// Devices
GET    /api/devices
POST   /api/devices
GET    /api/devices/{device}
PATCH  /api/devices/{device}
DELETE /api/devices/{device}
GET    /api/devices/{device}/logs
POST   /api/devices/{device}/sync

// Requests
GET  /api/requests
GET  /api/requests/{request}

// Approval
POST /api/requests/{request}/approve
POST /api/requests/{request}/reject
POST /api/requests/{request}/revoke

// Gate
POST /api/gate/checkin
POST /api/gate/checkout
GET  /api/gate/visitors/{visitor}/history
GET  /api/gate/devices/{device}/active-visitors

// Audit
GET /api/audit/logs
GET /api/audit/report
GET /api/audit/resource-history
```

---

## 🚀 Installation Guide

### 1. Prerequisites
- PHP 8.1+
- Laravel 10+
- MySQL/PostgreSQL
- Composer
- Node.js (optional, for queue worker)

### 2. Installation Steps

```bash
# Clone repository
git clone <repo-url>
cd <project-folder>

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=localhost
# DB_DATABASE=vms
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed sample data (optional)
php artisan db:seed --class=VisitorManagementSeeder

# Start queue worker (in separate terminal)
php artisan queue:work

# Start Laravel server
php artisan serve
```

### 3. Configuration

Update `.env` for:
- Database credentials
- Queue connection (database, redis, etc.)
- Mail settings (for notifications)

---

## 💡 Usage Examples

### 1. Creating a Device
```php
$device = Device::create([
    'device_id' => 'GATE-001',
    'name' => 'Main Gate',
    'location' => 'Front Entrance',
    'device_type' => 'gate',
    'ip_address' => '192.168.1.100',
]);
```

### 2. Submitting a Visitor Request
```bash
POST /api/public/request
{
    "visitor_name": "John Visitor",
    "visitor_phone": "+91-9876543210",
    "purpose_of_visit": "Business Meeting",
    "visit_date": "2025-11-17",
    "host_department": "Sales",
    "host_person_name": "Jane Smith"
}
```

### 3. Approving a Request
```php
$approvalService->approveRequest($publicRequest, auth()->id());
```

### 4. Processing Check-In
```bash
POST /api/gate/checkin
{
    "badge_number": "VMS-rAkdPmXq",
    "device_id": 1
}
```

### 5. Getting Audit Trail
```bash
GET /api/audit/logs?module=request&action=approve
```

---

## 📋 Best Practices

1. **Always validate input** - Use form requests for validation
2. **Use services for business logic** - Keep controllers thin
3. **Leverage events** - For loose coupling and extensibility
4. **Monitor device sync status** - Check device logs regularly
5. **Archive old audit logs** - Use cleanup job for performance
6. **Use badges consistently** - Format: VMS-{RANDOM}
7. **Validate visit dates** - Prevent backdated requests
8. **Keep device IPs updated** - For reliable syncing
9. **Test gate operations** - Verify checkin/checkout flow
10. **Monitor queue jobs** - Ensure jobs are processing

---

## 🔒 Security Considerations

- All admin endpoints require Sanctum authentication
- Audit logs capture all changes for compliance
- Device IPs should be on private network
- Use HTTPS for all API communications
- Rate limit public endpoints
- Validate and sanitize all inputs
- Use database transactions for atomic operations

---

## 📞 Support & Troubleshooting

### Common Issues

**Queue jobs not processing**
- Ensure queue worker is running
- Check queue connection in config/queue.php
- Review queue:work logs

**Device sync failing**
- Verify device IP and connectivity
- Check device status (should be 'active')
- Review device logs for errors

**Audit logs growing too large**
- Verify cleanup job is running
- Check crontab for scheduler
- Consider archiving old logs

---

**Created with ❤️ using Laravel 10+**

