<?php

namespace App\Events;

use App\Models\PublicRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PublicRequestApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public PublicRequest $publicRequest,
        public ?int $userId = null
    ) {}
}
