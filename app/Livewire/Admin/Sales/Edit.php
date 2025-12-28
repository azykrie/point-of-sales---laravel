<?php

namespace App\Livewire\Admin\Sales;

use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Sale / Refund')]
class Edit extends Component
{
    public Sale $sale;
    public $customerName;
    public $notes;
    public $status;
    public $refundItems = [];
    public $refundReason = '';

    public function mount($id)
    {
        $this->sale = Sale::with(['items.product', 'refunds.items'])->findOrFail($id);
        $this->customerName = $this->sale->customer_name;
        $this->notes = $this->sale->notes;
        $this->status = $this->sale->status;
        
        // Initialize refund items array
        foreach ($this->sale->items as $item) {
            $this->refundItems[$item->id] = [
                'refund' => false,
                'quantity' => $item->quantity,
            ];
        }
    }

    public function updateSale()
    {
        $this->sale->update([
            'customer_name' => $this->customerName,
            'notes' => $this->notes,
            'status' => $this->status,
        ]);

        Flux::toast('Sale updated successfully!', variant: 'success');
    }

    public function processRefund()
    {
        if (empty($this->refundReason)) {
            Flux::toast('Please enter a refund reason!', variant: 'danger');
            return;
        }

        $hasRefund = false;
        $totalRefund = 0;
        $refundItemsData = [];

        try {
            DB::beginTransaction();

            foreach ($this->refundItems as $itemId => $refundData) {
                if ($refundData['refund'] && $refundData['quantity'] > 0) {
                    $hasRefund = true;
                    $item = SaleItem::with('product')->find($itemId);
                    
                    if (!$item) continue;

                    $refundQty = min($refundData['quantity'], $item->quantity);
                    $refundAmount = $refundQty * $item->price;
                    $totalRefund += $refundAmount;

                    // Store refund item data
                    $refundItemsData[] = [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'quantity' => $refundQty,
                        'price' => $item->price,
                        'subtotal' => $refundAmount,
                    ];

                    // Restore stock
                    $item->product->increment('stock', $refundQty);

                    if ($refundQty >= $item->quantity) {
                        // Full refund - delete item
                        $item->delete();
                    } else {
                        // Partial refund - update quantity
                        $item->update([
                            'quantity' => $item->quantity - $refundQty,
                            'subtotal' => ($item->quantity - $refundQty) * $item->price,
                        ]);
                    }
                }
            }

            if (!$hasRefund) {
                Flux::toast('Please select items to refund!', variant: 'danger');
                DB::rollBack();
                return;
            }

            // Create refund record
            $refund = Refund::create([
                'sale_id' => $this->sale->id,
                'user_id' => auth()->id(),
                'total_refund' => $totalRefund,
                'reason' => $this->refundReason,
            ]);

            // Create refund items
            foreach ($refundItemsData as $itemData) {
                RefundItem::create([
                    'refund_id' => $refund->id,
                    ...$itemData,
                ]);
            }

            // Update sale total
            $this->sale->refresh();
            $newTotal = $this->sale->items->sum('subtotal');
            
            $this->sale->update([
                'total_amount' => $newTotal,
                'status' => $newTotal <= 0 ? 'cancelled' : $this->sale->status,
            ]);

            DB::commit();

            Flux::toast('Refund processed! Refund #' . $refund->refund_number . ' - Total: Rp ' . number_format($totalRefund, 0, ',', '.'), variant: 'success');
            
            // Refresh the sale data
            $this->sale = Sale::with(['items.product', 'refunds.items'])->findOrFail($this->sale->id);
            $this->refundItems = [];
            foreach ($this->sale->items as $item) {
                $this->refundItems[$item->id] = [
                    'refund' => false,
                    'quantity' => $item->quantity,
                ];
            }
            $this->refundReason = '';

        } catch (\Exception $e) {
            DB::rollBack();
            Flux::toast('Error: ' . $e->getMessage(), variant: 'danger');
        }
    }

    public function cancelSale()
    {
        try {
            DB::beginTransaction();

            $totalRefund = 0;
            $refundItemsData = [];

            // Restore all stock and prepare refund data
            foreach ($this->sale->items as $item) {
                $item->product->increment('stock', $item->quantity);
                
                $refundItemsData[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                ];
                
                $totalRefund += $item->subtotal;
            }

            // Create refund record for cancellation
            $refund = Refund::create([
                'sale_id' => $this->sale->id,
                'user_id' => auth()->id(),
                'total_refund' => $totalRefund,
                'reason' => 'Sale cancelled',
                'notes' => 'Full cancellation of sale',
            ]);

            // Create refund items
            foreach ($refundItemsData as $itemData) {
                RefundItem::create([
                    'refund_id' => $refund->id,
                    ...$itemData,
                ]);
            }

            $this->sale->update([
                'status' => 'cancelled',
            ]);

            DB::commit();

            Flux::toast('Sale cancelled! Refund #' . $refund->refund_number . ' created.', variant: 'success');
            $this->status = 'cancelled';
            $this->sale = Sale::with(['items.product', 'refunds.items'])->findOrFail($this->sale->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Flux::toast('Error: ' . $e->getMessage(), variant: 'danger');
        }
    }

    public function render()
    {
        return view('livewire.admin.sales.edit');
    }
}
