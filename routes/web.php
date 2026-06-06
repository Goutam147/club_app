<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', [DashboardController::class, 'welcome'])->name('welcome');

Route::get('/clear-cache', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return 'All cache cleared successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/maintenance', function () {
    $club = \App\Models\ClubMaster::first();
    $message = \App\Models\Setting::where('key', 'maintenance_message')->value('value') 
        ?? 'Site is currently undergoing scheduled maintenance. Please check back later.';
    return view('maintenance', compact('club', 'message'));
})->name('maintenance');

// Authenticated Pages
Route::middleware(['auth'])->group(function () {
    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/transactions', [ProfileController::class, 'transactions'])->name('profile.transactions');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Member Directory
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Transactions Management
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/load', [TransactionController::class, 'load'])->name('transactions.load');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

    // Notices Board
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');

    // Events List
    Route::get('/events', [EventController::class, 'index'])->name('events.index');

    // Gallery List
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

    // User management (Anyone with manage_users permission)
    Route::middleware(['can:manage_users'])->group(function () {
        Route::get('/users/pending', [UserController::class, 'pending'])->name('users.pending');
        Route::post('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
        Route::post('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });

    // Admins (TH, President, Secretary) Restricted Routes
    Route::middleware(['role:TH|President|Secretary'])->group(function () {

        // Notices Admin Actions
        Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
        Route::put('/notices/{notice}', [NoticeController::class, 'update'])->name('notices.update');
        Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');

        // Events Admin Actions
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        // Gallery Admin Actions
        Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
        Route::delete('/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

        // Settings Actions
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/club', [SettingController::class, 'updateClub'])->name('settings.updateClub');
        Route::post('/settings/maintenance', [SettingController::class, 'toggleMaintenance'])->name('settings.toggleMaintenance');
    });

    // Transaction Approval (approve_transactions permission)
    Route::middleware(['can:approve_transactions'])->group(function () {
        Route::get('/transactions/approvals', [TransactionController::class, 'approvals'])->name('transactions.approvals');
        Route::get('/transactions/approvals/load', [TransactionController::class, 'loadPending'])->name('transactions.approvals.load');
        Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
        Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');
    });

    // Roles & Permissions management (TH only)
    Route::middleware(['role:TH'])->group(function () {
        Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
        Route::post('/roles-permissions/sync', [RolePermissionController::class, 'syncMatrix'])->name('roles-permissions.sync');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');

        // User management moved to manage_users middleware
    });
});

require __DIR__.'/auth.php';
