<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WhatsappAccountController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // WhatsApp Accounts Management
    Route::resource('accounts', WhatsappAccountController::class);
    Route::get('accounts/{account}/connect', [WhatsappAccountController::class, 'connect'])->name('accounts.connect');
    Route::get('accounts/{account}/check-status', [WhatsappAccountController::class, 'checkStatus'])->name('accounts.check-status');
    Route::post('accounts/{account}/initialize', [WhatsappAccountController::class, 'initialize'])->name('accounts.initialize');
    Route::post('accounts/{account}/disconnect', [WhatsappAccountController::class, 'disconnect'])->name('accounts.disconnect');
    Route::post('accounts/{account}/regenerate-keys', [WhatsappAccountController::class, 'regenerateKeys'])->name('accounts.regenerate');
    
    // Messages
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
});
