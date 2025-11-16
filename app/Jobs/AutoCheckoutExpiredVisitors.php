<?php

namespace App\Jobs;

use App\Models\ApprovedVisitor;
use App\Services\GateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoCheckoutExpiredVisitors implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(GateService $gateService): void
    {
        // Auto checkout visitors whose visit date has passed
        $count = $gateService->autoCheckoutExpiredVisitors();
        
        \Log::info("Auto-checkout job: {$count} visitors checked out");
    }
}
