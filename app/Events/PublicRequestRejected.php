<?php

namespace App\Events;

use App\Models\PublicRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PublicRequestRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public PublicRequest $publicRequest,
        public ?string $rejectionReason = null,
        public ?int $userId = null
    ) {}
}
