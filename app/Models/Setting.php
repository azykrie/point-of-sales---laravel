<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        Cache::forget('setting_' . $key);
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllSettings(): array
    {
        return static::pluck('value', 'key')->toArray();
    }

    // Default settings keys
    const STORE_NAME = 'store_name';
    const STORE_ADDRESS = 'store_address';
    const STORE_PHONE = 'store_phone';
    const STORE_EMAIL = 'store_email';
    const STORE_LOGO = 'store_logo';
    const STORE_LOGO_DARK = 'store_logo_dark';
    const RECEIPT_FOOTER = 'receipt_footer';
    const TAX_ENABLED = 'tax_enabled';
    const TAX_PERCENTAGE = 'tax_percentage';
    const TAX_NAME = 'tax_name';

    /**
     * Get default values for settings
     */
    public static function getDefaults(): array
    {
        return [
            self::STORE_NAME => 'My Store',
            self::STORE_ADDRESS => 'Jl. Example No. 123',
            self::STORE_PHONE => '08123456789',
            self::STORE_EMAIL => 'store@example.com',
            self::STORE_LOGO => null,
            self::STORE_LOGO_DARK => null,
            self::RECEIPT_FOOTER => 'Thank you for your purchase!',
            self::TAX_ENABLED => '0',
            self::TAX_PERCENTAGE => '11',
            self::TAX_NAME => 'PPN',
        ];
    }
}
