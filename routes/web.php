<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Fetch dynamic version from Git
    $gitVersion = \App\Helpers\SystemHelper::getSystemVersion();

    return view('welcome', compact('gitVersion'));
});

Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications/latest', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.latest');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all');

    // --- Shared POS/Sales Resources (Admin, Pharmacist, Cashier) ---
    Route::middleware(['role:admin,pharmacist,cashier'])->group(function () {
        Route::resource('shifts', \App\Http\Controllers\ShiftController::class)->only(['create', 'store', 'update']);
        Route::get('/shifts/my-history', [\App\Http\Controllers\ShiftController::class, 'myShifts'])->name('shifts.my_index');
        Route::get('/shifts/{shift}/print', [\App\Http\Controllers\ShiftController::class, 'print'])->name('shifts.print');
        Route::resource('sales', \App\Http\Controllers\SalesController::class)->only(['index', 'show']);
        Route::resource('cashier', \App\Http\Controllers\CashierController::class)->only(['index', 'show', 'update'])->parameters(['cashier' => 'sale']);

        // Patients
        Route::get('/patients/search', [\App\Http\Controllers\PatientController::class, 'search'])->name('patients.search');
        Route::post('/patients/api/store', [\App\Http\Controllers\PatientController::class, 'apiStore'])->name('patients.api.store');
    });

    // --- Active POS Interface (Admin & Pharmacist Only) ---
    Route::middleware(['role:admin,pharmacist', 'shift.open'])->group(function () {
        Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'store'])->name('pos.store');
        Route::post('/pos/check-interactions', [\App\Http\Controllers\PosController::class, 'checkInteractions'])->name('pos.check-interactions');
    });

    // Receipt (Accessible if you have reference)
    Route::get('/pos/receipt/{sale}', [\App\Http\Controllers\PosController::class, 'receipt'])
        ->middleware(['auth'])
        ->name('pos.receipt');

    // --- Clinical & Inventory (Admin, Pharmacist) ---
    Route::middleware(['role:admin,pharmacist'])->group(function () {
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);

        // Product Management
        // Products & Inventory
        Route::get('products/import', [\App\Http\Controllers\ProductController::class, 'importForm'])->name('products.import');
        Route::get('products/import/template', [\App\Http\Controllers\ProductController::class, 'downloadTemplate'])->name('products.import.template');
        Route::post('products/import', [\App\Http\Controllers\ProductController::class, 'processImport'])->name('products.import.store');
        Route::get('products/lookup', [\App\Http\Controllers\ProductController::class, 'lookup'])->name('products.lookup');
        Route::resource('products', \App\Http\Controllers\ProductController::class);

        Route::get('inventory/history', [\App\Http\Controllers\InventoryController::class, 'history'])->name('inventory.history');
        Route::resource('inventory', \App\Http\Controllers\InventoryController::class);

        // Clinical
        Route::get('/patients/{patient}/loyalty', [\App\Http\Controllers\PatientController::class, 'loyaltyHistory'])->name('patients.loyalty');
        Route::resource('patients', \App\Http\Controllers\PatientController::class);
        Route::resource('prescriptions', \App\Http\Controllers\PrescriptionController::class);
        Route::post('/prescriptions/{prescription}/dispense', [\App\Http\Controllers\PrescriptionController::class, 'dispense'])->name('prescriptions.dispense');
        Route::post('/drug-interactions/sync', [\App\Http\Controllers\DrugInteractionController::class, 'sync'])->name('drug-interactions.sync');
        Route::post('/prescriptions/{prescription}/refill', [\App\Http\Controllers\PrescriptionController::class, 'refill'])->name('prescriptions.refill');
        Route::resource('drug-interactions', \App\Http\Controllers\DrugInteractionController::class);

        // Procurement
        Route::get('procurement/orders', [\App\Http\Controllers\ProcurementController::class, 'index'])->name('procurement.orders.index');
        Route::get('procurement/orders/create', [\App\Http\Controllers\ProcurementController::class, 'create'])->name('procurement.orders.create');
        Route::post('procurement/orders', [\App\Http\Controllers\ProcurementController::class, 'store'])->name('procurement.orders.store');
        Route::get('procurement/orders/{order}/print', [\App\Http\Controllers\ProcurementController::class, 'print'])->name('procurement.orders.print');
        Route::get('procurement/orders/{order}', [\App\Http\Controllers\ProcurementController::class, 'show'])->name('procurement.orders.show');
        Route::post('procurement/orders/{order}/receive', [\App\Http\Controllers\ProcurementController::class, 'receive'])->name('procurement.orders.receive');
        Route::get('procurement/orders/{order}', [\App\Http\Controllers\ProcurementController::class, 'show'])->name('procurement.orders.show');
        Route::post('procurement/orders/{order}/receive', [\App\Http\Controllers\ProcurementController::class, 'receive'])->name('procurement.orders.receive');
    });

    // --- Refunds (Split Access) ---
    Route::middleware(['auth'])->group(function () {
        // Cashier/Pharmacist can request
        Route::post('/sales/{sale}/refund', [\App\Http\Controllers\RefundController::class, 'store'])->name('refunds.store');
    });

    // --- Administration (Admin Only) ---
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/refunds', [\App\Http\Controllers\RefundController::class, 'index'])->name('admin.refunds.index');
        Route::post('/admin/refunds/{refund}/approve', [\App\Http\Controllers\RefundController::class, 'approve'])->name('admin.refunds.approve');
        Route::post('/admin/refunds/{refund}/reject', [\App\Http\Controllers\RefundController::class, 'reject'])->name('admin.refunds.reject');

        Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');
        Route::get('/support', [\App\Http\Controllers\SupportController::class, 'index'])->name('support.index');

        Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);

        // Settings
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/update-software', [\App\Http\Controllers\SettingController::class, 'updateSoftware'])->name('settings.system_update');

        Route::resource('branches', \App\Http\Controllers\BranchController::class);
        Route::resource('users', \App\Http\Controllers\UserController::class);

        Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');

        // CRM
        Route::prefix('crm')->name('admin.crm.')->group(function () {
            Route::get('/', [\App\Http\Controllers\CrmController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\CrmController::class, 'create'])->name('create');
            Route::post('/store', [\App\Http\Controllers\CrmController::class, 'store'])->name('store');
            Route::get('/{campaign}', [\App\Http\Controllers\CrmController::class, 'show'])->name('show');
        });

        // Backups
        Route::get('/backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/create', [\App\Http\Controllers\BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{filename}/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{filename}', [\App\Http\Controllers\BackupController::class, 'delete'])->name('backups.delete');

        // Shift Reports
        Route::get('/shifts/reports', [\App\Http\Controllers\ShiftController::class, 'index'])->name('admin.shifts.index');
        Route::get('/shifts/reports/{shift}', [\App\Http\Controllers\ShiftController::class, 'show'])->name('admin.shifts.show');

        // Financials
        Route::prefix('financials')->name('admin.financials.')->group(function () {
            Route::get('/', [\App\Http\Controllers\FinancialReportController::class, 'index'])->name('index');
            Route::get('/sales', [\App\Http\Controllers\FinancialReportController::class, 'sales'])->name('sales');
            Route::get('/inventory', [\App\Http\Controllers\FinancialReportController::class, 'inventory'])->name('inventory');
            Route::get('/profit', [\App\Http\Controllers\FinancialReportController::class, 'profit'])->name('profit');
        });

        // Audit Logs
        Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
        // System Health
        Route::get('/system-health', [\App\Http\Controllers\SystemHealthController::class, 'index'])->name('admin.system-health');
        Route::post('/system-health/toggle-debug', [\App\Http\Controllers\SystemHealthController::class, 'toggleDebug'])->name('admin.system-health.toggle-debug');
    });
});

require __DIR__ . '/auth.php';
