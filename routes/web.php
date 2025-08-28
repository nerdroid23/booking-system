<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EmployeeShowController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, '__invoke'])->name('home');

Route::get('/employees/{employee:slug}', [EmployeeShowController::class, '__invoke'])->name('employees.show');

Route::get('/checkout/{service:slug}/{employee:slug}', [CheckoutController::class, '__invoke'])
    ->name('checkout')
    ->scopeBindings();

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
