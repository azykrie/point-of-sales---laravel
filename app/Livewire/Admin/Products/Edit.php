<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Title('Edit Product')]
class Edit extends Component
{
    use WithFileUploads;

    public $name, $barcode, $category_id, $price, $selling_price, $stock, $description, $productId;
    public $image, $existingImage;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $this->productId,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // max 2MB
        ];
    }

    public function mount($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->barcode = $product->barcode;
        $this->category_id = $product->category_id;
        $this->price = $product->price;
        $this->selling_price = $product->selling_price;
        $this->stock = $product->stock;
        $this->description = $product->description;
        $this->existingImage = $product->image;
    }

    public function update()
    {
        $this->validate();

        $product = Product::findOrFail($this->productId);
        $product->name = $this->name;
        $product->barcode = $this->barcode;
        $product->category_id = $this->category_id;
        $product->price = $this->price;
        $product->selling_price = $this->selling_price;
        $product->stock = $this->stock;
        $product->description = $this->description;

        // If new image is uploaded
        if ($this->image) {
            // Delete old image if exists
            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $product->image = $this->image->store('products', 'public');
        }

        $product->save();

        session()->flash('success', 'Product updated successfully!');
        return $this->redirect(route('admin.products.index'), navigate: true);
    }
    
    public function render()
    {
        $categories = Category::all();
        return view('livewire.admin.products.edit', compact('categories'));
    }
}
