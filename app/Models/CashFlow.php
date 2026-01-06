<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    protected $fillable = [
        'reference_number',
        'type',
        'category',
        'amount',
        'description',
        'notes',
        'user_id',
        'sale_id',
        'refund_id',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    // Category constants
    const CATEGORY_SALES = 'sales';
    const CATEGORY_REFUND = 'refund';
    const CATEGORY_SALARY = 'salary';
    const CATEGORY_CAPITAL = 'capital';
    const CATEGORY_OPERATIONAL = 'operational';
    const CATEGORY_PURCHASE = 'purchase';
    const CATEGORY_OTHER_INCOME = 'other_income';
    const CATEGORY_OTHER_EXPENSE = 'other_expense';

    public static function getIncomeCategories(): array
    {
        return [
            self::CATEGORY_SALES => 'Sales',
            self::CATEGORY_CAPITAL => 'Capital',
            self::CATEGORY_OTHER_INCOME => 'Other Income',
        ];
    }

    public static function getExpenseCategories(): array
    {
        return [
            self::CATEGORY_REFUND => 'Refund',
            self::CATEGORY_SALARY => 'Employee Salary',
            self::CATEGORY_OPERATIONAL => 'Operational',
            self::CATEGORY_PURCHASE => 'Stock Purchase',
            self::CATEGORY_OTHER_EXPENSE => 'Other Expense',
        ];
    }

    public static function getAllCategories(): array
    {
        return array_merge(self::getIncomeCategories(), self::getExpenseCategories());
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::getAllCategories()[$this->category] ?? $this->category;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cashFlow) {
            $cashFlow->reference_number = self::generateReferenceNumber($cashFlow->type);
        });
    }

    public static function generateReferenceNumber($type): string
    {
        $prefix = $type === 'income' ? 'CF-IN' : 'CF-OUT';
        $date = date('Ymd');
        
        // Get the last reference number for today with same prefix
        $lastRef = static::where('reference_number', 'like', $prefix . $date . '%')
            ->orderBy('reference_number', 'desc')
            ->first();
        
        if ($lastRef) {
            // Extract the number from the last reference
            $lastNumber = (int) substr($lastRef->reference_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $date . $newNumber;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }
}
