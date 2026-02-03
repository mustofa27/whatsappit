<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WhatsappAccountController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ConversationController;
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
    Route::get('accounts/{account}/verify', [WhatsappAccountController::class, 'verify'])->name('accounts.verify');
    Route::post('accounts/{account}/request-code', [WhatsappAccountController::class, 'requestCode'])->name('accounts.request-code');
    Route::post('accounts/{account}/verify-code', [WhatsappAccountController::class, 'verifyCode'])->name('accounts.verify-code');
    Route::post('accounts/{account}/disconnect', [WhatsappAccountController::class, 'disconnect'])->name('accounts.disconnect');
    Route::post('accounts/{account}/regenerate-keys', [WhatsappAccountController::class, 'regenerateKeys'])->name('accounts.regenerate');
    
    // Messages
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    
    // Conversations (Feature #1)
    Route::get('conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('conversations/{contact_number}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('conversations/{contact_number}/mark-as-read', [ConversationController::class, 'markAsRead'])->name('conversations.mark-as-read');
    Route::post('conversations/{contact_number}/archive', [ConversationController::class, 'archive'])->name('conversations.archive');
    Route::post('conversations/{contact_number}/unarchive', [ConversationController::class, 'unarchive'])->name('conversations.unarchive');
    Route::post('conversations/{contact_number}/send', [ConversationController::class, 'send'])->name('conversations.send');
});
