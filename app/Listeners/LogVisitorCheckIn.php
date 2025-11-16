<?php

namespace App\Listeners;

use App\Events\VisitorCheckedIn;
use App\Models\AuditLog;
use App\Models\ApprovedVisitor;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogVisitorCheckIn implements ShouldQueue
{
    public function handle(VisitorCheckedIn $event): void
    {
        AuditLog::create([
            'user_id' => null,
            'module' => 'gate',
            'action' => 'checkin',
            'auditable_type' => ApprovedVisitor::class,
            'auditable_id' => $event->approvedVisitor->id,
            'old_data' => ['status' => $event->approvedVisitor->status],
            'new_data' => ['status' => 'active', 'device_id' => $event->deviceId],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
