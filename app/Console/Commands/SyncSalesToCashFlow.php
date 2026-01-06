<?php

namespace App\Console\Commands;

use App\Models\CashFlow;
use App\Models\Sale;
use Illuminate\Console\Command;

class SyncSalesToCashFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cashflow:sync-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing completed sales to cash flow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing completed sales to cash flow...');
        
        $sales = Sale::where('status', 'completed')->get();
        $synced = 0;
        
        foreach ($sales as $sale) {
            if (!CashFlow::where('sale_id', $sale->id)->exists()) {
                CashFlow::create([
                    'type' => 'income',
                    'category' => CashFlow::CATEGORY_SALES,
                    'amount' => $sale->total_amount,
                    'description' => 'Sale ' . $sale->invoice_number,
                    'notes' => 'Customer: ' . ($sale->customer_name ?? 'Walk-in') . ', Method: ' . strtoupper($sale->payment_method),
                    'user_id' => $sale->cashier_id,
                    'sale_id' => $sale->id,
                    'transaction_date' => $sale->created_at->toDateString(),
                ]);
                $this->line("Created cash flow for: {$sale->invoice_number}");
                $synced++;
            }
        }
        
        $this->info("Done! Synced {$synced} sales to cash flow.");
        
        return Command::SUCCESS;
    }
}
