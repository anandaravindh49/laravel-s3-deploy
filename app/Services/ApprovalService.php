<?php

namespace App\Services;

use App\Models\PublicRequest;
use App\Models\ApprovedVisitor;
use App\Models\DeviceUser;
use App\Models\Device;
use App\Events\PublicRequestApproved;
use App\Events\PublicRequestRejected;
use Illuminate\Support\Str;

class ApprovalService
{
    public function __construct(
        private DeviceSyncService $deviceSyncService
    ) {}

    /**
     * Approve a public request
     */
    public function approveRequest(PublicRequest $request, ?int $userId = null): ApprovedVisitor
    {
        // Create approved visitor record
        $approvedVisitor = ApprovedVisitor::create([
            'public_request_id' => $request->id,
            'visitor_name' => $request->visitor_name,
            'visitor_phone' => $request->visitor_phone,
            'id_proof_type' => $request->id_proof_type,
            'id_proof_number' => $request->id_proof_number,
            'id_proof_image' => $request->id_proof_image,
            'purpose_of_visit' => $request->purpose_of_visit,
            'visit_date' => $request->visit_date,
            'visit_time' => $request->visit_time,
            'host_department' => $request->host_department,
            'host_person_name' => $request->host_person_name,
            'badge_number' => 'VMS-' . Str::random(8),
            'status' => 'pending',
            'metadata' => [
                'access_level' => 'visitor',
                'entry_point' => 'main_gate',
            ],
        ]);

        // Update request status
        $request->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        // Add to device users and sync
        $this->addVisitorToDevices($approvedVisitor);

        // Dispatch event for audit logging
        PublicRequestApproved::dispatch($request, $userId);

        return $approvedVisitor;
    }

    /**
     * Reject a public request
     */
    public function rejectRequest(PublicRequest $request, ?string $reason = null, ?int $userId = null): void
    {
        $request->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        // Dispatch event for audit logging
        PublicRequestRejected::dispatch($request, $reason, $userId);
    }

    /**
     * Add visitor to all active devices
     */
    private function addVisitorToDevices(ApprovedVisitor $visitor): void
    {
        $devices = Device::active()->get();

        foreach ($devices as $device) {
            DeviceUser::firstOrCreate(
                [
                    'device_id' => $device->id,
                    'external_user_id' => "VMS-{$visitor->id}",
                ],
                [
                    'name' => $visitor->visitor_name,
                    'id_proof' => $visitor->id_proof_type,
                    'access_level' => $visitor->metadata['access_level'] ?? 'visitor',
                    'status' => 'active',
                    'valid_from' => $visitor->visit_date,
                    'valid_until' => $visitor->visit_date,
                ]
            );

            // Sync updated user list to device
            $this->deviceSyncService->syncApprovedUsersToDevice($device);
        }
    }

    /**
     * Revoke visitor access
     */
    public function revokeAccess(ApprovedVisitor $visitor): void
    {
        // Revoke from all devices
        DeviceUser::where('external_user_id', "VMS-{$visitor->id}")
            ->update(['status' => 'revoked']);

        $visitor->update(['status' => 'revoked']);

        // Sync all devices
        Device::active()->each(
            fn ($device) => $this->deviceSyncService->syncApprovedUsersToDevice($device)
        );
    }
}
