<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\User;

class ActivityLogService
{
    public function log(User $user, string $action, ?Document $document = null, ?array $detail = null): void
    {
        ActivityLog::create([
            'user_id'     => $user->id,
            'document_id' => $document?->id,
            'action'      => $action,
            'detail'      => $detail ? json_encode($detail) : null,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
