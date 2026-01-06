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
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->enum('type', ['income', 'expense']); // income = uang masuk, expense = uang keluar
            $table->string('category'); // sales, refund, salary, capital, operational, etc
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siapa yang mencatat
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null'); // jika terkait dengan penjualan
            $table->foreignId('refund_id')->nullable()->constrained()->onDelete('set null'); // jika terkait dengan refund
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};
