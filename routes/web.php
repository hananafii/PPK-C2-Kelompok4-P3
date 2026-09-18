<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Homepage
Route::get('/', function () {
    return view('home');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store']);
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerStore']);
});

// Logout
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

// Authenticated Routes - SRS-04: Admin & User Management
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.users'))->name('dashboard');
    Route::get('/lists', fn () => redirect()->route('admin.users'))->name('lists.index');
    Route::get('/task-lists', fn () => redirect()->route('admin.users'))->name('task-lists.index');
    Route::get('/tasks', fn () => redirect()->route('admin.users'))->name('tasks.index');
    Route::get('/tasks/create', fn () => redirect()->route('admin.users'))->name('tasks.create');

    // Admin Users (AdminUserController)
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

// Admin User Management (UserController)
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/manage-users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/manage-users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/manage-users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
