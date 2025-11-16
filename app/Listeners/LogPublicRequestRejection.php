<?php

namespace App\Listeners;

use App\Events\PublicRequestRejected;
use App\Models\AuditLog;
use App\Models\PublicRequest;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPublicRequestRejection implements ShouldQueue
{
    public function handle(PublicRequestRejected $event): void
    {
        AuditLog::create([
            'user_id' => $event->userId,
            'module' => 'request',
            'action' => 'reject',
            'auditable_type' => PublicRequest::class,
            'auditable_id' => $event->publicRequest->id,
            'old_data' => ['status' => 'pending'],
            'new_data' => ['status' => 'rejected', 'reason' => $event->rejectionReason],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
