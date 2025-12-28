<?php

namespace App\Livewire\Admin\StockMovements;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $filterType = '';
    public $filterDate = '';
    public $filterReason = '';

    // Stock In Form
    public $showStockInModal = false;
    public $stockInProductId = '';
    public $stockInQuantity = 1;
    public $stockInReason = 'purchase';
    public $stockInNotes = '';

    // Stock Out Form
    public $showStockOutModal = false;
    public $stockOutProductId = '';
    public $stockOutQuantity = 1;
    public $stockOutReason = 'adjustment';
    public $stockOutNotes = '';

    // Delete
    public $deleteMovementId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function updatingFilterReason()
    {
        $this->resetPage();
    }

    // Stock In Methods
    public function openStockInModal()
    {
        $this->reset(['stockInProductId', 'stockInQuantity', 'stockInReason', 'stockInNotes']);
        $this->stockInQuantity = 1;
        $this->stockInReason = 'purchase';
        $this->showStockInModal = true;
    }

    public function closeStockInModal()
    {
        $this->showStockInModal = false;
    }

    public function processStockIn()
    {
        $this->validate([
            'stockInProductId' => 'required|exists:products,id',
            'stockInQuantity' => 'required|integer|min:1',
            'stockInReason' => 'required|string',
        ]);

        $product = Product::find($this->stockInProductId);
        $stockBefore = $product->stock;
        $stockAfter = $stockBefore + $this->stockInQuantity;

        // Update product stock
        $product->update(['stock' => $stockAfter]);

        // Create stock movement record
        StockMovement::create([
            'product_id' => $this->stockInProductId,
            'user_id' => auth()->id(),
            'type' => 'in',
            'quantity' => $this->stockInQuantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reason' => $this->stockInReason,
            'notes' => $this->stockInNotes,
        ]);

        $this->closeStockInModal();
        session()->flash('success', 'Stock in recorded successfully! ' . $product->name . ' increased by ' . $this->stockInQuantity . ' units.');
    }

    // Stock Out Methods
    public function openStockOutModal()
    {
        $this->reset(['stockOutProductId', 'stockOutQuantity', 'stockOutReason', 'stockOutNotes']);
        $this->stockOutQuantity = 1;
        $this->stockOutReason = 'adjustment';
        $this->showStockOutModal = true;
    }

    public function closeStockOutModal()
    {
        $this->showStockOutModal = false;
    }

    public function processStockOut()
    {
        $this->validate([
            'stockOutProductId' => 'required|exists:products,id',
            'stockOutQuantity' => 'required|integer|min:1',
            'stockOutReason' => 'required|string',
        ]);

        $product = Product::find($this->stockOutProductId);
        
        if ($product->stock < $this->stockOutQuantity) {
            session()->flash('error', 'Insufficient stock! Available: ' . $product->stock);
            return;
        }

        $stockBefore = $product->stock;
        $stockAfter = $stockBefore - $this->stockOutQuantity;

        // Update product stock
        $product->update(['stock' => $stockAfter]);

        // Create stock movement record
        StockMovement::create([
            'product_id' => $this->stockOutProductId,
            'user_id' => auth()->id(),
            'type' => 'out',
            'quantity' => $this->stockOutQuantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reason' => $this->stockOutReason,
            'notes' => $this->stockOutNotes,
        ]);

        $this->closeStockOutModal();
        session()->flash('success', 'Stock out recorded successfully! ' . $product->name . ' decreased by ' . $this->stockOutQuantity . ' units.');
    }

    // Delete Movement
    public function confirmDelete($id)
    {
        $this->deleteMovementId = $id;
    }

    public function delete()
    {
        $movement = StockMovement::find($this->deleteMovementId);
        
        if ($movement) {
            $product = $movement->product;
            
            // Reverse the stock change
            if ($movement->type === 'in') {
                $product->update(['stock' => $product->stock - $movement->quantity]);
            } else {
                $product->update(['stock' => $product->stock + $movement->quantity]);
            }
            
            $movement->delete();
            session()->flash('success', 'Stock movement deleted and stock restored successfully.');
        }
        
        $this->deleteMovementId = null;
    }

    public function render()
    {
        $movements = StockMovement::with(['product', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('barcode', 'like', '%' . $this->search . '%');
                })->orWhere('reference_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterDate, function ($query) {
                $query->whereDate('created_at', $this->filterDate);
            })
            ->when($this->filterReason, function ($query) {
                $query->where('reason', $this->filterReason);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $products = Product::where('is_available', true)->orderBy('name')->get();

        // Get unique reasons for filter
        $reasons = StockMovement::distinct()->pluck('reason')->filter();

        return view('livewire.admin.stock-movements.index', [
            'movements' => $movements,
            'products' => $products,
            'reasons' => $reasons,
        ]);
    }
}
