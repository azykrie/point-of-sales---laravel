<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Edit Category')]
class Edit extends Component
{
    public $name, $categoryId;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->categoryId,
        ];
    }

    public function mount($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
    }

    public function update()
    {
        $this->validate();

        $category = Category::findOrFail($this->categoryId);
        $category->name = $this->name;
        $category->save();

        session()->flash('success', 'Category updated successfully!');
        return $this->redirect(route('admin.categories.index'), navigate: true);
    }
    
    public function render()
    {
        return view('livewire.admin.categories.edit');
    }
}
