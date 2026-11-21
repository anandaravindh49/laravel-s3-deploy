# Visitor Management System (Laravel)

A Laravel-based Visitor Management System (VMS) for managing visitor requests, approval workflows, gate check-in/check-out, device synchronization, and audit history.

> **Repository note:** This repository is currently named `laravel-s3-deploy`, but the inspected application code and documentation describe a Visitor Management System. The repository name is historical and does not describe the main application purpose.

## Project overview

The application is organized around these workflows:

- **Visitor requests:** accept public visitor requests and track their status.
- **Approval workflow:** approve, reject, or revoke visitor access.
- **Gate operations:** record visitor check-in and check-out events.
- **Device management:** manage devices and process device logs / synchronization jobs.
- **Audit trail:** record important approval and gate actions for later review.
- **Background processing:** use Laravel jobs, events, and listeners for asynchronous work.

## Technology stack

- PHP `^8.2`
- Laravel `^12.0`
- Livewire `^3.6`
- AWS SDK for PHP and Flysystem S3 adapter (dependencies are present in Composer)
- Laravel queues, events, listeners, scheduler, and Eloquent ORM
- PHPUnit/Pest-related test dependencies

The repository includes dedicated VMS documentation files covering the API, architecture, quick start, implementation summary, and project file index.

## Architecture at a glance

```text
Visitor / Admin
      |
      v
Laravel Web UI and API
      |
      +--> Visitor request workflow
      |
      +--> Approval / rejection / revocation
      |
      +--> Gate check-in / check-out
      |
      +--> Device management and synchronization
      |
      +--> Audit logging
      |
      v
Database + Laravel Queue / Scheduler
```

Relevant application areas include:

- `app/Http/Controllers/Api/` — API controllers
- `app/Services/` — approval, audit, device sync, gate, and request services
- `app/Jobs/` — background processing
- `app/Events/` and `app/Listeners/` — event-driven audit and workflow handling
- `app/Models/` — Eloquent models
- `database/migrations/` — database schema

## Local development setup

### Prerequisites

- PHP 8.2 or later
- Composer
- Node.js and npm
- A database supported by the configured Laravel connection

### Install

```bash
git clone https://github.com/anandaravindh49/laravel-s3-deploy.git
cd laravel-s3-deploy

composer install
cp .env.example .env
php artisan key:generate
```

Configure the database and other required values in `.env`. Do not commit the real `.env` file or credentials.

Then run:

```bash
php artisan migrate
npm install
npm run build
php artisan serve
```

In a separate terminal, start the queue worker if you are testing queued workflows:

```bash
php artisan queue:work
```

For scheduled tasks, configure Laravel's scheduler according to the deployment environment and inspect the registered tasks with:

```bash
php artisan schedule:list
```

## Documentation

| File | Description |
| --- | --- |
| [VMS_QUICK_START_GUIDE.md](VMS_QUICK_START_GUIDE.md) | Setup and example API usage |
| [VMS_API_DOCUMENTATION.md](VMS_API_DOCUMENTATION.md) | API endpoint reference |
| [VMS_COMPLETE_DOCUMENTATION.md](VMS_COMPLETE_DOCUMENTATION.md) | Application components and design |
| [VMS_ARCHITECTURE_DIAGRAM.md](VMS_ARCHITECTURE_DIAGRAM.md) | Architecture and workflow diagrams |
| [VMS_IMPLEMENTATION_SUMMARY.md](VMS_IMPLEMENTATION_SUMMARY.md) | Implementation notes |
| [README_VMS.md](README_VMS.md) | VMS feature overview |

## Security notes

- Use unique, strong credentials for every environment.
- Keep `.env`, access tokens, AWS keys, database passwords, and real visitor information out of Git.
- Set `APP_DEBUG=false` in production.
- Do not use sample or seeded credentials on an internet-accessible deployment.
- Review access control, validation, file uploads, queue configuration, and device connectivity before production use.
- Enable GitHub secret scanning, push protection, and dependency alerts where available.

## Testing

Run the Laravel test suite with:

```bash
php artisan test
```

Add or update tests when changing request approval, access revocation, device synchronization, gate operations, or audit behavior.

## Project status

This README documents the code and supporting documentation currently present in the repository. It does not claim that the application is deployed, production-certified, or connected to live hardware. Verify those details in the target environment before describing them as delivered outcomes.

## Maintainer

**Aravindh Anand**  
GitHub: [@anandaravindh49](https://github.com/anandaravindh49)  
LinkedIn: [Aravindh Anand](https://www.linkedin.com/in/aravindh-anand-002278236/)
