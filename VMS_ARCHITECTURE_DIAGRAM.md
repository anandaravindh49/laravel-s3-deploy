# VMS System Architecture & Data Flow

## 🏗️ System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    VISITOR MANAGEMENT SYSTEM                     │
└─────────────────────────────────────────────────────────────────┘

                         PUBLIC LAYER
                             │
                    POST /api/public/request
                    (No authentication required)
                             │
                    ┌────────▼────────┐
                    │ Public Request  │
                    │    Controller   │
                    └────────┬────────┘
                             │
                    ┌────────▼──────────────┐
                    │PublicRequestService  │
                    │  - Create Request    │
                    │  - Validate Input    │
                    └────────┬──────────────┘
                             │
                    ┌────────▼────────────┐
                    │ PublicRequest       │
                    │ (DB: pending)       │
                    └────────┬────────────┘
                             │
                ┌────────────┘
                │
                │
    ┌───────────▼──────────────────────────────────────────┐
    │           ADMIN APPROVAL LAYER                        │
    │           (Requires Sanctum Auth)                     │
    └───────────┬──────────────────────────────────────────┘
                │
    ┌───────────┴──────────────────────────────────────────┐
    │                                                       │
    ▼                                                       ▼
GET /api/requests                                  POST /api/requests/{id}/approve
    │                                                       │
    ├─▶ ApprovalController::approve()                       │
    │       │                                               │
    │       ├─▶ ApprovalService::approveRequest()           │
    │       │   ├─▶ Create ApprovedVisitor                  │
    │       │   ├─▶ Update PublicRequest status             │
    │       │   ├─▶ Add to DeviceUsers (all devices)        │
    │       │   └─▶ Dispatch PublicRequestApproved          │
    │       │       │                                        │
    │       │       └─▶ LogPublicRequestApproval            │
    │       │           └─▶ Create AuditLog                 │
    │       │                                               │
    │       └─▶ ApprovalService::addVisitorToDevices()      │
    │           └─▶ DeviceSyncService::syncApprovedUsersToDevice()
    │
    └─▶ Return ApprovedVisitor + badge_number
        (VMS-ABC123)


    ┌──────────────────────────────────────────────────────┐
    │         DEVICE MANAGEMENT LAYER                       │
    │         (Real-time Device Sync)                       │
    └──────────┬───────────────────────────────────────────┘
               │
    ┌──────────┴──────────────────────────────────────────┐
    │                                                      │
    ▼                                                      ▼
SyncApprovedUsersToDevice (Job - Every 5 min)    ProcessDeviceLogs (Job - Every min)
    │                                                      │
    ├─▶ DeviceSyncService::                               ├─▶ DeviceSyncService::
    │   syncApprovedUsersToDevice()                       │   pullDeviceLogs()
    │   │                                                  │   │
    │   ├─▶ Get all active devices                        │   ├─▶ Connect to device IP
    │   ├─▶ GET approved users from DB                    │   ├─▶ Fetch new logs
    │   ├─▶ POST to device: /api/sync-users               │   ├─▶ Process events
    │   ├─▶ Update synced_at timestamps                   │   └─▶ Create DeviceLog
    │   └─▶ Log in DeviceLog                              │
    │                                                      │
    └─▶ Device receives updated user list                 └─▶ Server synced with device


    ┌──────────────────────────────────────────────────────┐
    │          GATE OPERATIONS LAYER                        │
    │          (Real-time Check-in/out)                    │
    └──────────┬───────────────────────────────────────────┘
               │
    ┌──────────┴─────────────┬────────────────────────────┐
    │                        │                            │
    ▼                        ▼                            ▼
POST /api/gate/             POST /api/gate/            GET /api/gate/
    checkin                 checkout                  visitors/{id}/history
    │                        │                            │
    ├─▶ GateController::     ├─▶ GateController::         ├─▶ GateController::
    │   checkIn()            │   checkOut()               │   getHistory()
    │   │                    │   │                        │
    │   ├─▶ Find visitor by  │   ├─▶ Find visitor by      │   ├─▶ Get visitor record
    │   │   badge_number     │   │   badge_number        │   ├─▶ Fetch all gate logs
    │   │                    │   │                        │   └─▶ Return with timeline
    │   ├─▶ Validate status  │   ├─▶ Validate status
    │   ├─▶ GateService::    │   ├─▶ GateService::
    │   │   checkIn()        │   │   checkOut()
    │   │   │                │   │   │
    │   │   ├─▶ Create       │   │   ├─▶ Create
    │   │   │   GateLog      │   │   │   GateLog
    │   │   │   (type:       │   │   │   (type:
    │   │   │    checkin)    │   │   │    checkout)
    │   │   │                │   │   │
    │   │   ├─▶ Update       │   │   ├─▶ Update
    │   │   │   visitor      │   │   │   visitor
    │   │   │   status:      │   │   │   status:
    │   │   │   'active'     │   │   │   'checkout'
    │   │   │                │   │   │
    │   │   └─▶ Dispatch     │   │   └─▶ Dispatch
    │   │       VisitorCheckedIn  VisitorCheckedOut
    │   │       │                │   │
    │   │       └─▶ LogVisitor   │   └─▶ LogVisitor
    │   │           CheckIn      │       CheckOut
    │   │           │            │       │
    │   │           └─▶ Create   │       └─▶ Create
    │   │               AuditLog │           AuditLog
    │   │                        │
    │   └─▶ Return success       └─▶ Return success


    ┌──────────────────────────────────────────────────────┐
    │         AUDIT & COMPLIANCE LAYER                      │
    │         (Complete Audit Trail)                        │
    └──────────┬───────────────────────────────────────────┘
               │
    ┌──────────┴──────────────────────────────────────────┐
    │                                                      │
    ▼                                                      ▼
GET /api/audit/logs                           GET /api/audit/report
    │                                                      │
    ├─▶ AuditController::index()                          ├─▶ AuditController::report()
    │   │                                                  │   │
    │   ├─▶ AuditService::getLogs(filters)                │   ├─▶ AuditService::
    │   │   ├─▶ Query by module (optional)                │   │   getActivityReport()
    │   │   ├─▶ Query by action (optional)                │   │   │
    │   │   ├─▶ Query by user_id (optional)               │   │   ├─▶ Count by module
    │   │   ├─▶ Date range filtering                      │   │   ├─▶ Count by action
    │   │   └─▶ Paginated results                         │   │   └─▶ Count by user
    │   │                                                  │   │
    │   └─▶ Return logs with changes tracked              └─▶ Return statistics


    ┌──────────────────────────────────────────────────────┐
    │      AUTOMATED MAINTENANCE LAYER                      │
    │      (Scheduled Jobs)                                │
    └──────────┬───────────────────────────────────────────┘
               │
    ┌──────────┴──────────────────────────────────────────┐
    │                                                      │
    ▼                                                      ▼
AutoCheckoutExpiredVisitors        AuditLogCleanup
(Daily at 00:00)                   (Monthly)
    │                                  │
    ├─▶ Find visitors with             ├─▶ Delete logs older
    │   visit_date < today             │   than 6 months
    │                                  │
    ├─▶ GateService::checkOut()        └─▶ Maintain DB performance
    │   for each expired visitor
    │
    └─▶ Update status to 'expired'
```

---

## 📊 Data Flow Diagram

### Visitor Request → Approval → Check-in Flow

```
┌──────────────┐
│   Visitor    │
└────────┬─────┘
         │
         │ 1. Submit Public Request
         ▼
    ┌──────────────────┐
    │ public_requests  │
    │ status: pending  │
    └────────┬─────────┘
             │
             │ 2. Admin Reviews
             ▼
    ┌──────────────────────────┐
    │ Approve/Reject Decision  │
    └────┬──────────────┬──────┘
         │              │
         │ Approve      │ Reject
         │              │
         ▼              ▼
    ┌──────────────┐   │
    │approved_     │   │
    │visitors      │   │
    │status:active │   │
    └────┬─────────┘   │
         │             │
         │ 3. Add to   └─▶ Log Rejection
         │    devices      (audit_logs)
         │
         ▼
    ┌──────────────────┐
    │ device_users     │
    │ per device       │
    └────┬─────────────┘
         │
         │ 4. Sync to Device
         ├─▶ Device A (via HTTP API)
         ├─▶ Device B (via HTTP API)
         └─▶ Device C (via HTTP API)
             │
             ▼
         Visitor appears
         on all devices
         │
         │ 5. Visitor Check-in
         ▼
    ┌──────────────────┐
    │ gate_logs        │
    │ event_type:      │
    │ checkin          │
    └────┬─────────────┘
         │
         └─▶ Log event (audit_logs)
             Status: active
```

---

## 🔄 State Diagram: Visitor Status

```
                    ┌──────────────┐
                    │   Submitted  │
                    │   (pending)  │
                    └──────┬───────┘
                           │
                ┌──────────┴──────────┐
                │                    │
                │ Admin Decision     │
                │                    │
                ▼                    ▼
          ┌──────────┐          ┌──────────┐
          │ Approved │          │ Rejected │
          │ (pending)│          │(rejected)│
          └────┬─────┘          └──────────┘
               │                     │
               │ Added to            └─▶ End
               │ devices
               │
               ▼
          ┌──────────┐
          │  Active  │  ◄─── Visitor checked-in
          │(active)  │
          └────┬─────┘
               │
               │ Visitor checked-out
               ▼
          ┌──────────┐
          │ CheckOut │
          │(checkout)│
          └────┬─────┘
               │
               │ (optional) Auto-checkout
               │ if date passed
               ▼
          ┌──────────┐
          │ Expired  │
          │(expired) │
          └──────────┘
```

---

## 🗄️ Database Relationships

```
┌─────────────┐
│   devices   │ (3 gates/turnstiles)
└──────┬──────┘
       │
       ├─1─▶─N──┐
       │        │
       │        ▼
       │   ┌──────────────┐
       │   │ device_logs  │ (Sync events)
       │   └──────────────┘
       │
       ├─1─▶─N──┐
       │        │
       │        ▼
       │   ┌──────────────┐
       │   │ device_users │ (Active users per device)
       │   └──────────────┘
       │
       └─1─▶─N──┐
                │
                ▼
           ┌──────────────┐
           │  gate_logs   │ (Check-in/out events)
           └──────────────┘


┌──────────────────┐
│ public_requests  │ (Visitor requests)
└─────────┬────────┘
          │
          └─1─▶─1──┬──┐
                   │  │
                   │  └─▶ approved_by (User FK)
                   │
                   ▼
           ┌───────────────────┐
           │approved_visitors  │ (Approved visitors)
           └─────────┬─────────┘
                     │
                     └─1─▶─N──┐
                              │
                              ▼
                         ┌──────────────┐
                         │  gate_logs   │
                         └──────────────┘


                    ┌────────────────┐
                    │  audit_logs    │ (Complete trail)
                    └────────┬───────┘
                             │
                             └─▶ user_id (FK)
                             └─▶ auditable_type
                             └─▶ auditable_id
```

---

## 🎪 Event Flow

```
PublicRequest Created (Pending)
    │
    │
    ▼ (Admin approves)
Event: PublicRequestApproved
    │
    ├─▶ Listener: LogPublicRequestApproval
    │   └─▶ Create AuditLog (module: request, action: approve)
    │
    └─▶ Service: ApprovalService
        └─▶ Create ApprovedVisitor
        └─▶ Add to DeviceUsers
        └─▶ Sync to Devices


VisitorCheckedIn at Device
    │
    ├─▶ Create GateLog (event_type: checkin)
    │
    ├─▶ Event: VisitorCheckedIn
    │   │
    │   └─▶ Listener: LogVisitorCheckIn
    │       └─▶ Create AuditLog (module: gate, action: checkin)
    │
    └─▶ Return success response


VisitorCheckedOut at Device
    │
    ├─▶ Create GateLog (event_type: checkout)
    │
    ├─▶ Event: VisitorCheckedOut
    │   │
    │   └─▶ Listener: LogVisitorCheckOut
    │       └─▶ Create AuditLog (module: gate, action: checkout)
    │
    └─▶ Return success response
```

---

## 📱 API Interaction Pattern

```
CLIENT REQUEST
    │
    ▼
├─ Public Request?
│  └─ No Auth Required
│     ├─ Validate input
│     ├─ Create PublicRequest
│     └─ Return request_id
│
└─ Authenticated Request?
   └─ Check Sanctum Token
      ├─ Valid? ✓
      │  ├─ Find/Validate Resource
      │  ├─ Check Authorization
      │  ├─ Execute Service Method
      │  ├─ Handle Events (async)
      │  ├─ Create AuditLog
      │  └─ Return Response (JSON)
      │
      └─ Invalid? ✗
         └─ Return 401 Unauthorized
```

---

## 🔌 Integration Points

```
┌─────────────────────────────────────────────────┐
│           External Systems                       │
├─────────────────────────────────────────────────┤
│                                                  │
│ ┌────────────────────────────────────┐          │
│ │    Physical Devices (Gates)        │          │
│ │  - Receive: user list (JSON POST)  │          │
│ │  - Send: gate logs (HTTP API GET)  │          │
│ └────────────────────────────────────┘          │
│           ▲                │                     │
│           │                │                     │
│       HTTP/IP          HTTP/IP                   │
│      (REST API)        (REST API)                │
│           │                │                     │
│           └────┬───────────┘                     │
│                │                                 │
│                ▼                                 │
│ ┌────────────────────────────────────┐          │
│ │   VMS Laravel Application          │          │
│ │ - DeviceSyncService                │          │
│ │ - GateService                      │          │
│ │ - ProcessDeviceLogs Job            │          │
│ └────────────────────────────────────┘          │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## ⏱️ Timing & Frequency

```
Real-time (Synchronous):
├─ Public request submission
├─ Admin approval/rejection
├─ Visitor check-in/checkout
└─ Get active visitors

Every Minute:
├─ ProcessDeviceLogs job
└─ Pull fresh logs from all devices

Every 5 Minutes:
├─ SyncApprovedUsersToDevice job
└─ Push updated user list to all devices

Daily at 00:00:
├─ AutoCheckoutExpiredVisitors job
└─ Mark passed-date visitors as expired

Monthly:
├─ Audit log cleanup
└─ Delete logs older than 6 months
```

---

## 🎯 Request/Response Cycle

```
1. REQUEST (Client → Server)
   POST /api/gate/checkin
   {
     "badge_number": "VMS-ABC123",
     "device_id": 1
   }
              │
              ▼
2. ROUTE HANDLER
   Route::post('/gate/checkin', [GateController::class, 'checkIn'])
              │
              ▼
3. VALIDATION
   - Validate badge_number
   - Validate device_id exists
              │
              ▼
4. BUSINESS LOGIC (Service)
   GateService::checkIn($visitor, $device)
   - Find visitor
   - Check status
   - Create GateLog
              │
              ▼
5. EVENT DISPATCH
   Event: VisitorCheckedIn
              │
              ▼
6. EVENT LISTENERS
   - LogVisitorCheckIn listener
   - Create AuditLog
              │
              ▼
7. RESPONSE (Server → Client)
   {
     "message": "Check-in successful",
     "status": "success",
     "visitor": { ... },
     "checked_in_at": "2025-11-17T10:15:00Z"
   }
```

---

This architecture provides:
✅ Clear separation of concerns
✅ Scalable design
✅ Extensible event system
✅ Comprehensive audit trail
✅ Automated background tasks
✅ Real-time device synchronization
✅ Production-ready error handling

