<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\ApprovedVisitor;
use App\Services\DeviceSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncApprovedUsersToDevice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private ?int $deviceId = null
    ) {}

    public function handle(DeviceSyncService $deviceSyncService): void
    {
        if ($this->deviceId) {
            // Sync specific device
            $device = Device::find($this->deviceId);
            if ($device) {
                $deviceSyncService->syncApprovedUsersToDevice($device);
            }
        } else {
            // Sync all active devices
            Device::active()->each(function (Device $device) use ($deviceSyncService) {
                $deviceSyncService->syncApprovedUsersToDevice($device);
            });
        }
    }
}
