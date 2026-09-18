<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
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

// Authenticated Routes - SRS-02: Task Management
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('tasks.index'))->name('dashboard');
    Route::get('/lists', fn () => redirect()->route('tasks.index'))->name('lists.index');
    Route::get('/task-lists', fn () => redirect()->route('tasks.index'))->name('task-lists.index');
    Route::get('/admin/users', fn () => redirect()->route('tasks.index'))->name('admin.users');

    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
});
