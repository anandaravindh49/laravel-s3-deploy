<?php

namespace App\Services;

use App\Models\ApprovedVisitor;
use App\Models\GateLog;
use App\Models\Device;
use App\Events\VisitorCheckedIn;
use App\Events\VisitorCheckedOut;

class GateService
{
    /**
     * Process visitor check-in
     */
    public function checkIn(ApprovedVisitor $visitor, Device $device, ?array $metadata = null): GateLog
    {
        $log = GateLog::create([
            'approved_visitor_id' => $visitor->id,
            'device_id' => $device->id,
            'visitor_name' => $visitor->visitor_name,
            'badge_number' => $visitor->badge_number,
            'event_type' => 'checkin',
            'event_at' => now(),
            'ip_address' => request()->ip(),
            'metadata' => $metadata,
        ]);

        // Update visitor status
        if ($visitor->status !== 'active') {
            $visitor->update(['status' => 'active']);
        }

        // Dispatch event
        VisitorCheckedIn::dispatch($visitor, $device->id, $metadata);

        return $log;
    }

    /**
     * Process visitor check-out
     */
    public function checkOut(ApprovedVisitor $visitor, Device $device, ?array $metadata = null): GateLog
    {
        $log = GateLog::create([
            'approved_visitor_id' => $visitor->id,
            'device_id' => $device->id,
            'visitor_name' => $visitor->visitor_name,
            'badge_number' => $visitor->badge_number,
            'event_type' => 'checkout',
            'event_at' => now(),
            'ip_address' => request()->ip(),
            'metadata' => $metadata,
        ]);

        // Update visitor status
        $visitor->update(['status' => 'checkout']);

        // Dispatch event
        VisitorCheckedOut::dispatch($visitor, $device->id, $metadata);

        return $log;
    }

    /**
     * Get visitor history
     */
    public function getVisitorHistory(ApprovedVisitor $visitor)
    {
        return $visitor->gateLogs()
            ->with('device')
            ->latest('event_at')
            ->get();
    }

    /**
     * Get active visitors on device
     */
    public function getActiveVisitorsOnDevice(Device $device)
    {
        return GateLog::where('device_id', $device->id)
            ->where('event_type', 'checkin')
            ->whereNotIn('approved_visitor_id', function ($query) {
                $query->select('approved_visitor_id')
                    ->from('gate_logs')
                    ->where('event_type', 'checkout');
            })
            ->with('approvedVisitor')
            ->latest('event_at')
            ->get();
    }

    /**
     * Auto check-out expired visitors
     */
    public function autoCheckoutExpiredVisitors(): int
    {
        $today = now()->toDateString();
        $expiredVisitors = ApprovedVisitor::where('visit_date', '<', $today)
            ->where('status', '!=', 'checkout')
            ->whereDoesntHave('gateLogs', function ($query) {
                $query->where('event_type', 'checkout')
                    ->where('event_at', '>', now()->subDays(1));
            })
            ->get();

        foreach ($expiredVisitors as $visitor) {
            // Get the device used for check-in
            $checkinLog = $visitor->gateLogs()
                ->where('event_type', 'checkin')
                ->latest('event_at')
                ->first();

            if ($checkinLog) {
                $this->checkOut($visitor, $checkinLog->device);
            }
        }

        return $expiredVisitors->count();
    }
}
