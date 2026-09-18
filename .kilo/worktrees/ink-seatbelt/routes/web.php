<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListMemberController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskListController;

// Public Homepage / Landing
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

// Logout Route
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', fn () => redirect()->route('tasks.index'))->name('dashboard');

    // SRS-02: Task Management
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

    // Collaboration: Task List Members
    Route::get('/task-lists/{taskList}/members', [TaskListMemberController::class, 'index'])->name('task-lists.members.index');
    Route::post('/task-lists/{taskList}/members', [TaskListMemberController::class, 'store'])->name('task-lists.members.store');
    Route::delete('/task-lists/{taskList}/members/{user}', [TaskListMemberController::class, 'destroy'])->name('task-lists.members.destroy');
    Route::get('/task-lists/{taskList}', function ($taskList) {
        return redirect()->route('tasks.index', ['task_list_id' => is_object($taskList) ? $taskList->id : $taskList]);
    })->name('task-lists.show');

    // Admin Users (AdminUserController)
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

// Admin User Management (SRS-04 - UserController)
Route::prefix('admin')->group(function () {
    Route::get('/manage-users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/manage-users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/manage-users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('task-lists', TaskListController::class)->parameters([
        'task-lists' => 'taskList',
    ]);
});

// Authentication routes akan diintegrasikan dari branch User Management.