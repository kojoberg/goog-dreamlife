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

        // Check for Low Stock Logic
        // We need to check TOTAL stock for the product, not just this batch
        $product = $inventoryBatch->product;

        // Helper to get total stock
        $totalStock = $product->batches()->where('quantity', '>', 0)->sum('quantity');

        if ($totalStock <= $product->reorder_level) {
            $settings = \App\Models\Setting::first();
            $alertMessage = "Low Stock Alert: {$product->name} is down to {$totalStock} units (Level: {$product->reorder_level}).";

            // EMAIL ALERT
            if ($settings && $settings->notify_low_stock_email && $settings->email) {
                // Ideally Queue Job, but direct mail for simplicity (or use Notification class)
                try {
                    \Illuminate\Support\Facades\Mail::raw($alertMessage, function ($msg) use ($settings) {
                        $msg->to($settings->email)->subject('Low Stock Alert');
                    });
                    // Log successful email
                    \App\Models\CommunicationLog::create([
                        'type' => 'email',
                        'recipient' => $settings->email,
                        'message' => $alertMessage,
                        'status' => 'sent',
                        'context' => 'low_stock_alert',
                        'user_id' => Auth::id(),
                        'branch_id' => Auth::user()?->branch_id,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Low Stock Email Failed: " . $e->getMessage());
                    // Log failed email
                    \App\Models\CommunicationLog::create([
                        'type' => 'email',
                        'recipient' => $settings->email,
                        'message' => $alertMessage,
                        'status' => 'failed',
                        'response' => $e->getMessage(),
                        'context' => 'low_stock_alert',
                        'user_id' => Auth::id(),
                        'branch_id' => Auth::user()?->branch_id,
                    ]);
                }
            }

            // SMS ALERT
            if ($settings && $settings->notify_low_stock_sms && $settings->phone) {
                try {
                    $sms = new \App\Services\SmsService();
                    $sms->sendQuickSms($settings->phone, "ALERT: Low Stock for {$product->name}. Remaining: {$totalStock}", 'low_stock_alert');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Low Stock SMS Failed: " . $e->getMessage());
                }
            }
        }
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
