<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/support', [\App\Http\Controllers\SupportController::class, 'index'])->name('support.index');

    // Phase 3: Inventory Management
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('inventory', \App\Http\Controllers\InventoryController::class);

    // Phase 4: POS & Shift Management
    Route::resource('shifts', \App\Http\Controllers\ShiftController::class)->only(['create', 'store', 'update']);

    Route::middleware('shift.open')->group(function () {
        Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'store'])->name('pos.store');
        Route::post('/pos/check-interactions', [\App\Http\Controllers\PosController::class, 'checkInteractions'])->name('pos.check-interactions');
    });

    Route::get('/pos/receipt/{sale}', [\App\Http\Controllers\PosController::class, 'receipt'])->name('pos.receipt');

    // Phase 12: Expense Management
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    // Phase 5: Clinical
    Route::get('/patients/search', [\App\Http\Controllers\PatientController::class, 'search'])->name('patients.search');
    Route::post('/patients/api/store', [\App\Http\Controllers\PatientController::class, 'apiStore'])->name('patients.api.store');
    Route::resource('patients', \App\Http\Controllers\PatientController::class);
    Route::resource('prescriptions', \App\Http\Controllers\PrescriptionController::class);
    Route::post('/prescriptions/{prescription}/dispense', [\App\Http\Controllers\PrescriptionController::class, 'dispense'])->name('prescriptions.dispense');

    // Phase 13: Clinical Safety
    Route::resource('drug-interactions', \App\Http\Controllers\DrugInteractionController::class);

    // Phase 14: Procurement
    Route::get('procurement/orders', [\App\Http\Controllers\ProcurementController::class, 'index'])->name('procurement.orders.index');
    Route::get('procurement/orders/create', [\App\Http\Controllers\ProcurementController::class, 'create'])->name('procurement.orders.create');
    Route::post('procurement/orders', [\App\Http\Controllers\ProcurementController::class, 'store'])->name('procurement.orders.store');
    Route::get('procurement/orders/{order}', [\App\Http\Controllers\ProcurementController::class, 'show'])->name('procurement.orders.show');
    Route::post('procurement/orders/{order}/receive', [\App\Http\Controllers\ProcurementController::class, 'receive'])->name('procurement.orders.receive');

    // Phase 15: Analytics
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
});

require __DIR__ . '/auth.php';
