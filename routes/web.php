<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ApiLogController;

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/', function () {
    $footer = \App\Models\Footer::first();
    $stats = [
        'katalog' => \App\Models\ServiceCatalog::count(),
        'endpoint' => \App\Models\ServiceEndpoint::count(),
        'total_request' => \App\Models\ApiLog::count(),
    ];
    return view('landing.index', compact('footer', 'stats'));
})->name('home');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::get('/register', [\App\Http\Controllers\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\RegisterController::class, 'register'])->name('register.post');
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard')->middleware(['auth', 'verified']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/account-reset', [AuthController::class, 'resetAccount'])->name('account.reset')->middleware('auth');

// Forgot Password Routes
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
Route::get('/pending-approval', function () {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.pages.pages-misc-pending-approval', ['pageConfigs' => $pageConfigs]);
})->name('pages-pending-approval');


Route::middleware('auth')->group(function () {
    Route::get('api-clients', App\Livewire\User\ApiClientManager::class)->name('api-clients.index');
    Route::get('/profile', App\Livewire\UserProfile::class)->name('pages-profile-user');
    Route::get('/profile/activity', App\Livewire\UserActivity::class)->name('pages-profile-activity');
    // API Logs
    Route::get('api-logs', [ApiLogController::class, 'index'])->name('api-logs.index');
    Route::get('api-logs/endpoints/{catalogId}', [ApiLogController::class, 'getEndpoints'])->name('api-logs.endpoints');
    Route::get('api-logs/{catalog}', [ApiLogController::class, 'show'])->name('api-logs.show');
    Route::get('documentation', [\App\Http\Controllers\DocumentationController::class, 'index'])->name('documentation.index');

    // Support Tickets (User)
    Route::get('tickets', [\App\Http\Controllers\SupportTicketController::class, 'index'])->name('tickets.index');
    Route::post('tickets', [\App\Http\Controllers\SupportTicketController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{id}', [\App\Http\Controllers\SupportTicketController::class, 'show'])->name('tickets.show');

    // Notifications
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Layanan Saya (Service Proposal)
    Route::get('/my-services', App\Livewire\User\MyServiceList::class)->name('user.my-services.index');
    Route::get('/my-services/incoming-requests', App\Livewire\User\IncomingRequestTable::class)->name('user.incoming-requests');
    Route::get('/my-services/{service}', App\Livewire\User\MyServiceDetail::class)->name('user.my-services.show');

    // Status Permohonan Saya (Consumer)
    Route::get('/access-requests', App\Livewire\User\AccessRequestList::class)->name('user.access-requests.index');

    // Global Search (JSON for Typeahead)
    Route::get('/global-search', [\App\Http\Controllers\GlobalSearchController::class, 'search'])->name('global-search');
});

// Admin Routes (Super Admin Only)
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:admin']], function () {
    Route::get('/confirm-registrations', [\App\Http\Controllers\AdminController::class, 'confirmRegistrations'])->name('admin.confirm-registrations');
    Route::get('/rejected-registrations', [\App\Http\Controllers\AdminController::class, 'rejectedRegistrations'])->name('admin.rejected-registrations');
    Route::post('/approve/{id}', [\App\Http\Controllers\AdminController::class, 'approveUser'])->name('admin.approve-user');
    Route::post('/reject/{id}', [\App\Http\Controllers\AdminController::class, 'rejectUser'])->name('admin.reject-user');

    // Master Data
    Route::resource('agency', \App\Http\Controllers\AgencyController::class);
    Route::get('agency-helper/{slug}/list', [\App\Http\Controllers\AgencyController::class, 'getExternalReferenceList'])->name('agency.external-list');
    Route::resource('users', \App\Http\Controllers\UserController::class);

    // Access Requests
    Route::get('access-requests', [\App\Http\Controllers\AccessRequestController::class, 'index'])->name('access-requests.index');
    Route::post('access-requests/{id}/approve', [\App\Http\Controllers\AccessRequestController::class, 'approve'])->name('access-requests.approve');
    Route::post('access-requests/{id}/reject', [\App\Http\Controllers\AccessRequestController::class, 'reject'])->name('access-requests.reject');
    Route::get('access-requests/{id}/download', [\App\Http\Controllers\AccessRequestController::class, 'download'])->name('access-requests.download');
    Route::post('access-requests/bulk-approve', [\App\Http\Controllers\AccessRequestController::class, 'bulkApprove'])->name('access-requests.bulk-approve');

    // Role & Permissions
    Route::get('roles-permissions', \App\Livewire\Admin\RolePermissionManager::class)->name('roles-permissions.index');
    // Route::post('roles-permissions', [\App\Http\Controllers\RolePermissionController::class, 'update'])->name('roles-permissions.update'); // Deprecated

    // Support Tickets (Admin)
    Route::get('tickets', [\App\Http\Controllers\AdminTicketController::class, 'index'])->name('admin.tickets.index');
    Route::get('tickets/{id}', [\App\Http\Controllers\AdminTicketController::class, 'show'])->name('admin.tickets.show');
    Route::post('tickets/{id}/reply', [\App\Http\Controllers\AdminTicketController::class, 'reply'])->name('admin.tickets.reply');

    // Announcements
    Route::get('announcements', \App\Livewire\Admin\AnnouncementManager::class)->name('admin.announcements.index');

    // Landing Page Settings (Protected by manage_landing_page permission via Component internal check or middleware)
    Route::get('landing/footer', \App\Livewire\Admin\Landing\FooterManager::class)
        ->name('admin.landing.footer')
        ->middleware('permission:manage_landing_page');

    // Utilities
    // Route::get('code-generator', \App\Livewire\Admin\CodeGenerator::class)->name('admin.code-generator');
});

Route::middleware(['auth', 'permission:use_code_generator'])->group(function () {
    Route::get('admin/code-generator', \App\Livewire\Admin\CodeGenerator::class)->name('admin.code-generator');
});

// Service Catalog Routes (Admin, Dinas, User)
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    // Read Access (All Roles)
    Route::post('service-catalogs/request-access', [\App\Http\Controllers\ServiceCatalogController::class, 'requestAccess'])->name('service-catalogs.request-access');
    Route::get('service-catalogs', [\App\Http\Controllers\ServiceCatalogController::class, 'index'])->name('service-catalogs.index');

    // Admin Service Verification
    Route::get('service-verification', [\App\Http\Controllers\AdminController::class, 'serviceVerification'])->name('admin.service-verification')->middleware('role:admin');
    Route::get('template-manager', [\App\Http\Controllers\AdminController::class, 'templateManager'])->name('admin.template-manager')->middleware('role:admin');

    Route::get('service-catalogs/{id}/references', [\App\Http\Controllers\ServiceCatalogController::class, 'getReferences'])->name('service-catalogs.references');
    Route::get('service-catalogs/{service_catalog}', [\App\Http\Controllers\ServiceCatalogController::class, 'show'])->name('service-catalogs.show');

    // Endpoint Detail & Test (Shared)
    Route::get('service-catalogs/{catalog}/endpoint/{id}/detail', [\App\Http\Controllers\ServiceCatalogController::class, 'showEndpointDetail'])->name('service-catalogs.endpoint.detail');
    Route::post('service-catalogs/{catalog}/endpoint/{id}/test', [\App\Http\Controllers\ServiceCatalogController::class, 'testEndpoint'])->name('service-catalogs.endpoint.test');

    // Write Access (Admin Only)
    Route::group(['middleware' => ['role:admin']], function () {
        Route::post('service-catalogs', [\App\Http\Controllers\ServiceCatalogController::class, 'store'])->name('service-catalogs.store');
        // Route::get('service-catalogs/{service_catalog}/edit', [\App\Http\Controllers\ServiceCatalogController::class, 'edit'])->name('service-catalogs.edit');
        // Route::put('service-catalogs/{service_catalog}', [\App\Http\Controllers\ServiceCatalogController::class, 'update'])->name('service-catalogs.update');
        Route::delete('service-catalogs/{service_catalog}', [\App\Http\Controllers\ServiceCatalogController::class, 'destroy'])->name('service-catalogs.destroy');
        // Route::post('service-catalogs/{service_catalog}/toggle-status', [\App\Http\Controllers\ServiceCatalogController::class, 'toggleStatus'])->name('service-catalogs.toggle-status');

        // Endpoint Management (Write) - Migrated to Livewire
        // Route::post('service-catalogs/endpoint', [\App\Http\Controllers\ServiceCatalogController::class, 'storeEndpoint'])->name('service-catalogs.endpoint.store');
        // Route::put('service-catalogs/endpoint/{id}', [\App\Http\Controllers\ServiceCatalogController::class, 'updateEndpoint'])->name('service-catalogs.endpoint.update');
        // Route::delete('service-catalogs/endpoint/{id}', [\App\Http\Controllers\ServiceCatalogController::class, 'destroyEndpoint'])->name('service-catalogs.endpoint.destroy');
    });
});

// Gateway Route
Route::any('/api/{slug}/{path?}', [App\Http\Controllers\GatewayController::class, 'handle'])
    ->where('path', '.*')
    ->name('gateway.handle');


Route::get('/debug-chart', function () {
    $user = \App\Models\User::first();
    auth()->login($user);

    $query = \App\Models\ApiLog::query();
    if (!$user->hasRole('admin')) {
        $query->where('user_id', $user->id);
    }

    $logs = $query->where('created_at', '>=', now()->subDays(7))->get();

    return [
        'user' => $user->name,
        'role' => $user->getRoleNames(),
        'count' => $logs->count(),
        'sample' => $logs->take(1)->first(),
        'first_log_user_id' => $logs->first()?->user_id,
        'current_user_id' => $user->id
    ];
});
