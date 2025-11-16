<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovedVisitor;
use App\Models\Device;
use App\Services\GateService;
use Illuminate\Http\Request;

class GateController extends Controller
{
    public function __construct(
        private GateService $gateService
    ) {}

    /**
     * Visitor check-in
     */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'badge_number' => 'required|string',
            'device_id' => 'required|exists:devices,id',
        ]);

        try {
            $visitor = ApprovedVisitor::where('badge_number', $validated['badge_number'])->first();

            if (!$visitor) {
                return response()->json([
                    'message' => 'Visitor not found',
                    'status' => 'denied',
                ], 404);
            }

            if ($visitor->status === 'expired' || $visitor->visit_date < now()->toDateString()) {
                return response()->json([
                    'message' => 'Visitor pass expired',
                    'status' => 'denied',
                ], 403);
            }

            if ($visitor->hasCheckedIn() && !$visitor->hasCheckedOut()) {
                return response()->json([
                    'message' => 'Visitor already checked in',
                    'status' => 'denied',
                ], 400);
            }

            $device = Device::find($validated['device_id']);
            $log = $this->gateService->checkIn($visitor, $device, $request->all());

            return response()->json([
                'message' => 'Check-in successful',
                'status' => 'success',
                'visitor' => [
                    'id' => $visitor->id,
                    'name' => $visitor->visitor_name,
                    'badge_number' => $visitor->badge_number,
                    'purpose' => $visitor->purpose_of_visit,
                ],
                'log_id' => $log->id,
                'checked_in_at' => $log->event_at,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Check-in error: ' . $e->getMessage(),
                'status' => 'error',
            ], 500);
        }
    }

    /**
     * Visitor check-out
     */
    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'badge_number' => 'required|string',
            'device_id' => 'required|exists:devices,id',
        ]);

        try {
            $visitor = ApprovedVisitor::where('badge_number', $validated['badge_number'])->first();

            if (!$visitor) {
                return response()->json([
                    'message' => 'Visitor not found',
                    'status' => 'denied',
                ], 404);
            }

            if (!$visitor->hasCheckedIn()) {
                return response()->json([
                    'message' => 'Visitor has not checked in',
                    'status' => 'denied',
                ], 400);
            }

            $device = Device::find($validated['device_id']);
            $log = $this->gateService->checkOut($visitor, $device, $request->all());

            return response()->json([
                'message' => 'Check-out successful',
                'status' => 'success',
                'visitor' => [
                    'id' => $visitor->id,
                    'name' => $visitor->visitor_name,
                    'badge_number' => $visitor->badge_number,
                ],
                'log_id' => $log->id,
                'checked_out_at' => $log->event_at,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Check-out error: ' . $e->getMessage(),
                'status' => 'error',
            ], 500);
        }
    }

    /**
     * Get visitor history
     */
    public function getHistory(ApprovedVisitor $visitor)
    {
        return response()->json([
            'visitor' => [
                'id' => $visitor->id,
                'name' => $visitor->visitor_name,
                'badge_number' => $visitor->badge_number,
            ],
            'history' => $this->gateService->getVisitorHistory($visitor)
                ->map(fn ($log) => [
                    'id' => $log->id,
                    'event_type' => $log->event_type,
                    'device_name' => $log->device->name,
                    'event_at' => $log->event_at->format('Y-m-d H:i:s'),
                ]),
        ]);
    }

    /**
     * Get active visitors on device
     */
    public function getActiveVisitors(Device $device)
    {
        return response()->json([
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'location' => $device->location,
            ],
            'active_visitors' => $this->gateService->getActiveVisitorsOnDevice($device)
                ->map(fn ($log) => [
                    'visitor_id' => $log->approvedVisitor->id,
                    'visitor_name' => $log->approvedVisitor->visitor_name,
                    'badge_number' => $log->approvedVisitor->badge_number,
                    'checked_in_at' => $log->event_at->format('Y-m-d H:i:s'),
                ]),
        ]);
    }
}
