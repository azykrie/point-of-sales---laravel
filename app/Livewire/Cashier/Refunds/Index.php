<?php

namespace App\Livewire\Cashier\Refunds;

use App\Models\Product;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Sale;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Request Refund')]
class Index extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $filterStatus = '';
    
    // Request refund form
    public $showRequestModal = false;
    public $invoiceSearch = '';
    public $selectedSale = null;
    public $selectedItems = [];
    public $reason = '';
    public $notes = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function openRequestModal()
    {
        $this->reset(['invoiceSearch', 'selectedSale', 'selectedItems', 'reason', 'notes']);
        $this->showRequestModal = true;
    }

    public function closeRequestModal()
    {
        $this->showRequestModal = false;
    }

    public function searchSale()
    {
        if (empty($this->invoiceSearch)) {
            return;
        }

        $this->selectedSale = Sale::with(['items.product'])
            ->where('cashier_id', auth()->id())
            ->where('status', 'completed')
            ->where('invoice_number', $this->invoiceSearch)
            ->first();

        if (!$this->selectedSale) {
            session()->flash('search-error', 'Invoice not found or not your transaction.');
        } else {
            $this->selectedItems = [];
        }
    }

    public function toggleItem($itemId)
    {
        if (isset($this->selectedItems[$itemId])) {
            unset($this->selectedItems[$itemId]);
        } else {
            $item = $this->selectedSale->items->find($itemId);
            if ($item) {
                // Calculate already refunded quantity for this item
                $refundedQty = RefundItem::whereHas('refund', function($q) use ($item) {
                    $q->where('sale_id', $this->selectedSale->id)
                      ->where('status', '!=', Refund::STATUS_REJECTED);
                })->where('product_id', $item->product_id)->sum('quantity');
                
                $maxQty = $item->quantity - $refundedQty;
                
                if ($maxQty > 0) {
                    $this->selectedItems[$itemId] = [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'price' => $item->price,
                        'quantity' => 1,
                        'max_quantity' => $maxQty,
                    ];
                }
            }
        }
    }

    public function updateItemQuantity($itemId, $quantity)
    {
        if (isset($this->selectedItems[$itemId])) {
            $max = $this->selectedItems[$itemId]['max_quantity'];
            $this->selectedItems[$itemId]['quantity'] = max(1, min($quantity, $max));
        }
    }

    public function getTotalRefundProperty()
    {
        $total = 0;
        foreach ($this->selectedItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function submitRequest()
    {
        $this->validate([
            'selectedSale' => 'required',
            'selectedItems' => 'required|array|min:1',
            'reason' => 'required|string|min:10',
        ], [
            'selectedSale.required' => 'Please search and select a sale first.',
            'selectedItems.required' => 'Please select at least one item to refund.',
            'selectedItems.min' => 'Please select at least one item to refund.',
            'reason.required' => 'Please provide a reason for the refund.',
            'reason.min' => 'Reason must be at least 10 characters.',
        ]);

        // Create refund request
        $refund = Refund::create([
            'sale_id' => $this->selectedSale->id,
            'user_id' => auth()->id(),
            'total_refund' => $this->totalRefund,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'status' => Refund::STATUS_PENDING,
        ]);

        // Create refund items
        foreach ($this->selectedItems as $item) {
            RefundItem::create([
                'refund_id' => $refund->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        $this->closeRequestModal();
        session()->flash('success', 'Refund request submitted successfully! Waiting for admin approval.');
    }

    public function render()
    {
        $refunds = Refund::with(['sale', 'items.product', 'processedBy'])
            ->where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where('refund_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('sale', function ($q) {
                        $q->where('invoice_number', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Summary stats
        $pendingCount = Refund::where('user_id', auth()->id())->where('status', Refund::STATUS_PENDING)->count();
        $approvedCount = Refund::where('user_id', auth()->id())->where('status', Refund::STATUS_APPROVED)->count();
        $rejectedCount = Refund::where('user_id', auth()->id())->where('status', Refund::STATUS_REJECTED)->count();

        return view('livewire.cashier.refunds.index', [
            'refunds' => $refunds,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }
}
