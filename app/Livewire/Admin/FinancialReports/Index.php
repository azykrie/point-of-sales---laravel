<?php

namespace App\Livewire\Admin\FinancialReports;

use App\Models\CashFlow;
use App\Models\Sale;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Financial Report')]
class Index extends Component
{
    public $dateFrom = '';
    public $dateTo = '';
    public $reportType = 'all'; // all, sales, income, expense

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getSalesData()
    {
        return Sale::query()
            ->with(['cashier', 'items'])
            ->where('status', 'completed')
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getIncomeData()
    {
        return CashFlow::query()
            ->with(['user', 'sale'])
            ->where('type', 'income')
            ->when($this->dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $this->dateTo))
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function getExpenseData()
    {
        return CashFlow::query()
            ->with(['user', 'refund'])
            ->where('type', 'expense')
            ->when($this->dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $this->dateTo))
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function getSummary()
    {
        $sales = $this->getSalesData();
        $income = $this->getIncomeData();
        $expense = $this->getExpenseData();

        return [
            'total_sales' => $sales->sum('total_amount'),
            'total_transactions' => $sales->count(),
            'total_income' => $income->sum('amount'),
            'total_expense' => $expense->sum('amount'),
            'net_profit' => $income->sum('amount') - $expense->sum('amount'),
        ];
    }

    public function downloadSales($format = 'csv')
    {
        $sales = $this->getSalesData();
        $filename = 'sales_report_' . $this->dateFrom . '_to_' . $this->dateTo . '.' . $format;

        return $this->generateDownload($format, $filename, function() use ($sales) {
            $headers = ['Invoice', 'Date', 'Customer', 'Items', 'Payment Method', 'Total', 'Paid', 'Change', 'Cashier', 'Status'];
            $rows = [];
            
            foreach ($sales as $sale) {
                $rows[] = [
                    $sale->invoice_number,
                    $sale->created_at->format('Y-m-d H:i'),
                    $sale->customer_name,
                    $sale->items->count(),
                    strtoupper($sale->payment_method),
                    $sale->total_amount,
                    $sale->paid_amount,
                    $sale->change_amount,
                    $sale->cashier?->name ?? '-',
                    $sale->status,
                ];
            }
            
            return ['headers' => $headers, 'rows' => $rows];
        });
    }

    public function downloadIncome($format = 'csv')
    {
        $income = $this->getIncomeData();
        $filename = 'income_report_' . $this->dateFrom . '_to_' . $this->dateTo . '.' . $format;

        return $this->generateDownload($format, $filename, function() use ($income) {
            $headers = ['Reference', 'Date', 'Category', 'Description', 'Amount', 'Notes', 'Recorded By'];
            $rows = [];
            
            foreach ($income as $item) {
                $rows[] = [
                    $item->reference_number,
                    $item->transaction_date->format('Y-m-d'),
                    $item->category_label,
                    $item->description,
                    $item->amount,
                    $item->notes ?? '-',
                    $item->user?->name ?? '-',
                ];
            }
            
            return ['headers' => $headers, 'rows' => $rows];
        });
    }

    public function downloadExpense($format = 'csv')
    {
        $expense = $this->getExpenseData();
        $filename = 'expense_report_' . $this->dateFrom . '_to_' . $this->dateTo . '.' . $format;

        return $this->generateDownload($format, $filename, function() use ($expense) {
            $headers = ['Reference', 'Date', 'Category', 'Description', 'Amount', 'Notes', 'Recorded By'];
            $rows = [];
            
            foreach ($expense as $item) {
                $rows[] = [
                    $item->reference_number,
                    $item->transaction_date->format('Y-m-d'),
                    $item->category_label,
                    $item->description,
                    $item->amount,
                    $item->notes ?? '-',
                    $item->user?->name ?? '-',
                ];
            }
            
            return ['headers' => $headers, 'rows' => $rows];
        });
    }

    public function downloadAll($format = 'csv')
    {
        $sales = $this->getSalesData();
        $income = $this->getIncomeData();
        $expense = $this->getExpenseData();
        $summary = $this->getSummary();
        
        $filename = 'financial_report_' . $this->dateFrom . '_to_' . $this->dateTo . '.' . $format;

        return $this->generateDownload($format, $filename, function() use ($sales, $income, $expense, $summary) {
            $headers = ['Type', 'Reference', 'Date', 'Category', 'Description', 'Amount', 'Notes'];
            $rows = [];
            
            // Add summary section
            $rows[] = ['=== SUMMARY ===', '', '', '', '', '', ''];
            $rows[] = ['Total Sales', '', '', '', '', $summary['total_sales'], ''];
            $rows[] = ['Total Transactions', '', '', '', '', $summary['total_transactions'], ''];
            $rows[] = ['Total Income', '', '', '', '', $summary['total_income'], ''];
            $rows[] = ['Total Expense', '', '', '', '', $summary['total_expense'], ''];
            $rows[] = ['Net Profit', '', '', '', '', $summary['net_profit'], ''];
            $rows[] = ['', '', '', '', '', '', ''];
            
            // Add sales section
            $rows[] = ['=== SALES ===', '', '', '', '', '', ''];
            foreach ($sales as $sale) {
                $rows[] = [
                    'SALE',
                    $sale->invoice_number,
                    $sale->created_at->format('Y-m-d'),
                    strtoupper($sale->payment_method),
                    'Customer: ' . $sale->customer_name,
                    $sale->total_amount,
                    $sale->notes ?? '-',
                ];
            }
            $rows[] = ['', '', '', '', '', '', ''];
            
            // Add income section
            $rows[] = ['=== INCOME ===', '', '', '', '', '', ''];
            foreach ($income as $item) {
                $rows[] = [
                    'INCOME',
                    $item->reference_number,
                    $item->transaction_date->format('Y-m-d'),
                    $item->category_label,
                    $item->description,
                    $item->amount,
                    $item->notes ?? '-',
                ];
            }
            $rows[] = ['', '', '', '', '', '', ''];
            
            // Add expense section
            $rows[] = ['=== EXPENSE ===', '', '', '', '', '', ''];
            foreach ($expense as $item) {
                $rows[] = [
                    'EXPENSE',
                    $item->reference_number,
                    $item->transaction_date->format('Y-m-d'),
                    $item->category_label,
                    $item->description,
                    $item->amount,
                    $item->notes ?? '-',
                ];
            }
            
            return ['headers' => $headers, 'rows' => $rows];
        });
    }

    protected function generateDownload($format, $filename, $dataCallback): StreamedResponse
    {
        $data = $dataCallback();
        
        if ($format === 'csv') {
            return $this->generateCsv($filename, $data['headers'], $data['rows']);
        }
        
        return $this->generateExcel($filename, $data['headers'], $data['rows']);
    }

    protected function generateCsv($filename, $headers, $rows): StreamedResponse
    {
        return response()->streamDownload(function() use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, $headers);
            
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function generateExcel($filename, $headers, $rows): StreamedResponse
    {
        // Generate as TSV (Tab-separated) which Excel can open directly
        return response()->streamDownload(function() use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            fwrite($handle, implode("\t", $headers) . "\n");
            
            // Write rows
            foreach ($rows as $row) {
                fwrite($handle, implode("\t", $row) . "\n");
            }
            
            fclose($handle);
        }, str_replace('.xlsx', '.xls', $filename), [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function render()
    {
        $summary = $this->getSummary();
        $sales = $this->getSalesData();
        $income = $this->getIncomeData();
        $expense = $this->getExpenseData();

        return view('livewire.admin.financial-reports.index', compact(
            'summary',
            'sales', 
            'income', 
            'expense'
        ));
    }
}
