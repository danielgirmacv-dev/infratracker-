<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'dashboard'])->name('dashboard');

Route::get('/login', [TaskController::class, 'showLogin'])->name('login');
Route::post('/login', [TaskController::class, 'login']);
Route::post('/logout', [TaskController::class, 'logout'])->name('logout');

Route::post('/notifications/read-all', [TaskController::class, 'markAllNotificationsRead'])->name('notifications.read-all');

Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/quick', [SearchController::class, 'quick'])->name('search.quick');

Route::get('/tasks/export', [TaskController::class, 'export'])->name('tasks.export');
Route::resource('tasks', TaskController::class)->except(['show']);

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::post('/projects/import', [ProjectController::class, 'import'])->name('projects.import');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
Route::post('/suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import');
Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

Route::get('/settings/password', [PasswordController::class, 'showChangeForm'])->name('settings.password');
Route::post('/settings/password', [PasswordController::class, 'updatePassword'])->name('settings.password.update');

Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
