<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Categories Table')]
class Index extends Component
{
    use WithPagination;
    public $search = '';
    public $deleteCategoryId;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($categoryId)
    {
        $this->deleteCategoryId = $categoryId;
    }

    public function delete()
    {
        Category::destroy($this->deleteCategoryId);
        Flux::toast('Category deleted successfully!', variant:'success');
        Flux::modals()->close();
    }
    
    public function render()
    {
        $categories = Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(5);

        return view('livewire.admin.categories.index', compact('categories'));
    }
}
