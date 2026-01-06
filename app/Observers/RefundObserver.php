<?php

namespace App\Observers;

use App\Models\CashFlow;
use App\Models\Refund;

class RefundObserver
{
    /**
     * Handle the Refund "created" event.
     */
    public function created(Refund $refund): void
    {
        // Create expense cash flow for refund
        CashFlow::create([
            'type' => 'expense',
            'category' => CashFlow::CATEGORY_REFUND,
            'amount' => $refund->total_refund,
            'description' => 'Refund ' . $refund->refund_number,
            'notes' => 'Reason: ' . ($refund->reason ?? '-') . ($refund->notes ? ', Notes: ' . $refund->notes : ''),
            'user_id' => $refund->user_id,
            'refund_id' => $refund->id,
            'sale_id' => $refund->sale_id,
            'transaction_date' => $refund->created_at->toDateString(),
        ]);
    }

    /**
     * Handle the Refund "updated" event.
     */
    public function updated(Refund $refund): void
    {
        // Update cash flow amount if refund amount changed
        if ($refund->isDirty('total_refund')) {
            CashFlow::where('refund_id', $refund->id)->update([
                'amount' => $refund->total_refund,
            ]);
        }
    }

    /**
     * Handle the Refund "deleted" event.
     */
    public function deleted(Refund $refund): void
    {
        // Remove associated cash flow when refund is deleted
        CashFlow::where('refund_id', $refund->id)->delete();
    }

    /**
     * Handle the Refund "restored" event.
     */
    public function restored(Refund $refund): void
    {
        //
    }

    /**
     * Handle the Refund "force deleted" event.
     */
    public function forceDeleted(Refund $refund): void
    {
        CashFlow::where('refund_id', $refund->id)->delete();
    }
}
