<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->after('payment_method');
            $table->string('tax_name', 50)->nullable()->after('subtotal');
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('tax_name');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'tax_name', 'tax_percentage', 'tax_amount']);
        });
    }
};
