<?php

namespace App\Livewire\Admin\Sales;

use App\Models\Sale;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Sales History')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterPaymentMethod = '';
    public $filterDate = '';
    public $deleteSaleId;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($saleId)
    {
        $this->deleteSaleId = $saleId;
    }

    public function delete()
    {
        $sale = Sale::findOrFail($this->deleteSaleId);
        
        // Restore stock for each item
        foreach ($sale->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }
        
        $sale->delete();
        Flux::toast('Sale deleted and stock restored!', variant: 'success');
        Flux::modals()->close();
    }

    public function resetFilters()
    {
        $this->reset(['filterStatus', 'filterPaymentMethod', 'filterDate', 'search']);
    }

    public function render()
    {
        $sales = Sale::query()
            ->with(['cashier', 'items'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterPaymentMethod, function ($query) {
                $query->where('payment_method', $this->filterPaymentMethod);
            })
            ->when($this->filterDate, function ($query) {
                $query->whereDate('created_at', $this->filterDate);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.sales.index', compact('sales'));
    }
}
