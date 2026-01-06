<?php

namespace App\Livewire\Admin\CashFlows;

use App\Models\CashFlow;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Cash Flow')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filterType = '';

    public $filterCategory = '';

    public $filterDateFrom = '';

    public $filterDateTo = '';

    // Form properties
    public $editMode = false;

    public $cashFlowId;

    public $type = 'income';

    public $category = '';

    public $amount = '';

    public $description = '';

    public $notes = '';

    public $transaction_date = '';

    public $deleteCashFlowId;

    protected $rules = [
        'type' => 'required|in:income,expense',
        'category' => 'required|string',
        'amount' => 'required|numeric|min:1',
        'description' => 'required|string|max:255',
        'notes' => 'nullable|string',
        'transaction_date' => 'required|date',
    ];

    public function mount()
    {
        $this->transaction_date = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->category = '';
    }

    public function openCreateModal()
    {
        $this->reset(['cashFlowId', 'type', 'category', 'amount', 'description', 'notes', 'editMode']);
        $this->type = 'income';
        $this->transaction_date = date('Y-m-d');
    }

    public function openEditModal($id)
    {
        $cashFlow = CashFlow::findOrFail($id);

        // Only allow editing manual entries (not system-generated)
        if ($cashFlow->sale_id || $cashFlow->refund_id) {
            Flux::toast('Automatic transactions cannot be edited!', variant: 'danger');

            return;
        }

        $this->cashFlowId = $cashFlow->id;
        $this->type = $cashFlow->type;
        $this->category = $cashFlow->category;
        $this->amount = $cashFlow->amount;
        $this->description = $cashFlow->description;
        $this->notes = $cashFlow->notes;
        $this->transaction_date = $cashFlow->transaction_date->format('Y-m-d');
        $this->editMode = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
            'notes' => $this->notes,
            'transaction_date' => $this->transaction_date,
            'user_id' => auth()->id(),
        ];

        if ($this->editMode) {
            $cashFlow = CashFlow::findOrFail($this->cashFlowId);
            $cashFlow->update($data);
            Flux::toast('Cash flow updated successfully!', variant: 'success');
        } else {
            CashFlow::create($data);
            Flux::toast('Cash flow added successfully!', variant: 'success');
        }

        Flux::modals()->close();
        $this->reset(['cashFlowId', 'type', 'category', 'amount', 'description', 'notes', 'editMode']);
    }

    public function confirmDelete($id)
    {
        $cashFlow = CashFlow::findOrFail($id);

        // Only allow deleting manual entries
        if ($cashFlow->sale_id || $cashFlow->refund_id) {
            Flux::toast('Automatic transactions cannot be deleted!', variant: 'danger');

            return;
        }

        $this->deleteCashFlowId = $id;
    }

    public function delete()
    {
        $cashFlow = CashFlow::findOrFail($this->deleteCashFlowId);
        $cashFlow->delete();
        Flux::toast('Cash flow deleted successfully!', variant: 'success');
        Flux::modals()->close();
    }

    public function resetFilters()
    {
        $this->reset(['filterType', 'filterCategory', 'filterDateFrom', 'filterDateTo', 'search']);
    }

    public function getCategories()
    {
        if ($this->type === 'income') {
            return CashFlow::getIncomeCategories();
        }

        return CashFlow::getExpenseCategories();
    }

    public function render()
    {
        $cashFlows = CashFlow::query()
            ->with(['user', 'sale', 'refund'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reference_number', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category', $this->filterCategory);
            })
            ->when($this->filterDateFrom, function ($query) {
                $query->whereDate('transaction_date', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->whereDate('transaction_date', '<=', $this->filterDateTo);
            })
            ->latest('transaction_date')
            ->latest('created_at')
            ->paginate(5);

        // Summary calculations
        $summaryQuery = CashFlow::query()
            ->when($this->filterDateFrom, fn ($q) => $q->whereDate('transaction_date', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn ($q) => $q->whereDate('transaction_date', '<=', $this->filterDateTo));

        $totalIncome = (clone $summaryQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $summaryQuery)->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $allCategories = CashFlow::getAllCategories();

        return view('livewire.admin.cash-flows.index', compact(
            'cashFlows',
            'totalIncome',
            'totalExpense',
            'balance',
            'allCategories'
        ));
    }
}
