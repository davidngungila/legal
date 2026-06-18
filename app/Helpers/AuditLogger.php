<?php

namespace App\Helpers;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(
        string $event,
        ?Model $auditable = null,
        ?string $module = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): Audit {
        return Audit::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable ? $auditable->id : null,
            'module' => $module,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'description' => $description,
            'client_id' => session('current_client_id'),
        ]);
    }
}