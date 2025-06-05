<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// Route untuk kasir
Route::get('/', [KasirController::class, 'index'])->name('kasir');
Route::post('/scan', [KasirController::class, 'scan'])->name('scan');
Route::post('/checkout', [KasirController::class, 'checkout'])->name('checkout');
Route::get('/api/products/{barcode}', [KasirController::class, 'getProductByBarcode']);

// Route untuk admin
Route::get('/admin', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('/admin/produk', AdminController::class)->except(['create', 'edit']);
    Route::get('/admin/historis', [AdminController::class, 'historis'])->name('historis');
    Route::get('/admin/prediksi', [AdminController::class, 'prediksi'])->name('prediksi');
    Route::post('/admin/prediksi', [AdminController::class, 'generate'])->name('prediksi.generate');
});