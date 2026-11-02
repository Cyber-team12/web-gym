<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/test', fn () => 'Halaman khusus admin')->name('test');
    });
});