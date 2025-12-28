<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products Table')]
class Index extends Component
{
    use WithPagination;
    public $search = '';
    public $deleteProductId;
    public $selectedProducts = [];
    public $selectAll = false;
    public $filterCategory = '';
    public $filterAvailability = '';
    public $filterStock = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function updatingFilterAvailability()
    {
        $this->resetPage();
    }

    public function updatingFilterStock()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['filterCategory', 'filterAvailability', 'filterStock', 'search']);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProducts = Product::pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function confirmDelete($productId)
    {
        $this->deleteProductId = $productId;
    }

    public function delete()
    {
        Product::destroy($this->deleteProductId);
        Flux::toast('Product deleted successfully!', variant:'success');
        Flux::modals()->close();
    }

    public function deleteSelected()
    {
        if (count($this->selectedProducts) > 0) {
            Product::whereIn('id', $this->selectedProducts)->delete();
            Flux::toast(count($this->selectedProducts) . ' products deleted!', variant: 'success');
            $this->selectedProducts = [];
            $this->selectAll = false;
        }
    }

    public function toggleAvailable($productId)
    {
        $product = Product::findOrFail($productId);
        $product->is_available = !$product->is_available;
        $product->save();

        $status = $product->is_available ? 'available' : 'unavailable';
        Flux::toast("Product marked as {$status}!", variant: 'success');
    }

    public function exportCsv()
    {
        $products = $this->getProductsForExport();
        
        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');
            
            // Header
            fputcsv($handle, ['Barcode', 'Name', 'Category', 'Buy Price', 'Sell Price', 'Stock', 'Available']);
            
            // Data
            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->barcode,
                    $product->name,
                    $product->category->name ?? '-',
                    $product->price,
                    $product->selling_price,
                    $product->stock,
                    $product->is_available ? 'Yes' : 'No',
                ]);
            }
            
            fclose($handle);
        }, 'products_' . date('Y-m-d') . '.csv');
    }

    public function exportExcel()
    {
        $products = $this->getProductsForExport();
        
        return response()->streamDownload(function () use ($products) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head>';
            echo '<body><table border="1">';
            
            // Header
            echo '<tr style="font-weight: bold; background-color: #f0f0f0;">';
            echo '<th>Barcode</th>';
            echo '<th>Name</th>';
            echo '<th>Category</th>';
            echo '<th>Buy Price</th>';
            echo '<th>Sell Price</th>';
            echo '<th>Stock</th>';
            echo '<th>Available</th>';
            echo '</tr>';
            
            // Data
            foreach ($products as $product) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($product->barcode) . '</td>';
                echo '<td>' . htmlspecialchars($product->name) . '</td>';
                echo '<td>' . htmlspecialchars($product->category->name ?? '-') . '</td>';
                echo '<td>' . number_format($product->price, 0) . '</td>';
                echo '<td>' . number_format($product->selling_price, 0) . '</td>';
                echo '<td>' . $product->stock . '</td>';
                echo '<td>' . ($product->is_available ? 'Yes' : 'No') . '</td>';
                echo '</tr>';
            }
            
            echo '</table></body></html>';
        }, 'products_' . date('Y-m-d') . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    private function getProductsForExport()
    {
        if (count($this->selectedProducts) > 0) {
            return Product::with('category')->whereIn('id', $this->selectedProducts)->get();
        }
        return Product::with('category')->get();
    }
    
    public function render()
    {
        $products = Product::query()
            ->with('category')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterAvailability !== '', function ($query) {
                $query->where('is_available', $this->filterAvailability === 'available');
            })
            ->when($this->filterStock, function ($query) {
                match ($this->filterStock) {
                    'in_stock' => $query->where('stock', '>', 10),
                    'low_stock' => $query->whereBetween('stock', [1, 10]),
                    'out_of_stock' => $query->where('stock', '<=', 0),
                    default => null,
                };
            })
            ->latest()
            ->paginate(10);

        $categories = \App\Models\Category::orderBy('name')->get();

        return view('livewire.admin.products.index', compact('products', 'categories'));
    }
}
