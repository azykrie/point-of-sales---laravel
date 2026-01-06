<?php

namespace App\Observers;

use App\Models\CashFlow;
use App\Models\Sale;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        // Only create cash flow for completed sales
        if ($sale->status === 'completed') {
            CashFlow::create([
                'type' => 'income',
                'category' => CashFlow::CATEGORY_SALES,
                'amount' => $sale->total_amount,
                'description' => 'Sale ' . $sale->invoice_number,
                'notes' => 'Customer: ' . ($sale->customer_name ?? 'Walk-in') . ', Method: ' . strtoupper($sale->payment_method),
                'user_id' => $sale->cashier_id,
                'sale_id' => $sale->id,
                'transaction_date' => $sale->created_at->toDateString(),
            ]);
        }
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        // If sale status changed to completed, create cash flow
        if ($sale->isDirty('status') && $sale->status === 'completed') {
            // Check if cash flow already exists
            $exists = CashFlow::where('sale_id', $sale->id)->exists();
            if (!$exists) {
                CashFlow::create([
                    'type' => 'income',
                    'category' => CashFlow::CATEGORY_SALES,
                    'amount' => $sale->total_amount,
                    'description' => 'Sale ' . $sale->invoice_number,
                    'notes' => 'Customer: ' . ($sale->customer_name ?? 'Walk-in') . ', Method: ' . strtoupper($sale->payment_method),
                    'user_id' => $sale->cashier_id,
                    'sale_id' => $sale->id,
                    'transaction_date' => $sale->created_at->toDateString(),
                ]);
            }
        }
        
        // If sale status changed to cancelled, remove cash flow
        if ($sale->isDirty('status') && $sale->status === 'cancelled') {
            CashFlow::where('sale_id', $sale->id)->delete();
        }
    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        // Remove associated cash flow when sale is deleted
        CashFlow::where('sale_id', $sale->id)->delete();
    }

    /**
     * Handle the Sale "restored" event.
     */
    public function restored(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "force deleted" event.
     */
    public function forceDeleted(Sale $sale): void
    {
        CashFlow::where('sale_id', $sale->id)->delete();
    }
}
