<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\DeviceUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeviceSyncService
{
    /**
     * Sync approved users list to device
     */
    public function syncApprovedUsersToDevice(Device $device): bool
    {
        try {
            $users = $device->getApprovedUsersList();

            $payload = [
                'device_id' => $device->device_id,
                'timestamp' => now()->toIso8601String(),
                'users' => $users->map(fn ($user) => [
                    'external_user_id' => $user->external_user_id,
                    'name' => $user->name,
                    'id_proof' => $user->id_proof,
                    'access_level' => $user->access_level,
                    'valid_from' => $user->valid_from,
                    'valid_until' => $user->valid_until,
                ])->toArray(),
            ];

            // Send to device API
            $response = Http::timeout(10)
                ->post("http://{$device->ip_address}/api/sync-users", $payload)
                ->json();

            if ($response['success'] ?? false) {
                DeviceUser::query()
                    ->where('device_id', $device->id)
                    ->update(['synced_at' => now()]);

                $device->update(['last_sync_at' => now()]);

                $this->logDeviceEvent($device, 'user_list_push', 'success', $payload);

                return true;
            }

            $this->logDeviceEvent($device, 'user_list_push', 'failed', 
                ['error' => $response['message'] ?? 'Unknown error']);

            return false;
        } catch (\Exception $e) {
            Log::error('Device sync error', [
                'device_id' => $device->id,
                'error' => $e->getMessage(),
            ]);

            $this->logDeviceEvent($device, 'user_list_push', 'failed', 
                ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Pull logs from device
     */
    public function pullDeviceLogs(Device $device): bool
    {
        try {
            $response = Http::timeout(10)
                ->get("http://{$device->ip_address}/api/logs", [
                    'since' => $device->last_sync_at?->toIso8601String(),
                ])
                ->json();

            if (!($response['success'] ?? false)) {
                $this->logDeviceEvent($device, 'sync_request', 'failed', 
                    ['error' => $response['message'] ?? 'Unknown error']);
                return false;
            }

            $logs = $response['logs'] ?? [];
            foreach ($logs as $log) {
                // Process log - example for gate events
                // This would be handled by the GateService
            }

            $device->update(['last_sync_at' => now()]);

            $this->logDeviceEvent($device, 'sync_request', 'success', 
                ['logs_count' => count($logs)]);

            return true;
        } catch (\Exception $e) {
            Log::error('Pull device logs error', [
                'device_id' => $device->id,
                'error' => $e->getMessage(),
            ]);

            $this->logDeviceEvent($device, 'sync_request', 'failed', 
                ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Log device event
     */
    private function logDeviceEvent(Device $device, string $eventType, string $status, ?array $data = null): void
    {
        DeviceLog::create([
            'device_id' => $device->id,
            'event_type' => $eventType,
            'log_data' => $data,
            'status' => $status,
            'logged_at' => now(),
        ]);
    }
}
