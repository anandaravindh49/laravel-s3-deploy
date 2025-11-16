<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;

class AuditService
{
    /**
     * Get audit logs with filtering
     */
    public function getLogs(array $filters = [])
    {
        $query = AuditLog::query();

        if ($filters['module'] ?? null) {
            $query->forModule($filters['module']);
        }

        if ($filters['action'] ?? null) {
            $query->forAction($filters['action']);
        }

        if ($filters['user_id'] ?? null) {
            $query->forUser($filters['user_id']);
        }

        if ($filters['start_date'] ?? null) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if ($filters['end_date'] ?? null) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        return $query->recent()->paginate($filters['per_page'] ?? 50);
    }

    /**
     * Get audit history for specific resource
     */
    public function getResourceHistory($type, $id)
    {
        return AuditLog::where('auditable_type', $type)
            ->where('auditable_id', $id)
            ->recent()
            ->get();
    }

    /**
     * Get changes summary
     */
    public function getChangesSummary(AuditLog $log): array
    {
        return [
            'module' => $log->module,
            'action' => $log->action,
            'user' => $log->user?->name ?? 'System',
            'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
            'changes' => $log->getChanges(),
        ];
    }

    /**
     * Get activity report
     */
    public function getActivityReport(array $filters = [])
    {
        $logs = $this->getLogs($filters);

        return [
            'total' => $logs->total(),
            'by_module' => $this->getCountByModule($filters),
            'by_action' => $this->getCountByAction($filters),
            'by_user' => $this->getCountByUser($filters),
        ];
    }

    /**
     * Count logs by module
     */
    private function getCountByModule(array $filters)
    {
        return AuditLog::query()
            ->when($filters['start_date'] ?? null, fn ($q) => $q->where('created_at', '>=', $filters['start_date']))
            ->when($filters['end_date'] ?? null, fn ($q) => $q->where('created_at', '<=', $filters['end_date']))
            ->groupBy('module')
            ->selectRaw('module, count(*) as count')
            ->pluck('count', 'module')
            ->toArray();
    }

    /**
     * Count logs by action
     */
    private function getCountByAction(array $filters)
    {
        return AuditLog::query()
            ->when($filters['start_date'] ?? null, fn ($q) => $q->where('created_at', '>=', $filters['start_date']))
            ->when($filters['end_date'] ?? null, fn ($q) => $q->where('created_at', '<=', $filters['end_date']))
            ->groupBy('action')
            ->selectRaw('action, count(*) as count')
            ->pluck('count', 'action')
            ->toArray();
    }

    /**
     * Count logs by user
     */
    private function getCountByUser(array $filters)
    {
        return AuditLog::query()
            ->when($filters['start_date'] ?? null, fn ($q) => $q->where('created_at', '>=', $filters['start_date']))
            ->when($filters['end_date'] ?? null, fn ($q) => $q->where('created_at', '<=', $filters['end_date']))
            ->with('user')
            ->groupBy('user_id')
            ->selectRaw('user_id, count(*) as count')
            ->pluck('count', 'user_id')
            ->toArray();
    }
}
