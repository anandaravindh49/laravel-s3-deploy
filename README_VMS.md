# 🎉 Visitor Management System (VMS) - Complete Implementation

A **production-ready, enterprise-grade Visitor Management System** built with Laravel 10+ featuring device synchronization, visitor request management, real-time gate operations, and comprehensive audit logging.

## ✨ What's Included

### 📦 Complete Backend System
- ✅ **7 Database Tables** with proper relationships and indexes
- ✅ **7 Eloquent Models** with business logic and scopes
- ✅ **5 Service Classes** handling business operations
- ✅ **5 API Controllers** with 20+ RESTful endpoints
- ✅ **4 Events & Listeners** for event-driven architecture
- ✅ **3 Background Jobs** for scheduled operations
- ✅ **Event Service Provider** for clean event mapping
- ✅ **Console Kernel** with scheduler configuration

### 🛣️ API Features
- **Public Endpoints**: Visitor request submission (no auth required)
- **Device Management**: Create, manage, and sync physical devices
- **Request Workflow**: Admin approval/rejection with audit trail
- **Gate Operations**: Real-time check-in/checkout with history
- **Audit System**: Complete audit trail with filtering and reporting

### 🔐 Security & Compliance
- Sanctum API authentication
- Input validation on all endpoints
- SQL injection prevention (Eloquent ORM)
- CSRF protection
- Complete audit logging for compliance
- Access level control
- Status validation checks

### 📚 Comprehensive Documentation
- **API Documentation** - All 20+ endpoints with examples
- **Architecture Guide** - System design and data flow
- **Quick Start Guide** - Setup in 5 minutes
- **Detailed Documentation** - Models, services, best practices
- **Architecture Diagrams** - Visual representations of data flow

---

## 🚀 Quick Start (5 Minutes)

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Sample Data
```bash
php artisan db:seed --class=VisitorManagementSeeder
```

### 3. Start Queue Worker
```bash
php artisan queue:work
```

### 4. Start Laravel Server
```bash
php artisan serve
```

### 5. Access Dashboard
```
URL: http://localhost:8000
Email: admin@vms.local
Password: password
```

---

## 📖 Documentation Files

| Document | Purpose | Content |
|----------|---------|---------|
| **VMS_QUICK_START_GUIDE.md** | Get started in 5 minutes | Setup, testing, troubleshooting |
| **VMS_API_DOCUMENTATION.md** | Complete API reference | 20+ endpoints with request/response examples |
| **VMS_COMPLETE_DOCUMENTATION.md** | Architecture & design | Models, services, database schema, best practices |
| **VMS_ARCHITECTURE_DIAGRAM.md** | Visual system design | Data flow, state diagrams, integration points |
| **VMS_IMPLEMENTATION_SUMMARY.md** | Project metrics | Checklist, statistics, deployment guide |
| **VMS_PROJECT_FILE_INDEX.md** | File reference | Complete file listing and quick reference |

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────┐
│    Visitor Management System        │
├─────────────────────────────────────┤
│  PUBLIC API                          │
│  POST /api/public/request            │
├─────────────────────────────────────┤
│  ADMIN WORKFLOW                      │
│  - List requests                     │
│  - Approve/Reject                    │
│  - Revoke access                     │
├─────────────────────────────────────┤
│  DEVICE SYNC                         │
│  - Real-time synchronization         │
│  - Pull device logs                  │
│  - Push user lists                   │
├─────────────────────────────────────┤
│  GATE OPERATIONS                     │
│  - Check-in/Checkout                 │
│  - Visitor history                   │
│  - Active visitor tracking           │
├─────────────────────────────────────┤
│  AUDIT & REPORTING                   │
│  - Complete audit trail              │
│  - Activity reports                  │
│  - Resource history                  │
└─────────────────────────────────────┘
```

---

## 📊 Key Statistics

| Category | Count |
|----------|-------|
| **Files Created** | 32 |
| **Lines of Code** | 3500+ |
| **Migrations** | 7 |
| **Models** | 7 |
| **Services** | 5 |
| **Controllers** | 5 |
| **Events** | 4 |
| **Listeners** | 4 |
| **Jobs** | 3 |
| **API Endpoints** | 20+ |
| **Database Tables** | 7 |
| **Documentation Pages** | 6 |

---

## 🎯 Core Features

### Device Management
- Create and manage physical gates, turnstiles, and scanners
- Real-time device status monitoring
- Automatic synchronization of approved users
- Device operation logging with error tracking

### Visitor Request Management
- Public form submission for visitor requests
- ID proof image storage support
- Admin approval/rejection workflow
- Automatic badge generation for approved visitors

### Gate Operations
- Real-time visitor check-in/check-out
- Visitor history tracking
- Active visitor reporting per device
- Automatic checkout for expired visitors

### Audit & Compliance
- Complete audit trail for all operations
- Action tracking with user identification
- Change tracking (old data → new data)
- Activity reports and statistics
- Resource-level history lookup

### Automated Tasks
- ProcessDeviceLogs (every minute)
- SyncApprovedUsersToDevice (every 5 minutes)
- AutoCheckoutExpiredVisitors (daily at 00:00)
- AuditLogCleanup (monthly)

---

## 🔌 API Endpoints Overview

### Public (No Authentication)
```
POST /api/public/request          Submit visitor request
```

### Device Management (7 endpoints)
```
GET    /api/devices               List all devices
GET    /api/devices/{id}          Get device details
POST   /api/devices               Create device
PATCH  /api/devices/{id}          Update device
DELETE /api/devices/{id}          Delete device
GET    /api/devices/{id}/logs     Get device logs
POST   /api/devices/{id}/sync     Sync with device
```

### Request Management (2 endpoints)
```
GET /api/requests                 List pending requests
GET /api/requests/{id}            Get request details
```

### Approval Workflow (3 endpoints)
```
POST /api/requests/{id}/approve   Approve request
POST /api/requests/{id}/reject    Reject request
POST /api/requests/{id}/revoke    Revoke access
```

### Gate Operations (4 endpoints)
```
POST /api/gate/checkin                          Visitor check-in
POST /api/gate/checkout                         Visitor check-out
GET  /api/gate/visitors/{id}/history            Visitor history
GET  /api/gate/devices/{id}/active-visitors     Active visitors
```

### Audit & Reporting (3 endpoints)
```
GET /api/audit/logs               Get audit logs with filtering
GET /api/audit/report             Generate activity report
GET /api/audit/resource-history   Get resource history
```

---

## 💾 Database Tables

| Table | Purpose | Rows |
|-------|---------|------|
| `devices` | Physical gates/turnstiles | ~10 |
| `device_logs` | Device sync logs | N |
| `device_users` | Users per device | N |
| `public_requests` | Visitor requests | N |
| `approved_visitors` | Approved visitors | N |
| `gate_logs` | Check-in/out events | N |
| `audit_logs` | Complete audit trail | N |

---

## 🎓 Getting Started

### Prerequisites
- PHP 8.1+
- Laravel 10+
- MySQL/PostgreSQL
- Composer
- Node.js (optional, for queue worker)

### Installation
```bash
# 1. Clone or download the project
cd myapp

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Configure database in .env
# DB_DATABASE=vms
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Run migrations
php artisan migrate

# 7. Seed sample data (optional)
php artisan db:seed --class=VisitorManagementSeeder

# 8. Start queue worker (new terminal)
php artisan queue:work

# 9. Start Laravel server
php artisan serve

# 10. Setup scheduler (add to crontab)
# * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📱 API Testing

### Submit Visitor Request
```bash
curl -X POST http://localhost:8000/api/public/request \
  -H "Content-Type: application/json" \
  -d '{
    "visitor_name": "John Visitor",
    "visitor_phone": "+91-9876543210",
    "purpose_of_visit": "Business Meeting",
    "visit_date": "2025-11-17",
    "host_department": "Sales",
    "host_person_name": "Jane Smith"
  }'
```

### Check-in Visitor
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

## 🔐 Default Credentials

```
Email: admin@vms.local
Password: password
```

---

## 📂 Project Structure

```
app/
├── Console/Kernel.php              # Scheduler
├── Events/                          # 4 event classes
├── Http/Controllers/Api/            # 5 controllers
├── Jobs/                            # 3 job classes
├── Listeners/                       # 4 listener classes
├── Models/                          # 7 models
├── Providers/EventServiceProvider   # Event mapping
├── Services/                        # 5 services
└── Policies/                        # Authorization

database/
├── migrations/                      # 7 migrations
└── seeders/VisitorManagementSeeder

routes/
└── api.php                          # 20+ endpoints

Documentation files:
├── VMS_API_DOCUMENTATION.md
├── VMS_COMPLETE_DOCUMENTATION.md
├── VMS_QUICK_START_GUIDE.md
├── VMS_ARCHITECTURE_DIAGRAM.md
├── VMS_IMPLEMENTATION_SUMMARY.md
└── VMS_PROJECT_FILE_INDEX.md
```

---

## ✅ Production Readiness

- ✅ Comprehensive error handling
- ✅ Input validation on all endpoints
- ✅ Database indexes for performance
- ✅ Eager loading to prevent N+1 queries
- ✅ Event-driven architecture for scalability
- ✅ Queue jobs for heavy operations
- ✅ Audit trail for compliance
- ✅ Automated cleanup jobs
- ✅ Type hints and return types
- ✅ Follows PSR standards

---

## 🛠️ Troubleshooting

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

## 📞 Documentation Reference

| Document | Best For |
|----------|----------|
| **VMS_QUICK_START_GUIDE.md** | Getting started quickly |
| **VMS_API_DOCUMENTATION.md** | Understanding all API endpoints |
| **VMS_COMPLETE_DOCUMENTATION.md** | Learning architecture & design |
| **VMS_ARCHITECTURE_DIAGRAM.md** | Understanding data flow |
| **VMS_IMPLEMENTATION_SUMMARY.md** | Deployment & metrics |
| **VMS_PROJECT_FILE_INDEX.md** | File reference & quick lookup |

---

## 🎯 Next Steps

1. ✅ Read **VMS_QUICK_START_GUIDE.md**
2. ✅ Run the setup commands
3. ✅ Test the API with cURL
4. ✅ Review **VMS_API_DOCUMENTATION.md**
5. ✅ Study **VMS_ARCHITECTURE_DIAGRAM.md**
6. ✅ Deploy to production

---

## 📜 License

This project is created as a complete, production-ready solution for Visitor Management.

---

## 🤝 Support

For detailed information:
- 📖 Check the documentation files
- 🔍 Review the code comments
- 🎓 Study the service layer

---

## 🎉 You're All Set!

Your complete Visitor Management System is ready for:
- ✅ Development
- ✅ Testing
- ✅ Production Deployment

**Total Setup Time**: ~5 minutes  
**Total Implementation**: Complete and documented  
**Production Ready**: Yes!

---

**Created with ❤️ using Laravel 10+**

For more details, see the documentation files included in the project root.

