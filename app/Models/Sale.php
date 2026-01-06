<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_name',
        'cashier_id',
        'payment_method',
        'subtotal',
        'tax_name',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'change_amount',
        'total_refunded',
        'status',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            $sale->invoice_number = 'INV' . date('Ymd') . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
