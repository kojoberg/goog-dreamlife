<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created(Model $model)
    {
        $this->logActivity($model, 'Created', null, $model->toArray());
    }

    public function updated(Model $model)
    {
        $old = $model->getOriginal();
        $new = $model->getChanges();

        // Exclude 'updated_at' if it's the only change
        if (count($new) === 1 && isset($new['updated_at'])) {
            return;
        }

        $this->logActivity($model, 'Updated', $old, $new);
    }

    public function deleted(Model $model)
    {
        $this->logActivity($model, 'Deleted', $model->toArray(), null);
    }

    protected function logActivity(Model $model, $action, $oldValues = null, $newValues = null)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'table_name' => $model->getTable(),
                'record_id' => $model->getKey(),
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => request()->ip(),
            ]);
        }
    }
}
