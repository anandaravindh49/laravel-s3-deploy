<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicRequest;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(
        private ApprovalService $approvalService
    ) {}

    /**
     * Approve public request
     */
    public function approve(Request $request, PublicRequest $publicRequest)
    {
        if (!$publicRequest->canBeApproved()) {
            return response()->json([
                'message' => 'Request cannot be approved',
            ], 400);
        }

        try {
            $approvedVisitor = $this->approvalService->approveRequest(
                $publicRequest,
                $request->user()?->id
            );

            return response()->json([
                'message' => 'Request approved successfully',
                'approved_visitor' => [
                    'id' => $approvedVisitor->id,
                    'visitor_name' => $approvedVisitor->visitor_name,
                    'badge_number' => $approvedVisitor->badge_number,
                    'visit_date' => $approvedVisitor->visit_date,
                    'status' => $approvedVisitor->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Approval failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject public request
     */
    public function reject(Request $request, PublicRequest $publicRequest)
    {
        if (!$publicRequest->canBeRejected()) {
            return response()->json([
                'message' => 'Request cannot be rejected',
            ], 400);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->approvalService->rejectRequest(
                $publicRequest,
                $validated['reason'] ?? null,
                $request->user()?->id
            );

            return response()->json([
                'message' => 'Request rejected successfully',
                'request_id' => $publicRequest->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Rejection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Revoke visitor access
     */
    public function revoke(Request $request, PublicRequest $publicRequest)
    {
        if (!$publicRequest->approvedVisitor) {
            return response()->json([
                'message' => 'No approved visitor found',
            ], 404);
        }

        try {
            $this->approvalService->revokeAccess($publicRequest->approvedVisitor);

            return response()->json([
                'message' => 'Access revoked successfully',
                'visitor_id' => $publicRequest->approvedVisitor->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Revocation failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
