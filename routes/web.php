<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::controller(TaskController::class)->group( function () {
    Route::get('/tasks', 'index')->name('tasks');
    Route::get('/add-task', 'create')->name('task-add-form');
    Route::get('/edit-task/{task}','edit')->name('edit-task-form');
});

Route::controller(PermissionController::class)->group( function () {
    Route::get('/permissions','index')->name('permissions');
    Route::get('/add-permission','create')->name('permission-add-form');
    Route::get('/show-permission/{id}','show')->name('show-permission');
    Route::get('/edit-permission/{permission}','edit')->name('edit-permission-form');
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group( function () {
    Route::get('dashboard' ,function (){
      return inertia::render('Dashboard');
    })->name('dashboard');
});
require __DIR__.'/settings.php';
