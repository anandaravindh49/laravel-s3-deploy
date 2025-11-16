<?php

namespace App\Events;

use App\Models\ApprovedVisitor;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisitorCheckedIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ApprovedVisitor $approvedVisitor,
        public ?int $deviceId = null,
        public ?array $metadata = null
    ) {}
}
