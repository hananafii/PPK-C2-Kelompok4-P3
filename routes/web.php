<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskListController;
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

// Authenticated Routes - SRS-01: Task List Management
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('task-lists.index'))->name('dashboard');
    Route::get('/lists', fn () => redirect()->route('task-lists.index'))->name('lists.index');
    Route::get('/tasks', fn () => redirect()->route('task-lists.index'))->name('tasks.index');
    Route::get('/tasks/create', fn () => redirect()->route('task-lists.create'))->name('tasks.create');
    Route::get('/admin/users', fn () => redirect()->route('task-lists.index'))->name('admin.users');

    Route::resource('task-lists', TaskListController::class);
});
