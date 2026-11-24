<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('clientes', ClienteController::class)->except(['show']);
    Route::resource('comprobantes', ComprobanteController::class)->only(['index', 'create', 'store']);
});

require __DIR__.'/settings.php';
