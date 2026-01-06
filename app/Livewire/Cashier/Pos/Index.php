<?php

namespace App\Livewire\Cashier\Pos;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Point of Sale')]
class Index extends Component
{
    public $customerName = '';
    public $paymentMethod = 'cash';
    public $paidAmount = 0;
    public $notes = '';
    
    public $cart = [];
    public $selectedProductId = '';
    public $quantity = 1;
    
    public $lastSale = null;
    public $showReceipt = false;

    public function addToCart()
    {
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($this->selectedProductId);
        
        if (!$product->is_available) {
            Flux::toast('Product is not available!', variant: 'danger');
            return;
        }

        if ($product->stock < $this->quantity) {
            Flux::toast('Not enough stock! Available: ' . $product->stock, variant: 'danger');
            return;
        }

        // Check if product already in cart
        $existingIndex = collect($this->cart)->search(fn($item) => $item['product_id'] == $product->id);
        
        if ($existingIndex !== false) {
            $newQty = $this->cart[$existingIndex]['quantity'] + $this->quantity;
            if ($newQty > $product->stock) {
                Flux::toast('Not enough stock! Available: ' . $product->stock, variant: 'danger');
                return;
            }
            $this->cart[$existingIndex]['quantity'] = $newQty;
            $this->cart[$existingIndex]['subtotal'] = $newQty * $product->selling_price;
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'image' => $product->image,
                'price' => $product->selling_price,
                'stock' => $product->stock,
                'quantity' => $this->quantity,
                'subtotal' => $this->quantity * $product->selling_price,
            ];
        }

        $this->reset(['selectedProductId', 'quantity']);
        $this->quantity = 1;
        Flux::toast('Product added to cart!', variant: 'success');
    }

    public function quickAdd($productId)
    {
        $this->selectedProductId = $productId;
        $this->quantity = 1;
        $this->addToCart();
    }

    public function updateQuantity($index, $qty)
    {
        if ($qty < 1) {
            $this->removeFromCart($index);
            return;
        }

        $product = Product::find($this->cart[$index]['product_id']);
        
        if ($qty > $product->stock) {
            Flux::toast('Not enough stock! Available: ' . $product->stock, variant: 'danger');
            return;
        }

        $this->cart[$index]['quantity'] = $qty;
        $this->cart[$index]['subtotal'] = $qty * $this->cart[$index]['price'];
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        Flux::toast('Product removed from cart!', variant: 'success');
    }

    public function getSubtotal()
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function getTaxEnabled()
    {
        return (bool) Setting::get(Setting::TAX_ENABLED, '0');
    }

    public function getTaxPercentage()
    {
        return (float) Setting::get(Setting::TAX_PERCENTAGE, '11');
    }

    public function getTaxName()
    {
        return Setting::get(Setting::TAX_NAME, 'PPN');
    }

    public function getTaxAmount()
    {
        if (!$this->getTaxEnabled()) {
            return 0;
        }
        return $this->getSubtotal() * ($this->getTaxPercentage() / 100);
    }

    public function getTotal()
    {
        return $this->getSubtotal() + $this->getTaxAmount();
    }

    public function getChange()
    {
        return max(0, (int) $this->paidAmount - $this->getTotal());
    }

    public function processSale()
    {
        if (empty($this->cart)) {
            Flux::toast('Cart is empty!', variant: 'danger');
            return;
        }

        $total = $this->getTotal();

        if ($this->paymentMethod === 'cash' && (int) $this->paidAmount < $total) {
            Flux::toast('Paid amount is less than total!', variant: 'danger');
            return;
        }

        // For non-cash payments, set paid amount equal to total
        if ($this->paymentMethod !== 'cash') {
            $this->paidAmount = $total;
        }

        try {
            DB::beginTransaction();

            // Get tax info
            $subtotal = $this->getSubtotal();
            $taxEnabled = $this->getTaxEnabled();
            $taxName = $taxEnabled ? $this->getTaxName() : null;
            $taxPercentage = $taxEnabled ? $this->getTaxPercentage() : 0;
            $taxAmount = $this->getTaxAmount();

            // Create sale
            $sale = Sale::create([
                'customer_name' => $this->customerName ?: 'Guest',
                'cashier_id' => Auth::id(),
                'payment_method' => $this->paymentMethod,
                'subtotal' => $subtotal,
                'tax_name' => $taxName,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'paid_amount' => $this->paidAmount,
                'change_amount' => $this->getChange(),
                'notes' => $this->notes,
                'status' => 'completed',
            ]);

            // Create sale items and update stock
            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Update stock
                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // Store last sale for receipt
            $this->lastSale = $sale->load('items');
            $this->showReceipt = true;

            // Reset form
            $this->reset(['customerName', 'paymentMethod', 'paidAmount', 'notes', 'cart']);
            $this->paymentMethod = 'cash';

            Flux::toast('Sale completed successfully!', variant: 'success');
            Flux::modal('receipt-modal')->show();

        } catch (\Exception $e) {
            DB::rollBack();
            Flux::toast('Error: ' . $e->getMessage(), variant: 'danger');
        }
    }

    public function printReceipt()
    {
        $this->dispatch('print-receipt');
    }

    public function closeReceipt()
    {
        $this->showReceipt = false;
        $this->lastSale = null;
        Flux::modal('receipt-modal')->close();
    }

    public function render()
    {
        $products = Product::where('is_available', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('livewire.cashier.pos.index', [
            'products' => $products,
            'subtotal' => $this->getSubtotal(),
            'taxEnabled' => $this->getTaxEnabled(),
            'taxName' => $this->getTaxName(),
            'taxPercentage' => $this->getTaxPercentage(),
            'taxAmount' => $this->getTaxAmount(),
            'total' => $this->getTotal(),
            'change' => $this->getChange(),
        ]);
    }
}
