<?php

namespace App\Listeners;

use App\Events\PublicRequestApproved;
use App\Models\AuditLog;
use App\Models\PublicRequest;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPublicRequestApproval implements ShouldQueue
{
    public function handle(PublicRequestApproved $event): void
    {
        AuditLog::create([
            'user_id' => $event->userId,
            'module' => 'request',
            'action' => 'approve',
            'auditable_type' => PublicRequest::class,
            'auditable_id' => $event->publicRequest->id,
            'old_data' => ['status' => 'pending'],
            'new_data' => ['status' => 'approved'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
