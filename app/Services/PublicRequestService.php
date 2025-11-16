<?php

namespace App\Services;

use App\Models\PublicRequest;
use App\Models\ApprovedVisitor;
use App\Models\DeviceUser;
use App\Models\Device;
use Illuminate\Support\Str;

class PublicRequestService
{
    public function __construct(
        private ApprovalService $approvalService,
        private DeviceSyncService $deviceSyncService
    ) {}

    /**
     * Create a public request from visitor form
     */
    public function createRequest(array $data): PublicRequest
    {
        return PublicRequest::create([
            'visitor_name' => $data['visitor_name'],
            'visitor_email' => $data['visitor_email'] ?? null,
            'visitor_phone' => $data['visitor_phone'],
            'id_proof_type' => $data['id_proof_type'] ?? null,
            'id_proof_number' => $data['id_proof_number'] ?? null,
            'id_proof_image' => $data['id_proof_image'] ?? null,
            'purpose_of_visit' => $data['purpose_of_visit'],
            'visit_date' => $data['visit_date'],
            'visit_time' => $data['visit_time'] ?? null,
            'host_department' => $data['host_department'],
            'host_person_name' => $data['host_person_name'] ?? null,
            'additional_notes' => $data['additional_notes'] ?? null,
        ]);
    }

    /**
     * Get pending requests
     */
    public function getPendingRequests()
    {
        return PublicRequest::pending()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    /**
     * Get request details with related data
     */
    public function getRequestDetails(PublicRequest $request)
    {
        return [
            'request' => $request,
            'approved_visitor' => $request->approvedVisitor,
            'approved_by' => $request->approvedBy,
        ];
    }
}
