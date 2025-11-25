<?php

use App\Http\Controllers\PermissionController;
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
    Route::get('/get-tasks', 'getTasks')->name('get-tasks');
    Route::get('/get-user-tasks/{user_id}', 'getTasks')->name('get-user-tasks');
    Route::get('/tasks/{id}','show')->name('show-task');
    Route::get('/get-task/{id}','getTask')->name('get-task');
    Route::post('/add-task', 'store')->name('save-task');
    Route::put('/update-task/{task}', 'update')->name('update-task');
    Route::delete('/delete-task/{task}', 'destroy')->name('delete-task');
    Route::put('/restore-task/{task}', 'restore')->name('restore-task');
    Route::delete('/delete-task-permanently/{tasks}', 'destroyPermanently')->name('delete-task-permanently');
});

Route::controller(RoleController::class)->group(function(){
   Route::get('/get-role/{id}', 'getRole')->name('get-role');
   Route::get('/get-roles', 'getRoles')->name('get-roles');
   Route::post('/add-role', 'store')->name('add-role');
   Route::post('/attach-permission-to-role/{id}','attachPermissions')->name('attach-permission-to-role');
   Route::delete('/detach-permission-from-role','detachPermissions')->name('detach-permission-from-role');
   Route::put('/update-role/{role}', 'update')->name('update-role');
   Route::delete('/delete-role/{id}', 'destroy')->name('delete-role');
   Route::put('/restore-role/{id}', 'restore')->name('restore-role');
   Route::delete('/delete-role-permanently/{id}', 'deletePermanently')->name('delete-role-permanently');
   Route::post('/assign-role-to-user/{user_id}', 'assignRoleToUser')->name('assign-role-to-user');
   Route::delete('/remove-role-from-user/{user_id}', 'removeRoleFromUser')->name('remove-role-from-user');
});

Route::controller(PermissionController::class)->group(function(){
    Route::get('/get-permissions', 'getPermissions')->name('get-permissions');
    Route::post('/add-permission', 'store')->name('add-permission');
    Route::get('/get-permission/{id}', 'getPermission')->name('get-permission');
    Route::put('/update-permission/{id}', 'update')->name('update-permission');
    Route::delete('/delete-permission/{id}', 'destroy')->name('delete-permission');
    Route::put('/restore-permission/{id}', 'restore')->name('restore-permission');
    Route::delete('/delete-permission-permanently/{id}', 'deletePermanently')->name('delete-permission-permanently');
});