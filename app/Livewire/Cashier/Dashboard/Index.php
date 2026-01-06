<?php

namespace App\Livewire\Cashier\Dashboard;

use App\Models\Sale;
use App\Models\SaleItem;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Title('Cashier Dashboard')]
class Index extends Component
{
    public function render()
    {
        $cashierId = auth()->id();
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Today's stats
        $todaySales = Sale::where('cashier_id', $cashierId)
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $todayTransactions = Sale::where('cashier_id', $cashierId)
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        $todayItemsSold = SaleItem::whereHas('sale', function ($query) use ($cashierId, $today) {
            $query->where('cashier_id', $cashierId)
                ->whereDate('created_at', $today)
                ->where('status', 'completed');
        })->sum('quantity');

        // This month stats
        $monthSales = Sale::where('cashier_id', $cashierId)
            ->whereBetween('created_at', [$startOfMonth, Carbon::now()])
            ->where('status', 'completed')
            ->sum('total_amount');

        $monthTransactions = Sale::where('cashier_id', $cashierId)
            ->whereBetween('created_at', [$startOfMonth, Carbon::now()])
            ->where('status', 'completed')
            ->count();

        // Recent transactions
        $recentTransactions = Sale::where('cashier_id', $cashierId)
            ->with('items')
            ->latest()
            ->take(5)
            ->get();

        // Daily sales chart (last 7 days)
        $dailySales = Sale::where('cashier_id', $cashierId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartData = [];
        $chartTransactions = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->format('D, d M');
            $chartData[] = $dailySales->get($date)?->total ?? 0;
            $chartTransactions[] = $dailySales->get($date)?->count ?? 0;
        }

        // Payment method breakdown today
        $paymentBreakdown = Sale::where('cashier_id', $cashierId)
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('livewire.cashier.dashboard.index', [
            'todaySales' => $todaySales,
            'todayTransactions' => $todayTransactions,
            'todayItemsSold' => $todayItemsSold,
            'monthSales' => $monthSales,
            'monthTransactions' => $monthTransactions,
            'recentTransactions' => $recentTransactions,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'chartTransactions' => $chartTransactions,
            'paymentBreakdown' => $paymentBreakdown,
        ]);
    }
}
