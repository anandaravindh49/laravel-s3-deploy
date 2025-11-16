<?php

namespace App\Listeners;

use App\Events\VisitorCheckedOut;
use App\Models\AuditLog;
use App\Models\ApprovedVisitor;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogVisitorCheckOut implements ShouldQueue
{
    public function handle(VisitorCheckedOut $event): void
    {
        AuditLog::create([
            'user_id' => null,
            'module' => 'gate',
            'action' => 'checkout',
            'auditable_type' => ApprovedVisitor::class,
            'auditable_id' => $event->approvedVisitor->id,
            'old_data' => ['status' => $event->approvedVisitor->status],
            'new_data' => ['status' => 'checkout', 'device_id' => $event->deviceId],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
