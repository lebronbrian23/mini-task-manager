<?php


use App\Http\Controllers\ProductController;
use App\Http\Controllers\TaskController;
use App\Models\User;


Route::controller(ProductController::class)->group( function () {

    Route::get('/products', 'index')->name('products');
    Route::get('/get-product/{id}','show')->name('get-product-id');
    Route::post('/add-product' , 'store')->name('add-product');
    Route::get('/add-product-form' , 'create')->name('add-product-form');
    Route::get('/edit-product-form/{id}' , 'edit')->name('edit-product-form-id');
    Route::put('/update-product', 'update')->name('update-product');
    Route::delete('/delete-product/{id}','destroy')->name('delete-product-id');
    Route::post('/upload-photo','upload_image')->name('add_product_photo');
});

Route::controller(TaskController::class)->group( function() {
    Route::get('/tasks', 'index')->name('tasks');
    Route::get('/get-tasks', 'getTasks')->name('get-tasks');
    Route::get('/get-user-tasks/{user_id}', 'getTasks')->name('get-user-tasks');
    Route::get('/task','show')->name('show-task');
    Route::get('/get-task/{id}','getTask')->name('get-task-id');
    Route::get('/add-task', 'create')->name('add-task-form');
    Route::post('/add-task', 'store')->name('save-task');
    Route::get('/edit-task/{task}','edit')->name('edit-task-form');
    Route::put('/update-task/{task}', 'update')->name('update-task');
    Route::delete('/delete-task/{task}', 'destroy')->name('delete-task-id');
    Route::put('/restore-task/{id}', 'restore')->name('restore-task-id');
    Route::delete('/delete-task-permanently/{id}', 'destroy_permanently')->name('delete-task-permanently-id');

});
//Route::middleware('auth:sanctum')->group( function () {
//    Route::get('/user', function (Request $request){
//        return User::find(1);
//    });
//
//    Route::get('/get-users',  'getusers');
//    Route::post('/add-user',  'addUser');
//    Route::put('/update-user',  'update');
//    Route::delete('/delete-user', 'destroy');
//
//});

