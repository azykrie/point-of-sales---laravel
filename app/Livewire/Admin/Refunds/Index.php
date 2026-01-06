<?php

namespace App\Livewire\Admin\Refunds;

use App\Models\Product;
use App\Models\Refund;
use App\Models\StockMovement;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Title('Refund Management')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterDate = '';
    public $filterStatus = '';
    public $viewRefundId = null;
    public $showViewModal = false;
    
    // Reject modal
    public $showRejectModal = false;
    public $rejectRefundId = null;
    public $rejectReason = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function viewRefund($id)
    {
        $this->viewRefundId = $id;
        $this->showViewModal = true;
    }

    public function closeView()
    {
        $this->viewRefundId = null;
        $this->showViewModal = false;
    }

    public function approveRefund($id)
    {
        $refund = Refund::with('items')->findOrFail($id);
        
        if (!$refund->isPending()) {
            session()->flash('error', 'This refund has already been processed.');
            return;
        }

        DB::beginTransaction();
        try {
            // Update refund status
            $refund->update([
                'status' => Refund::STATUS_APPROVED,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Restore stock for each refunded item
            foreach ($refund->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $stockBefore = $product->stock;
                    $product->increment('stock', $item->quantity);
                    
                    // Record stock movement
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockBefore + $item->quantity,
                        'notes' => "Refund approved: {$refund->refund_number}",
                    ]);
                }
            }

            // Update sale total_refunded
            $refund->sale->increment('total_refunded', $refund->total_refund);

            DB::commit();
            $this->closeView();
            session()->flash('success', 'Refund approved successfully! Stock has been restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to approve refund: ' . $e->getMessage());
        }
    }

    public function openRejectModal($id)
    {
        $this->rejectRefundId = $id;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectRefundId = null;
        $this->rejectReason = '';
    }

    public function rejectRefund()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:10',
        ], [
            'rejectReason.required' => 'Please provide a reason for rejection.',
            'rejectReason.min' => 'Rejection reason must be at least 10 characters.',
        ]);

        $refund = Refund::findOrFail($this->rejectRefundId);
        
        if (!$refund->isPending()) {
            session()->flash('error', 'This refund has already been processed.');
            $this->closeRejectModal();
            return;
        }

        $refund->update([
            'status' => Refund::STATUS_REJECTED,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'reject_reason' => $this->rejectReason,
        ]);

        $this->closeRejectModal();
        session()->flash('success', 'Refund request rejected.');
    }

    public function render()
    {
        $refunds = Refund::with(['sale', 'user', 'items.product', 'processedBy'])
            ->when($this->search, function ($query) {
                $query->where('refund_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('sale', function ($q) {
                        $q->where('invoice_number', 'like', '%' . $this->search . '%')
                          ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterDate, function ($query) {
                $query->whereDate('created_at', $this->filterDate);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $viewRefund = $this->viewRefundId ? Refund::with(['sale', 'user', 'items.product', 'processedBy'])->find($this->viewRefundId) : null;

        // Summary stats
        $pendingRefunds = Refund::where('status', Refund::STATUS_PENDING)->count();
        $todayRefunds = Refund::where('status', Refund::STATUS_APPROVED)->whereDate('processed_at', today())->sum('total_refund');
        $monthRefunds = Refund::where('status', Refund::STATUS_APPROVED)->whereMonth('processed_at', now()->month)->whereYear('processed_at', now()->year)->sum('total_refund');
        $totalRefunds = Refund::where('status', Refund::STATUS_APPROVED)->count();

        return view('livewire.admin.refunds.index', [
            'refunds' => $refunds,
            'viewRefund' => $viewRefund,
            'pendingRefunds' => $pendingRefunds,
            'todayRefunds' => $todayRefunds,
            'monthRefunds' => $monthRefunds,
            'totalRefunds' => $totalRefunds,
        ]);
    }
}
