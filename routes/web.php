<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
Route::middleware(['auth'])->group(function () {
    Route::resource('clientes', ClienteController::class)->except(['show']);
});
