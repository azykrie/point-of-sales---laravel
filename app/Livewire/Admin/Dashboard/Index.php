<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\CashFlow;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Index extends Component
{
    public function getTodayStats()
    {
        $today = now()->toDateString();
        
        $todaySales = Sale::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->get();
        
        $todayIncome = CashFlow::whereDate('transaction_date', $today)
            ->where('type', 'income')
            ->sum('amount');
            
        $todayExpense = CashFlow::whereDate('transaction_date', $today)
            ->where('type', 'expense')
            ->sum('amount');
        
        return [
            'sales_count' => $todaySales->count(),
            'sales_amount' => $todaySales->sum('total_amount'),
            'income' => $todayIncome,
            'expense' => $todayExpense,
            'profit' => $todayIncome - $todayExpense,
        ];
    }

    public function getThisMonthStats()
    {
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();
        
        $monthSales = Sale::whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->where('status', 'completed')
            ->get();
        
        $monthIncome = CashFlow::whereDate('transaction_date', '>=', $startOfMonth)
            ->whereDate('transaction_date', '<=', $endOfMonth)
            ->where('type', 'income')
            ->sum('amount');
            
        $monthExpense = CashFlow::whereDate('transaction_date', '>=', $startOfMonth)
            ->whereDate('transaction_date', '<=', $endOfMonth)
            ->where('type', 'expense')
            ->sum('amount');
        
        return [
            'sales_count' => $monthSales->count(),
            'sales_amount' => $monthSales->sum('total_amount'),
            'income' => $monthIncome,
            'expense' => $monthExpense,
            'profit' => $monthIncome - $monthExpense,
        ];
    }

    public function getTopProducts($limit = 5)
    {
        return SaleItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('sale', function($q) {
                $q->where('status', 'completed');
            })
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }

    public function getLeastProducts($limit = 5)
    {
        // Get products that have been sold but with lowest quantity
        $soldProducts = SaleItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('sale', function($q) {
                $q->where('status', 'completed');
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_sold', 'asc')
            ->limit($limit)
            ->get();
        
        // If we have less than limit, also include unsold products
        if ($soldProducts->count() < $limit) {
            $soldProductIds = $soldProducts->pluck('product_id')->toArray();
            $unsoldProducts = Product::whereNotIn('id', $soldProductIds)
                ->where('is_available', true)
                ->limit($limit - $soldProducts->count())
                ->get()
                ->map(function($product) {
                    return (object)[
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'total_sold' => 0,
                        'total_revenue' => 0,
                    ];
                });
            
            return $soldProducts->concat($unsoldProducts);
        }
        
        return $soldProducts;
    }

    public function getCategorySales()
    {
        return SaleItem::select('products.category_id', 'categories.name as category_name', DB::raw('SUM(sale_items.quantity) as total_sold'), DB::raw('SUM(sale_items.subtotal) as total_revenue'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereHas('sale', function($q) {
                $q->where('status', 'completed');
            })
            ->groupBy('products.category_id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();
    }

    public function getWeeklySales()
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $sales = Sale::whereDate('created_at', $date->toDateString())
                ->where('status', 'completed')
                ->sum('total_amount');
            
            $days->push([
                'date' => $date->format('D'),
                'full_date' => $date->format('d/m'),
                'amount' => $sales,
            ]);
        }
        return $days;
    }

    public function render()
    {
        return view('livewire.admin.dashboard.index', [
            'todayStats' => $this->getTodayStats(),
            'monthStats' => $this->getThisMonthStats(),
            'topProducts' => $this->getTopProducts(),
            'leastProducts' => $this->getLeastProducts(),
            'categorySales' => $this->getCategorySales(),
            'weeklySales' => $this->getWeeklySales(),
        ]);
    }
}
