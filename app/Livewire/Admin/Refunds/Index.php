<?php

namespace App\Livewire\Admin\Refunds;

use App\Models\Refund;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Refund History')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterDate = '';
    public $viewRefundId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function viewRefund($id)
    {
        $this->viewRefundId = $id;
    }

    public function closeView()
    {
        $this->viewRefundId = null;
    }

    public function render()
    {
        $refunds = Refund::with(['sale', 'user', 'items.product'])
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
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $viewRefund = $this->viewRefundId ? Refund::with(['sale', 'user', 'items.product'])->find($this->viewRefundId) : null;

        // Summary stats
        $todayRefunds = Refund::whereDate('created_at', today())->sum('total_refund');
        $monthRefunds = Refund::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_refund');
        $totalRefunds = Refund::count();

        return view('livewire.admin.refunds.index', [
            'refunds' => $refunds,
            'viewRefund' => $viewRefund,
            'todayRefunds' => $todayRefunds,
            'monthRefunds' => $monthRefunds,
            'totalRefunds' => $totalRefunds,
        ]);
    }
}
