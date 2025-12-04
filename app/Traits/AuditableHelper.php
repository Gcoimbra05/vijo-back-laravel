<?php

namespace App\Traits;

trait AuditableHelper
{
    /**
     * Get formatted audit logs for a model instance
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFormattedAudits()
    {
        return $this->audits->map(function ($audit) {
            return [
                'id' => $audit->id,
                'event' => ucfirst($audit->event),
                'event_class' => $this->getEventBadgeClass($audit->event),
                'user' => $audit->user ? $audit->user->first_name . ' ' . $audit->user->last_name : 'System',
                'user_email' => $audit->user ? $audit->user->email : null,
                'old_values' => $audit->old_values,
                'new_values' => $audit->new_values,
                'ip_address' => $audit->ip_address,
                'user_agent' => $audit->user_agent,
                'url' => $audit->url,
                'created_at' => $audit->created_at,
                'formatted_date' => $audit->created_at->format('Y-m-d H:i:s'),
                'human_date' => $audit->created_at->diffForHumans(),
            ];
        });
    }

    /**
     * Get the Bootstrap badge class for an audit event
     *
     * @param string $event
     * @return string
     */
    protected function getEventBadgeClass($event)
    {
        return match($event) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'restored' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get audit summary statistics
     *
     * @return array
     */
    public function getAuditStats()
    {
        $audits = $this->audits;

        return [
            'total' => $audits->count(),
            'created' => $audits->where('event', 'created')->count(),
            'updated' => $audits->where('event', 'updated')->count(),
            'deleted' => $audits->where('event', 'deleted')->count(),
            'last_activity' => $audits->first() ? $audits->first()->created_at : null,
        ];
    }

    /**
     * Get changes between old and new values
     *
     * @param object $audit
     * @return array
     */
    public static function getChanges($audit)
    {
        $changes = [];
        $oldValues = $audit->old_values ? json_decode($audit->old_values, true) : [];
        $newValues = $audit->new_values ? json_decode($audit->new_values, true) : [];

        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allKeys as $key) {
            $oldValue = $oldValues[$key] ?? null;
            $newValue = $newValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Get audit logs with user information
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuditsWithUser($limit = 50)
    {
        return $this->audits()
            ->with('user:id,first_name,last_name,email')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for a specific date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuditsByDateRange($startDate, $endDate)
    {
        return $this->audits()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user:id,first_name,last_name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get audit logs by event type
     *
     * @param string $event
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuditsByEvent($event)
    {
        return $this->audits()
            ->where('event', $event)
            ->with('user:id,first_name,last_name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
