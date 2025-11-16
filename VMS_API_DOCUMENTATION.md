# Visitor Management System (VMS) - API Documentation

## System Overview

A production-ready Laravel 10+ Visitor Management System with:
- Device management and synchronization
- Public visitor request forms
- Admin approval workflow
- Gate check-in/check-out system
- Comprehensive audit logging
- Real-time device syncing
- Automated job scheduling

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Run Seeder (Optional - for sample data)
```bash
php artisan db:seed --class=VisitorManagementSeeder
```

### 3. Start Queue Worker (for jobs)
```bash
php artisan queue:work
```

### 4. Start Scheduler (for cron jobs)
```bash
# Add to crontab:
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

---

## API Endpoints

### Public Endpoints

#### 1. Submit Visitor Request
**Endpoint:** `POST /api/public/request`

**Request:**
```json
{
    "visitor_name": "John Visitor",
    "visitor_email": "john@example.com",
    "visitor_phone": "+91-9876543210",
    "id_proof_type": "passport",
    "id_proof_number": "ABC123456",
    "purpose_of_visit": "Business Meeting",
    "visit_date": "2025-11-17",
    "visit_time": "10:00",
    "host_department": "Sales",
    "host_person_name": "Jane Smith",
    "additional_notes": "First time visitor"
}
```

**Response (201):**
```json
{
    "message": "Request submitted successfully",
    "request_id": 1,
    "status": "pending",
    "submitted_at": "2025-11-16T10:30:00Z"
}
```

---

### Authenticated Endpoints (Requires Sanctum Token)

#### Device Management

#### 2. List All Devices
**Endpoint:** `GET /api/devices`

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "device_id": "GATE-MAIN-001",
            "name": "Main Gate",
            "location": "Front Entrance",
            "type": "gate",
            "status": "active",
            "is_online": true,
            "last_sync": "2 minutes ago",
            "active_users_count": 5
        }
    ]
}
```

#### 3. Get Device Details
**Endpoint:** `GET /api/devices/{device_id}`

**Response:**
```json
{
    "device": {
        "id": 1,
        "device_id": "GATE-MAIN-001",
        "name": "Main Gate",
        "location": "Front Entrance",
        "type": "gate",
        "status": "active",
        "ip_address": "192.168.1.100",
        "firmware_version": "1.0.0",
        "is_online": true,
        "last_sync": "2025-11-16T10:25:30Z",
        "recent_logs": [
            {
                "id": 1,
                "event_type": "user_list_push",
                "status": "success",
                "logged_at": "2025-11-16T10:25:30Z"
            }
        ]
    }
}
```

#### 4. Create Device
**Endpoint:** `POST /api/devices`

**Request:**
```json
{
    "device_id": "GATE-NEW-001",
    "name": "New Gate",
    "location": "Back Entrance",
    "device_type": "gate",
    "ip_address": "192.168.1.150"
}
```

**Response (201):**
```json
{
    "message": "Device created successfully",
    "device": {
        "id": 4,
        "device_id": "GATE-NEW-001",
        ...
    }
}
```

#### 5. Update Device
**Endpoint:** `PATCH /api/devices/{device_id}`

**Request:**
```json
{
    "status": "maintenance",
    "firmware_version": "1.1.0"
}
```

#### 6. Delete Device
**Endpoint:** `DELETE /api/devices/{device_id}`

#### 7. Get Device Logs
**Endpoint:** `GET /api/devices/{device_id}/logs`

**Response:**
```json
{
    "device_id": 1,
    "logs": {
        "data": [
            {
                "id": 1,
                "event_type": "sync_request",
                "status": "success",
                "logged_at": "2025-11-16T10:25:30Z",
                "error_message": null
            }
        ],
        "pagination": {
            "total": 50,
            "current_page": 1,
            "last_page": 3,
            "per_page": 20
        }
    }
}
```

#### 8. Sync Device
**Endpoint:** `POST /api/devices/{device_id}/sync`

**Response:**
```json
{
    "message": "Device synced successfully",
    "status": true,
    "last_sync": "2025-11-16T10:30:00Z"
}
```

---

#### Public Request Management

#### 9. Get Pending Requests
**Endpoint:** `GET /api/requests`

**Response:**
```json
{
    "requests": {
        "data": [
            {
                "id": 1,
                "visitor_name": "Rajesh Kumar",
                "visitor_phone": "+91-9876543210",
                "purpose_of_visit": "Business Meeting",
                "visit_date": "2025-11-17",
                "visit_time": "10:00",
                "host_department": "Sales",
                "status": "pending",
                "created_at": "2025-11-16 10:15:30"
            }
        ],
        "pagination": {
            "total": 25,
            "current_page": 1,
            "last_page": 2,
            "per_page": 20
        }
    }
}
```

#### 10. Get Request Details
**Endpoint:** `GET /api/requests/{request_id}`

**Response:**
```json
{
    "request": {
        "id": 1,
        "visitor_name": "Rajesh Kumar",
        "visitor_email": "rajesh@example.com",
        "visitor_phone": "+91-9876543210",
        "id_proof_type": "passport",
        "id_proof_number": "ABC123456",
        "purpose_of_visit": "Business Meeting",
        "visit_date": "2025-11-17",
        "visit_time": "10:00",
        "host_department": "Sales",
        "host_person_name": "John Doe",
        "status": "pending",
        "approved_at": null,
        "rejection_reason": null,
        "created_at": "2025-11-16T10:15:30Z"
    }
}
```

---

#### Approval Workflow

#### 11. Approve Request
**Endpoint:** `POST /api/requests/{request_id}/approve`

**Response:**
```json
{
    "message": "Request approved successfully",
    "approved_visitor": {
        "id": 1,
        "visitor_name": "Rajesh Kumar",
        "badge_number": "VMS-rAkdPmXq",
        "visit_date": "2025-11-17",
        "status": "pending"
    }
}
```

#### 12. Reject Request
**Endpoint:** `POST /api/requests/{request_id}/reject`

**Request:**
```json
{
    "reason": "Invalid ID proof"
}
```

**Response:**
```json
{
    "message": "Request rejected successfully",
    "request_id": 1
}
```

#### 13. Revoke Visitor Access
**Endpoint:** `POST /api/requests/{request_id}/revoke`

**Response:**
```json
{
    "message": "Access revoked successfully",
    "visitor_id": 1
}
```

---

#### Gate Operations

#### 14. Visitor Check-In
**Endpoint:** `POST /api/gate/checkin`

**Request:**
```json
{
    "badge_number": "VMS-rAkdPmXq",
    "device_id": 1
}
```

**Response:**
```json
{
    "message": "Check-in successful",
    "status": "success",
    "visitor": {
        "id": 1,
        "name": "Rajesh Kumar",
        "badge_number": "VMS-rAkdPmXq",
        "purpose": "Business Meeting"
    },
    "log_id": 1,
    "checked_in_at": "2025-11-17T10:15:00Z"
}
```

#### 15. Visitor Check-Out
**Endpoint:** `POST /api/gate/checkout`

**Request:**
```json
{
    "badge_number": "VMS-rAkdPmXq",
    "device_id": 1
}
```

**Response:**
```json
{
    "message": "Check-out successful",
    "status": "success",
    "visitor": {
        "id": 1,
        "name": "Rajesh Kumar",
        "badge_number": "VMS-rAkdPmXq"
    },
    "log_id": 2,
    "checked_out_at": "2025-11-17T12:30:00Z"
}
```

#### 16. Get Visitor History
**Endpoint:** `GET /api/gate/visitors/{visitor_id}/history`

**Response:**
```json
{
    "visitor": {
        "id": 1,
        "name": "Rajesh Kumar",
        "badge_number": "VMS-rAkdPmXq"
    },
    "history": [
        {
            "id": 2,
            "event_type": "checkout",
            "device_name": "Main Gate",
            "event_at": "2025-11-17 12:30:00"
        },
        {
            "id": 1,
            "event_type": "checkin",
            "device_name": "Main Gate",
            "event_at": "2025-11-17 10:15:00"
        }
    ]
}
```

#### 17. Get Active Visitors on Device
**Endpoint:** `GET /api/gate/devices/{device_id}/active-visitors`

**Response:**
```json
{
    "device": {
        "id": 1,
        "name": "Main Gate",
        "location": "Front Entrance"
    },
    "active_visitors": [
        {
            "visitor_id": 1,
            "visitor_name": "Rajesh Kumar",
            "badge_number": "VMS-rAkdPmXq",
            "checked_in_at": "2025-11-17 10:15:00"
        },
        {
            "visitor_id": 2,
            "visitor_name": "Priya Singh",
            "badge_number": "VMS-PrYa002",
            "checked_in_at": "2025-11-17 14:20:00"
        }
    ]
}
```

---

#### Audit & Reporting

#### 18. Get Audit Logs
**Endpoint:** `GET /api/audit/logs?module=request&action=approve&user_id=1`

**Query Parameters:**
- `module` (optional): device, user, request, approval, gate
- `action` (optional): create, update, delete, approve, reject, checkin, checkout
- `user_id` (optional): Filter by user
- `start_date` (optional): Filter from date (YYYY-MM-DD)
- `end_date` (optional): Filter to date (YYYY-MM-DD)
- `per_page` (optional): Results per page (default: 50)

**Response:**
```json
{
    "logs": [
        {
            "id": 1,
            "user": "Admin User",
            "module": "request",
            "action": "approve",
            "auditable": {
                "type": "PublicRequest",
                "id": 1
            },
            "changes": {
                "status": {
                    "old": "pending",
                    "new": "approved"
                }
            },
            "ip_address": "192.168.1.1",
            "created_at": "2025-11-16 10:20:30"
        }
    ],
    "pagination": {
        "total": 100,
        "current_page": 1,
        "last_page": 2,
        "per_page": 50
    }
}
```

#### 19. Get Activity Report
**Endpoint:** `GET /api/audit/report`

**Response:**
```json
{
    "report": {
        "total": 150,
        "by_module": {
            "request": 45,
            "gate": 78,
            "device": 27
        },
        "by_action": {
            "checkin": 40,
            "checkout": 38,
            "approve": 20,
            "create": 15,
            "update": 37
        },
        "by_user": {
            "1": 100,
            "2": 50
        }
    }
}
```

#### 20. Get Resource Audit History
**Endpoint:** `GET /api/audit/resource-history?type=App\Models\PublicRequest&id=1`

**Response:**
```json
{
    "type": "App\Models\PublicRequest",
    "id": 1,
    "history": [
        {
            "id": 5,
            "action": "approve",
            "user": "Admin User",
            "changes": {
                "status": {
                    "old": "pending",
                    "new": "approved"
                }
            },
            "created_at": "2025-11-16T10:20:30Z"
        },
        {
            "id": 4,
            "action": "create",
            "user": "System",
            "changes": {},
            "created_at": "2025-11-16T10:15:30Z"
        }
    ]
}
```

---

## Error Responses

### 400 Bad Request
```json
{
    "message": "Request cannot be approved",
    "errors": {
        "visit_date": ["Visit date must be in the future"]
    }
}
```

### 404 Not Found
```json
{
    "message": "Visitor not found",
    "status": "denied"
}
```

### 403 Forbidden
```json
{
    "message": "Visitor pass expired",
    "status": "denied"
}
```

### 500 Server Error
```json
{
    "message": "Check-in error: Connection timeout",
    "status": "error"
}
```

---

## Database Schema

### Tables
- `devices` - Physical gate/turnstile devices
- `device_logs` - Device sync and operation logs
- `device_users` - Users synced to devices
- `public_requests` - Visitor request forms
- `approved_visitors` - Approved visitor records
- `gate_logs` - Check-in/check-out records
- `audit_logs` - Complete audit trail

---

## Jobs & Scheduling

### Automated Jobs

1. **ProcessDeviceLogs** - Every minute
   - Pulls logs from all active devices
   - Processes gate events
   
2. **SyncApprovedUsersToDevice** - Every 5 minutes
   - Syncs approved visitor list to all devices
   
3. **AutoCheckoutExpiredVisitors** - Daily at 00:00
   - Auto-checks out visitors whose visit date has passed
   
4. **Audit Log Cleanup** - Monthly
   - Deletes audit logs older than 6 months

---

## Event System

The system dispatches events that trigger audit logging:

- `PublicRequestApproved` - When request is approved
- `PublicRequestRejected` - When request is rejected
- `VisitorCheckedIn` - When visitor checks in
- `VisitorCheckedOut` - When visitor checks out

Each event automatically creates audit log entries.

---

## Authentication

The system uses Laravel Sanctum for API authentication.

### Getting a Token
```bash
POST /api/login
{
    "email": "admin@vms.local",
    "password": "password"
}
```

### Using Token
Add to request headers:
```
Authorization: Bearer {token}
```

---

## Best Practices

1. **Device IP Management**: Keep device IPs up-to-date for reliable syncing
2. **Batch Operations**: Use device sync job for bulk updates
3. **Audit Trail**: Monitor audit logs for security and compliance
4. **Badge Numbering**: Use consistent badge format (VMS-*)
5. **Visit Date Validation**: Prevent backdated visitor requests

---

## Troubleshooting

### Device Not Syncing
- Check device IP address and connectivity
- Verify device status is 'active'
- Check recent device logs for errors

### Visitor Not Appearing on Device
- Verify request is approved
- Check device is active and online
- Review device user list
- Check SyncApprovedUsersToDevice job

### Check-in Failed
- Verify visitor badge exists
- Check visit date is current or future
- Ensure device is online
- Review gate logs for errors

