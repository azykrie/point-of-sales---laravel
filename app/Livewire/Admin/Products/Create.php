<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Create Product')]
class Create extends Component
{
    use WithFileUploads;

    public $name, $barcode, $category_id, $price, $selling_price, $description, $image;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // max 2MB
        ];
    }

    public function save()
    {
        $this->validate();

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        Product::create([
            'name' => $this->name,
            'barcode' => $this->barcode,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'selling_price' => $this->selling_price,
            'stock' => 0, // Stock is always 0, use Stock Movements to add stock
            'description' => $this->description,
            'image' => $imagePath,
        ]);

        session()->flash('success', 'Product created successfully!');
        return $this->redirect(route('admin.products.index'), navigate: true);

    }
    
    public function render()
    {
        $categories = Category::all();
        return view('livewire.admin.products.create', compact('categories'));
    }
}
