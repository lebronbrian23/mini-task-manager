<?php

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

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group( function () {
    Route::get('dashboard' ,function (){
      return inertia::render('Dashboard');
    })->name('dashboard');
});
require __DIR__.'/settings.php';
