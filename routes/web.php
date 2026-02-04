<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WhatsappAccountController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\WhatsappContactController;
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

    // Contacts (Feature #3)
    Route::resource('contacts', WhatsappContactController::class);
    
    // Conversations (Feature #1)
    Route::get('conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('conversations/{contact_number}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('conversations/{contact_number}/mark-as-read', [ConversationController::class, 'markAsRead'])->name('conversations.mark-as-read');
    Route::post('conversations/{contact_number}/archive', [ConversationController::class, 'archive'])->name('conversations.archive');
    Route::post('conversations/{contact_number}/unarchive', [ConversationController::class, 'unarchive'])->name('conversations.unarchive');
    Route::post('conversations/{contact_number}/send', [ConversationController::class, 'send'])->name('conversations.send');

    // Scheduled Messages (Feature #4)
    Route::prefix('scheduled-messages')->name('scheduled-messages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'store'])->name('store');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/{scheduledMessage}', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'show'])->name('show');
        Route::get('/{scheduledMessage}/edit', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'edit'])->name('edit');
        Route::put('/{scheduledMessage}', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'update'])->name('update');
        Route::delete('/{scheduledMessage}', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'destroy'])->name('destroy');
        Route::patch('/{scheduledMessage}/cancel', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'cancel'])->name('cancel');
        Route::patch('/{scheduledMessage}/retry', [\App\Http\Controllers\Admin\ScheduledMessageController::class, 'retry'])->name('retry');
    });

    // Message Templates (Feature #5)
    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TemplateController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TemplateController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TemplateController::class, 'store'])->name('store');
        Route::get('/{template}', [\App\Http\Controllers\Admin\TemplateController::class, 'show'])->name('show');
        Route::get('/{template}/edit', [\App\Http\Controllers\Admin\TemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [\App\Http\Controllers\Admin\TemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [\App\Http\Controllers\Admin\TemplateController::class, 'destroy'])->name('destroy');
        Route::post('/{template}/duplicate', [\App\Http\Controllers\Admin\TemplateController::class, 'duplicate'])->name('duplicate');
        Route::post('/{template}/preview', [\App\Http\Controllers\Admin\TemplateController::class, 'preview'])->name('preview');
        Route::post('/{template}/submit', [\App\Http\Controllers\Admin\TemplateController::class, 'submit'])->name('submit');
        Route::post('/{template}/approve', [\App\Http\Controllers\Admin\TemplateController::class, 'approve'])->name('approve');
        Route::post('/{template}/reject', [\App\Http\Controllers\Admin\TemplateController::class, 'reject'])->name('reject');
    });
});
