<?php

namespace App\Jobs;

use App\Models\Device;
use App\Services\DeviceSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDeviceLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(DeviceSyncService $deviceSyncService): void
    {
        // Process logs from all active devices every minute
        Device::active()->each(function (Device $device) use ($deviceSyncService) {
            $deviceSyncService->pullDeviceLogs($device);
        });
    }
}
