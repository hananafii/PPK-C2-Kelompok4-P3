<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\TaskListMemberController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

// Legacy Project Collaboration Routes
Route::get('/projects/{project}', [ProjectController::class, 'legacyShow'])->name('projects.show');
Route::post('/projects/{project}/members', [ProjectMemberController::class, 'legacyStore'])->name('projects.members.store');
Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'legacyDestroy'])->name('projects.members.destroy');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', fn () => redirect()->route('tasks.index'))->name('dashboard');

    // SRS-02: Task Management
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

    // Collaboration: Project / Lists
    Route::get('/lists', [ProjectController::class, 'index'])->name('lists.index');
    Route::post('/lists', [ProjectController::class, 'store'])->name('lists.store');
    Route::get('/lists/{project}', [ProjectController::class, 'show'])->name('lists.show');
    Route::post('/lists/{project}/tasks', [TaskController::class, 'storeProjectTask'])->name('lists.tasks.store');
    Route::patch('/lists/{project}/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::post('/lists/{project}/members', [ProjectMemberController::class, 'store'])->name('lists.members.store');
    Route::delete('/lists/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])->name('lists.members.destroy');

    // Task List Management (Atomic PPK Update)
    Route::resource('task-lists', TaskListController::class);

    // Collaboration: Task List Members
    Route::get('/task-lists/{taskList}/members', [TaskListMemberController::class, 'index'])->name('task-lists.members.index');
    Route::post('/task-lists/{taskList}/members', [TaskListMemberController::class, 'store'])->name('task-lists.members.store');
    Route::delete('/task-lists/{taskList}/members/{user}', [TaskListMemberController::class, 'destroy'])->name('task-lists.members.destroy');

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
