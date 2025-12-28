<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create Category')]
class Create extends Component
{
    public $name;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:categories',
        ];
    }

    public function save()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
        ]);

        session()->flash('success', 'Category created successfully!');
        return $this->redirect(route('admin.categories.index'), navigate: true);

    }
    
    public function render()
    {
        return view('livewire.admin.categories.create');
    }
}
