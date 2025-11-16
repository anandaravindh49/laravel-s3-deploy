<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\DeviceSyncService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function __construct(
        private DeviceSyncService $deviceSyncService
    ) {}

    /**
     * List all devices
     */
    public function index()
    {
        return response()->json([
            'data' => Device::with(['logs' => function ($q) {
                $q->latest('logged_at')->limit(5);
            }])->get()->map(function ($device) {
                return [
                    'id' => $device->id,
                    'device_id' => $device->device_id,
                    'name' => $device->name,
                    'location' => $device->location,
                    'type' => $device->device_type,
                    'status' => $device->status,
                    'is_online' => $device->isOnline(),
                    'last_sync' => $device->last_sync_at?->diffForHumans(),
                    'active_users_count' => $device->users()->active()->count(),
                ];
            }),
        ]);
    }

    /**
     * Get device details
     */
    public function show(Device $device)
    {
        return response()->json([
            'device' => [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'name' => $device->name,
                'location' => $device->location,
                'type' => $device->device_type,
                'status' => $device->status,
                'ip_address' => $device->ip_address,
                'firmware_version' => $device->firmware_version,
                'is_online' => $device->isOnline(),
                'last_sync' => $device->last_sync_at,
                'recent_logs' => $device->logs()
                    ->latest('logged_at')
                    ->limit(10)
                    ->get()
                    ->map(fn ($log) => [
                        'id' => $log->id,
                        'event_type' => $log->event_type,
                        'status' => $log->status,
                        'logged_at' => $log->logged_at,
                    ]),
            ],
        ]);
    }

    /**
     * Create device
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|unique:devices|string',
            'name' => 'required|string',
            'location' => 'required|string',
            'device_type' => 'required|in:gate,turnstile,scanner',
            'ip_address' => 'required|ip',
        ]);

        $device = Device::create($validated);

        return response()->json([
            'message' => 'Device created successfully',
            'device' => $device,
        ], 201);
    }

    /**
     * Update device
     */
    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'location' => 'sometimes|string',
            'status' => 'sometimes|in:active,inactive,maintenance',
            'ip_address' => 'sometimes|ip',
            'firmware_version' => 'sometimes|string',
        ]);

        $device->update($validated);

        return response()->json([
            'message' => 'Device updated successfully',
            'device' => $device,
        ]);
    }

    /**
     * Delete device
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return response()->json([
            'message' => 'Device deleted successfully',
        ]);
    }

    /**
     * Get device logs
     */
    public function getLogs(Device $device)
    {
        return response()->json([
            'device_id' => $device->id,
            'logs' => $device->logs()
                ->latest('logged_at')
                ->paginate(20)
                ->through(fn ($log) => [
                    'id' => $log->id,
                    'event_type' => $log->event_type,
                    'status' => $log->status,
                    'logged_at' => $log->logged_at,
                    'error_message' => $log->error_message,
                ]),
        ]);
    }

    /**
     * Sync device with server
     */
    public function sync(Device $device)
    {
        try {
            $success = $this->deviceSyncService->pullDeviceLogs($device);

            if (!$success) {
                return response()->json([
                    'message' => 'Failed to sync with device',
                    'status' => false,
                ], 400);
            }

            return response()->json([
                'message' => 'Device synced successfully',
                'status' => true,
                'last_sync' => $device->fresh()->last_sync_at,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sync error: ' . $e->getMessage(),
                'status' => false,
            ], 500);
        }
    }
}
