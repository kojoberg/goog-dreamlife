<?php

namespace App\Services;

use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\SaleItem;

class InventoryService
{
    /**
     * Deduct stock using FIFO (First-In, First-Out) logic based on batch expiry.
     * 
     * @param Product $product
     * @param int $quantity
     * @param mixed $relatedModel The Sale or Prescription to link the deduction to (optional, usually handled by caller creating items)
     * @return array List of deductions made [['batch_id' => x, 'quantity' => y], ...]
     * @throws \Exception
     */
    public function deductStock(Product $product, int $quantity)
    {
        if ($product->product_type === 'service') {
            return []; // No stock deduction for services
        }

        // 1. Get Batches
        $batches = InventoryBatch::where('product_id', $product->id)
            ->where('quantity', '>', 0)
            ->where('expiry_date', '>=', now()) // Don't sell expired
            ->orderBy('expiry_date', 'asc')
            ->get();

        $qtyToFulfill = $quantity;
        $deductions = [];

        foreach ($batches as $batch) {
            if ($qtyToFulfill <= 0)
                break;

            if ($batch->quantity >= $qtyToFulfill) {
                // Batch has enough
                $batch->decrement('quantity', $qtyToFulfill);

                $deductions[] = [
                    'batch_id' => $batch->id,
                    'quantity' => $qtyToFulfill
                ];

                $qtyToFulfill = 0;
            } else {
                // Partial take from this batch
                $taken = $batch->quantity;
                $batch->update(['quantity' => 0]);

                $deductions[] = [
                    'batch_id' => $batch->id,
                    'quantity' => $taken
                ];

                $qtyToFulfill -= $taken;
            }
        }

        if ($qtyToFulfill > 0) {
            throw new \Exception("Insufficient stock for product: " . $product->name . ". Short by: $qtyToFulfill");
        }

        return $deductions;
    }

    /**
     * Check if enough stock exists without deducting.
     */
    public function checkStock(Product $product, int $quantity): bool
    {
        if ($product->product_type === 'service')
            return true;

        if ($product->stock >= $quantity)
            return true;

        return false;
    }

    /**
     * Restock items from a refund.
     *
     * @param \App\Models\Sale $sale
     * @return void
     */
    public function restock(\App\Models\Sale $sale)
    {
        foreach ($sale->items as $item) {
            $product = $item->product;

            if ($product->product_type === 'service') {
                continue;
            }

            // Restore to Batch if possible
            if ($item->batch_id) {
                // Determine branch context from global scope or sale user?
                // Batches usually scoped by branch.
                // We'll trust the ID is unique enough or HasBranchScope handles retrieval if we are in context.
                // However, HasBranchScope might block finding it if we are Admin (assuming Admin sees all?).
                // Let's use withoutGlobalScopes to find the exact batch ID to be safe.

                $batch = \App\Models\InventoryBatch::withoutGlobalScopes()->find($item->batch_id);

                if ($batch) {
                    $batch->increment('quantity', $item->quantity);
                } else {
                    // Batch deleted?
                    // Implementation choice: Create a new batch or just log?
                    // For now, let's log logic here.
                    \Illuminate\Support\Facades\Log::warning("Refund Restock: Batch {$item->batch_id} not found for Product {$product->name}");
                }
            } else {
                // Logic for items without batch (if any strictness missed)
                \Illuminate\Support\Facades\Log::warning("Refund Restock: No batch ID for item {$item->id}");
            }
        }
    }
}
