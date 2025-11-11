<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Models\User;

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index')->name('products');
    Route::get('/get-product/{id}','show')->name('get-product');
    Route::post('/add-product', 'store')->name('add-product');
    Route::get('/add-product-form', 'create')->name('product-add-form');
    Route::get('/edit-product-form/{id}', 'edit')->name('edit-product-form');
    Route::put('/update-product', 'update')->name('update-product');
    Route::delete('/delete-product/{id}','destroy')->name('delete-product');
    Route::post('/upload-photo','upload_image')->name('add_product_photo');
});

Route::controller(TaskController::class)->group(function() {
    Route::get('/tasks', 'index')->name('tasks');
    Route::get('/get-tasks', 'getTasks')->name('get-tasks');
    Route::get('/get-user-tasks/{user_id}', 'getTasks')->name('get-user-tasks');
    Route::get('/tasks/{id}','show')->name('show-task');
    Route::get('/get-task/{id}','getTask')->name('get-task');
    Route::get('/add-task', 'create')->name('task-add-form');
    Route::post('/add-task', 'store')->name('save-task');
    Route::get('/edit-task/{task}','edit')->name('edit-task-form');
    Route::put('/update-task/{task}', 'update')->name('update-task');
    Route::delete('/delete-task/{task}', 'destroy')->name('delete-task');
    Route::put('/restore-task/{task}', 'restore')->name('restore-task');
    Route::delete('/delete-task-permanently/{tasks}', 'destroy_permanently')->name('delete-task-permanently');
});

Route::controller(RoleController::class)->group(function(){
   Route::get('/get-role/{id}', 'getRole')->name('get-role');
   Route::get('/get-roles', 'getRoles')->name('get-roles');
   Route::get('/roles', 'index')->name('roles');
   Route::get('/add-role', 'create')->name('role-add-form');
   Route::post('/add-role', 'store')->name('add-role');
   Route::get('/role/{id}', 'show')->name('show-role');
   Route::get('/edit-role/{role}', 'edit')->name('edit-role');
   Route::put('/update-role/{role}', 'update')->name('update-role');
   Route::delete('/delete-role/{id}', 'destroy')->name('delete-role');
   Route::put('/restore-role/{id}', 'restore')->name('restore-role');
   Route::delete('/delete-role-permanently/{id}', 'delete_permanently')->name('delete-role-permanently');
});