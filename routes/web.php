<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\WhatsappAccountController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\WhatsappContactController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

// Webhook for Meta WhatsApp
Route::match(['get', 'post'], '/webhook/meta', [\App\Http\Controllers\WebhookController::class, 'verify'])->name('webhook.meta');

// Xendit Payment Webhook
Route::post('/webhook/xendit', [\App\Http\Controllers\XenditWebhookController::class, 'handle'])->name('webhook.xendit');

// Pricing page
Route::get('/pricing', [\App\Http\Controllers\PricingController::class, 'index'])->name('pricing');
Route::post('/pricing/calculate', [\App\Http\Controllers\PricingController::class, 'calculator'])->name('pricing.calculate');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLink'])->name('password.send-reset-link');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Subscription Routes (Protected)
Route::middleware('auth')->prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('index');
    Route::post('/subscribe/{plan}', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::get('/success', [\App\Http\Controllers\SubscriptionController::class, 'success'])->name('success');
    Route::get('/failed', [\App\Http\Controllers\SubscriptionController::class, 'failed'])->name('failed');
    Route::get('/my-subscription', [\App\Http\Controllers\SubscriptionController::class, 'show'])->name('show');
    Route::post('/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('cancel');
});

// Admin Routes (Protected)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'check.subscription'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // WhatsApp Accounts Management
    Route::resource('accounts', WhatsappAccountController::class);
    Route::get('accounts/{account}/verify', [WhatsappAccountController::class, 'verify'])->name('accounts.verify');
    Route::post('accounts/{account}/request-code', [WhatsappAccountController::class, 'requestCode'])->name('accounts.request-code');
    Route::post('accounts/{account}/verify-code', [WhatsappAccountController::class, 'verifyCode'])->name('accounts.verify-code');
    Route::post('accounts/{account}/disconnect', [WhatsappAccountController::class, 'disconnect'])->name('accounts.disconnect');
    Route::post('accounts/{account}/regenerate-keys', [WhatsappAccountController::class, 'regenerateKeys'])->name('accounts.regenerate');
    Route::get('accounts/{account}/webhook-setup', [\App\Http\Controllers\Admin\WebhookSetupController::class, 'show'])->name('accounts.webhook-setup');
    Route::post('accounts/{account}/webhook-regenerate', [\App\Http\Controllers\Admin\WebhookSetupController::class, 'regenerateToken'])->name('accounts.webhook-regenerate');
    Route::get('accounts/{account}/webhook-test', [\App\Http\Controllers\Admin\WebhookSetupController::class, 'test'])->name('accounts.webhook-test');
    
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

    // Analytics (Feature #6)
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/test-email', [\App\Http\Controllers\Admin\SettingsController::class, 'sendTestEmail'])->name('settings.test-email');
    
    // User Management (Admin Only)
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Subscription Plans
    Route::resource('subscription-plans', \App\Http\Controllers\Admin\SubscriptionPlanController::class);
    Route::patch('subscription-plans/{subscriptionPlan}/toggle-status', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'toggleStatus'])->name('subscription-plans.toggle-status');});

// Team Members Management (requires subscription)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'check.subscription'])->group(function () {
    Route::resource('team-members', \App\Http\Controllers\Admin\TeamMemberController::class);
});

// Pending Invitations (auth only, no subscription required for invited users)
Route::get('/admin/pending-invitations', [\App\Http\Controllers\Admin\TeamMemberController::class, 'pendingInvitations'])->middleware('auth')->name('admin.pending-invitations');

// Team Member Invitation Routes (Public)
Route::post('/team-invite/{token}/accept', [\App\Http\Controllers\Admin\TeamMemberController::class, 'acceptInvitation'])->middleware('auth')->name('team-members.accept');
Route::post('/team-invite/{token}/reject', [\App\Http\Controllers\Admin\TeamMemberController::class, 'rejectInvitation'])->middleware('auth')->name('team-members.reject');
