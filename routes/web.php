<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Owner\RegisterController;

// Public marketing landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Owner self-service authentication & registration
Route::middleware('guest')->group(function () {
    Route::get('/owner/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('owner.login');
    Route::post('/owner/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('owner.login.submit');
    Route::get('/owner/register', [RegisterController::class, 'show'])->name('owner.register');
    Route::post('/owner/register', [RegisterController::class, 'store'])->name('owner.register.submit');
});
Route::post('/owner/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('owner.logout');

// Guest captive portal (tenant resolved by router sync token, then fallback)
Route::middleware('fallback_tenant')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PortalController::class, 'index'])->name('index');
    Route::post('process-payment', [PortalController::class, 'processPayment'])->name('process');
    Route::get('process-payment', function() { return redirect()->route('portal.index'); });
    Route::get('processing/{transactionRef}', [PortalController::class, 'processing'])->name('processing');
    Route::get('check-status/{transactionRef}', [PortalController::class, 'checkStatus']);
    Route::get('check-voucher/{code}', [PortalController::class, 'checkVoucher']);
    Route::post('restore-bundle', [PortalController::class, 'restoreBundle'])->name('restore_bundle');
    Route::post('restore-bundle-alias', [PortalController::class, 'restoreBundle'])->name('restore.bundle');
    Route::get('check-saved-session', [PortalController::class, 'checkSavedSession'])->name('check_saved_session');
    Route::get('check-saved-session-alias', [PortalController::class, 'checkSavedSession'])->name('check.saved.session');
});

// Payment webhooks + PWA manifest (stable public URLs, tenant optional)
Route::middleware('fallback_tenant')->group(function () {
    Route::post('/api/fimipay-webhook', [WebhookController::class, 'fimiPay'])->name('portal.fimipay.webhook');
    Route::post('/api/palm-pesa-webhook', [WebhookController::class, 'palmPesa']);
    Route::post('/api/sonicpesa-webhook', [WebhookController::class, 'sonicPesa'])->name('portal.sonicpesa.webhook');
    Route::get('/manifest.json', function () {
        $path = public_path('manifest.json');
        if (file_exists($path)) {
            return response()->file($path, ['Content-Type' => 'application/manifest+json; charset=utf-8']);
        }
        return response()->json(['name' => 'MedBando'], 200);
    });
});

// Mikrotik Auto-Provisioning (Polling Routes) with Tenant Token Auth
Route::middleware('mikrotik_token')->group(function () {
    Route::get('/api/mikrotik/sync/{token?}', [\App\Http\Controllers\Api\MikrotikSyncController::class, 'sync']);
    Route::get('/api/mikrotik/done/{token?}', [\App\Http\Controllers\Api\MikrotikSyncController::class, 'markDone']);
    Route::get('/api/mikrotik/login/{token?}', [\App\Http\Controllers\Api\MikrotikSyncController::class, 'onLogin']);
    Route::get('/api/mikrotik/logout/{token?}', [\App\Http\Controllers\Api\MikrotikSyncController::class, 'onLogout']);
    Route::post('/api/mikrotik/active-users-sync/{token?}', [\App\Http\Controllers\Api\MikrotikSyncController::class, 'activeUsersSync']);
    // Wall-clock expiry: MikroTik polls this every 5 mins to remove expired users
    Route::get('/api/mikrotik/expired-users/{token?}', [\App\Http\Controllers\Api\MikrotikSyncController::class, 'expiredUsers']);
    Route::get('/api/mikrotik/expired-done/{token?}', [\App\Http\Controllers\Api\MikrotikSyncController::class, 'markExpiredDone']);
    // Auto-reconnect: MikroTik inaita hii kila dakika 2, inawarudisha wateja wote wenye vocha active
    Route::get('/api/mikrotik/reconnect/{token?}', [\App\Http\Controllers\Api\MikrotikSyncController::class, 'reconnect']);
    Route::get('/api/mikrotik/reconnect-done/{token?}', [\App\Http\Controllers\Api\MikrotikSyncController::class, 'markReconnected']);
});


use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\RouterController;
use App\Http\Controllers\Admin\AccessPointController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ExpenseController;

// Admin Login Routes
Route::get('/login', fn() => redirect()->route('login'));
Route::get('/admin', fn() => redirect()->route('admin.dashboard'));
Route::get('/superadmin', fn() => redirect()->route('superadmin.dashboard'));
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Super Admin Routes (Platform Owner)
Route::middleware(['auth', 'super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('tenants', \App\Http\Controllers\SuperAdmin\TenantController::class);
    Route::post('tenants/{tenant}/impersonate', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'impersonate'])->name('tenants.impersonate');
    Route::post('leave-impersonation', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'leaveImpersonation'])->name('tenants.leave_impersonation');
    
    Route::resource('plans', \App\Http\Controllers\SuperAdmin\PlanController::class)->except(['show']);
    Route::resource('subscriptions', \App\Http\Controllers\SuperAdmin\SubscriptionController::class);
});

// Protected Admin Routes (Tenant Admin)
Route::middleware(['auth', 'tenant', \App\Http\Middleware\RequireActiveSubscription::class])->group(function () {
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    
    Route::get('/admin/vouchers', [VoucherController::class, 'index'])->name('admin.vouchers.index');
    Route::post('/admin/vouchers/generate-batch', [VoucherController::class, 'generateBatch'])->name('admin.vouchers.generateBatch');
    Route::post('/admin/vouchers/generate-sms', [VoucherController::class, 'generateSingleSms'])->name('admin.vouchers.generateSms');
    Route::get('/admin/vouchers/print/{batchId}', [VoucherController::class, 'printPdf'])->name('admin.vouchers.print');
    Route::get('/admin/vouchers/batch/{batchId}/json', [VoucherController::class, 'batchJson'])->name('admin.vouchers.batchJson');
    
    // Fallback for the quick cash voucher in dashboard
    Route::post('/admin/vouchers/generate', [\App\Http\Controllers\Admin\ManualVoucherController::class, 'generate'])->name('admin.vouchers.generate');
    
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/recent-transactions', [DashboardController::class, 'recentTransactionsJson'])->name('admin.dashboard.recent_transactions');
    Route::get('/admin/notifications/check', [DashboardController::class, 'checkNotifications']);
    
    // Notification Center (Kituo cha Taarifa)
    Route::get('/admin/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/admin/notifications/{id}/go', [\App\Http\Controllers\Admin\NotificationController::class, 'goToNotification'])->name('admin.notifications.go');
    Route::post('/admin/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::post('/admin/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark_all_read');
    Route::post('/admin/notifications/bulk-delete', [\App\Http\Controllers\Admin\NotificationController::class, 'bulkDelete'])->name('admin.notifications.bulk_delete');
    Route::post('/admin/notifications/bulk-read', [\App\Http\Controllers\Admin\NotificationController::class, 'bulkRead'])->name('admin.notifications.bulk_read');
    Route::delete('/admin/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::post('/admin/notifications/clear-all', [\App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])->name('admin.notifications.clear_all');
    Route::get('/admin/notifications/latest-json', [\App\Http\Controllers\Admin\NotificationController::class, 'latestJson'])->name('admin.notifications.latest_json');
    
    // Live Sessions
    Route::get('/admin/live-sessions', [\App\Http\Controllers\Admin\LiveSessionController::class, 'index'])->name('admin.live_sessions');
    Route::get('/admin/live-sessions/data', [\App\Http\Controllers\Admin\LiveSessionController::class, 'data'])->name('admin.live_sessions.data');

    Route::resource('admin/packages', PackageController::class, ['as' => 'admin']);
    Route::post('/admin/packages/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('admin.packages.toggle-status');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');

    Route::get('/admin/transactions', [TransactionController::class, 'index'])->name('admin.transactions');
    Route::resource('admin/expenses', ExpenseController::class, ['as' => 'admin'])->except(['show', 'create', 'edit']);
    
    // Settings
    Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings/reset-sales', [SettingsController::class, 'resetSales'])->name('admin.settings.reset_sales');
    Route::post('/admin/settings/set-sales', [SettingsController::class, 'setSales'])->name('admin.settings.set_sales');
    Route::get('/admin/settings/payment', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'index'])->name('admin.settings.payment');
    Route::post('/admin/settings/payment', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'update'])->name('admin.settings.payment.update');
    Route::get('/admin/mikrotik/status', [SettingsController::class, 'mikrotikStatus'])->name('admin.mikrotik.status');

    // SaaS Billing
    Route::get('/admin/billing', [\App\Http\Controllers\Admin\BillingController::class, 'index'])->name('admin.billing');
    Route::post('/admin/billing/pay', [\App\Http\Controllers\Admin\BillingController::class, 'pay'])->name('admin.billing.pay');
    Route::get('/admin/billing/processing/{id}', [\App\Http\Controllers\Admin\BillingController::class, 'processing'])->name('admin.billing.processing');
    Route::get('/admin/billing/status/{id}', [\App\Http\Controllers\Admin\BillingController::class, 'status'])->name('admin.billing.status');

    // Profile routes
    Route::get('/admin/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');
    
    // Sites
    Route::resource('admin/sites', SiteController::class, ['as' => 'admin']);
    
    // Routers (MikroTik)
    Route::get('/admin/routers/download-login-template', [RouterController::class, 'downloadLoginHtml'])->name('admin.routers.download_login');
    Route::resource('admin/routers', RouterController::class, ['as' => 'admin']);
    Route::get('/admin/routers/{router}/provision', [RouterController::class, 'provision'])->name('admin.routers.provision');

    // Access Points (manual entry + AI sticker scan adoption)
    Route::get('/admin/access-points', [AccessPointController::class, 'index'])->name('admin.access_points.index');
    Route::get('/admin/access-points/create', [AccessPointController::class, 'create'])->name('admin.access_points.create');
    Route::post('/admin/access-points', [AccessPointController::class, 'store'])->name('admin.access_points.store');
    Route::post('/admin/access-points/scan', [AccessPointController::class, 'scan'])->name('admin.access_points.scan');
    Route::get('/admin/access-points/{accessPoint}/edit', [AccessPointController::class, 'edit'])->name('admin.access_points.edit');
    Route::put('/admin/access-points/{accessPoint}', [AccessPointController::class, 'update'])->name('admin.access_points.update');
    Route::post('/admin/access-points/{accessPoint}/adopt', [AccessPointController::class, 'adopt'])->name('admin.access_points.adopt');
    Route::delete('/admin/access-points/{accessPoint}', [AccessPointController::class, 'destroy'])->name('admin.access_points.destroy');

    // Customers
    Route::resource('admin/customers', CustomerController::class, ['as' => 'admin']);
    Route::post('/admin/customers/{customer}/suspend',  [CustomerController::class, 'suspend'])->name('admin.customers.suspend');
    Route::post('/admin/customers/{customer}/activate', [CustomerController::class, 'activate'])->name('admin.customers.activate');
    Route::post('/admin/customers/{customer}/debt', [CustomerController::class, 'addDebt'])->name('admin.customers.debt');
    Route::post('/admin/customers/{customer}/payment', [CustomerController::class, 'addPayment'])->name('admin.customers.payment');

    // Global Customer Search (JSON)
    Route::get('/admin/customers-search', [CustomerController::class, 'search'])->name('admin.customers.search');

    // Subscriptions
    Route::get('/admin/subscriptions', [SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
    Route::post('/admin/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('admin.subscriptions.cancel');
});
