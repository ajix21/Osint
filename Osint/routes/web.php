<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Root — redirect based on auth state
Route::get('/', function () {
    return auth()->check() ? redirect()->route('search') : redirect()->route('login');
});

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Main search (operator + admin + viewer — viewer can only view results)
    Route::get('/search',  [SearchController::class, 'index'])->name('search');
    Route::post('/search', [SearchController::class, 'query'])->name('search.query')
         ->middleware('role:admin,operator');

    // Search history
    Route::get('/history', [SearchController::class, 'history'])->name('search.history');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users',          [AdminController::class, 'users'])->name('users');
    Route::get('/users/create',   [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users',         [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit',[AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}',     [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}',  [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/logs',           [AdminController::class, 'logs'])->name('logs');
});
