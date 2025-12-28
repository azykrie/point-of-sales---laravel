<?php

use App\Http\Controllers\Admin\PrintBarcodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');



Route::middleware(['role:admin'])->group(function () {
    Route::get('admin/dashboard', \App\Livewire\Admin\Dashboard\Index::class)->name('admin.dashboard.index');

    Route::get('admin/categories', \App\Livewire\Admin\Categories\Index::class)->name('admin.categories.index');
    Route::get('admin/categories/create', \App\Livewire\Admin\Categories\Create::class)->name('admin.categories.create');
    Route::get('admin/categories/{id}/edit', \App\Livewire\Admin\Categories\Edit::class)->name('admin.categories.edit');

    Route::get('/admin/cashiers', \App\Livewire\Admin\Cashiers\Index::class)->name('admin.cashiers.index');

    Route::get('admin/sales', \App\Livewire\Admin\Sales\Index::class)->name('admin.sales.index');
    Route::get('admin/sales/{id}/edit', \App\Livewire\Admin\Sales\Edit::class)->name('admin.sales.edit');

    Route::get('admin/refunds', \App\Livewire\Admin\Refunds\Index::class)->name('admin.refunds.index');

    Route::get('admin/stock-movements', \App\Livewire\Admin\StockMovements\Index::class)->name('admin.stock-movements.index');

    Route::get('admin/products', \App\Livewire\Admin\Products\Index::class)->name('admin.products.index');
    Route::get('admin/products/create', \App\Livewire\Admin\Products\Create::class)->name('admin.products.create');
    Route::get('admin/products/{id}/edit', \App\Livewire\Admin\Products\Edit::class)->name('admin.products.edit');
    Route::get('admin/products/print-barcode', PrintBarcodeController::class)->name('admin.products.print-barcode');
    
    Route::get('admin/users', \App\Livewire\Admin\Users\Index::class)->name('admin.users.index');
    Route::get('admin/users/create', \App\Livewire\Admin\Users\Create::class)->name('admin.users.create');
    Route::get('admin/users/{id}/edit', \App\Livewire\Admin\Users\Edit::class)->name('admin.users.edit');
});

Route::middleware(['role:cashier'])->group(function () {
    Route::get('cashier/dashboard', \App\Livewire\Cashier\Dashboard\Index::class)->name('cashier.dashboard.index');
});