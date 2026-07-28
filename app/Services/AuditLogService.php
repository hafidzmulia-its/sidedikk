<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(
        ?User $actor,
        string $action,
        mixed $subject = null,
        array $metadata = [],
        ?string $ipAddress = null,
    ): AuditLog {
        return AuditLog::query()->create([
            'actor_id' => $actor?->id,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->id,
            'safe_metadata' => $metadata,
            'ip_hash' => $ipAddress ? hash('sha256', $ipAddress) : null,
            'created_at' => now(),
        ]);
    }
}
