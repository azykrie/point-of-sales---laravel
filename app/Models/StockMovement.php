<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'reference_number',
        'product_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
        'notes',
    ];

    // Auto generate reference number on create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movement) {
            if (empty($movement->reference_number)) {
                $movement->reference_number = self::generateReferenceNumber($movement->type);
            }
        });
    }

    // Generate unique reference number
    public static function generateReferenceNumber($type)
    {
        $prefix = $type === 'in' ? 'STK-IN' : 'STK-OUT';
        $date = date('Ymd');
        
        $lastMovement = self::where('type', $type)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastMovement) {
            $lastNumber = (int) substr($lastMovement->reference_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . '-' . $date . '-' . $newNumber;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
