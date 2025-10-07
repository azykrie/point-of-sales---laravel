<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');



Route::middleware(['role:admin'])->group(function () {
    Route::get('admin/dashboard', \App\Livewire\Admin\Dashboard\Index::class)->name('admin.dashboard.index');
    Route::get('admin/users', \App\Livewire\Admin\Users\Index::class)->name('admin.users.index');
    Route::get('admin/users/create', \App\Livewire\Admin\Users\Create::class)->name('admin.users.create');
    Route::get('admin/users/{id}/edit', \App\Livewire\Admin\Users\Edit::class)->name('admin.users.edit');
});

Route::middleware(['role:user'])->group(function () {
    Route::get('user/dashboard', \App\Livewire\Users\Dashboard\Index::class)->name('user.dashboard.index');
});