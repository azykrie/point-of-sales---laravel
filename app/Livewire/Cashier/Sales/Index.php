<?php

namespace App\Livewire\Cashier\Sales;

use App\Models\Sale;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My Transactions')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterPaymentMethod = '';
    public $filterDate = '';
    public $selectedSale = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['filterStatus', 'filterPaymentMethod', 'filterDate', 'search']);
    }

    public function showReceipt($saleId)
    {
        $this->selectedSale = Sale::with(['items', 'cashier'])
            ->where('cashier_id', auth()->id())
            ->findOrFail($saleId);
        
        Flux::modal('receipt-modal')->show();
    }

    public function printReceipt()
    {
        $this->dispatch('print-receipt');
    }

    public function closeReceipt()
    {
        $this->selectedSale = null;
        Flux::modal('receipt-modal')->close();
    }

    public function render()
    {
        $sales = Sale::query()
            ->with(['items'])
            ->where('cashier_id', auth()->id()) // Only show transactions by this cashier
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

        return view('livewire.cashier.sales.index', compact('sales'));
    }
}
