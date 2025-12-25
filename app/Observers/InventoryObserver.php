<?php

namespace App\Observers;

use App\Models\InventoryBatch;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class InventoryObserver
{
    /**
     * Handle the InventoryBatch "created" event.
     */
    public function created(InventoryBatch $inventoryBatch): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATED',
            'table_name' => 'inventory_batches',
            'record_id' => $inventoryBatch->id,
            'new_values' => $inventoryBatch->toArray(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the InventoryBatch "updated" event.
     */
    public function updated(InventoryBatch $inventoryBatch): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATED',
            'table_name' => 'inventory_batches',
            'record_id' => $inventoryBatch->id,
            'old_values' => $inventoryBatch->getOriginal(),
            'new_values' => $inventoryBatch->getChanges(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the InventoryBatch "deleted" event.
     */
    public function deleted(InventoryBatch $inventoryBatch): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETED',
            'table_name' => 'inventory_batches',
            'record_id' => $inventoryBatch->id,
            'old_values' => $inventoryBatch->toArray(),
            'ip_address' => request()->ip(),
        ]);
    }
}
