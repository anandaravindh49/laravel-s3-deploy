<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct(
        private AuditService $auditService
    ) {}

    /**
     * Get audit logs with filters
     */
    public function index(Request $request)
    {
        $filters = $request->only(['module', 'action', 'user_id', 'start_date', 'end_date', 'per_page']);

        $logs = $this->auditService->getLogs($filters);

        return response()->json([
            'logs' => $logs->through(fn ($log) => [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'System',
                'module' => $log->module,
                'action' => $log->action,
                'auditable' => [
                    'type' => class_basename($log->auditable_type),
                    'id' => $log->auditable_id,
                ],
                'changes' => $log->getChanges(),
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
            ]),
            'pagination' => [
                'total' => $logs->total(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
            ],
        ]);
    }

    /**
     * Get audit report
     */
    public function report(Request $request)
    {
        $filters = $request->only(['module', 'action', 'user_id', 'start_date', 'end_date']);

        $report = $this->auditService->getActivityReport($filters);

        return response()->json([
            'report' => $report,
        ]);
    }

    /**
     * Get resource audit history
     */
    public function resourceHistory(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'id' => 'required|numeric',
        ]);

        $history = $this->auditService->getResourceHistory(
            $validated['type'],
            $validated['id']
        );

        return response()->json([
            'type' => $validated['type'],
            'id' => $validated['id'],
            'history' => $history->map(fn ($log) => [
                'id' => $log->id,
                'action' => $log->action,
                'user' => $log->user?->name ?? 'System',
                'changes' => $log->getChanges(),
                'created_at' => $log->created_at,
            ]),
        ]);
    }
}
