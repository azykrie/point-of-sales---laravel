<?php

namespace App\Livewire\Cashier\Sales;

use App\Models\Sale;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Transaction Detail')]
class Show extends Component
{
    public Sale $sale;

    public function mount($id)
    {
        $this->sale = Sale::with(['items.product', 'refunds.items'])
            ->where('cashier_id', auth()->id()) // Only allow viewing own transactions
            ->findOrFail($id);
    }

    public function printReceipt()
    {
        $this->dispatch('print-receipt');
    }

    public function render()
    {
        return view('livewire.cashier.sales.show');
    }
}
