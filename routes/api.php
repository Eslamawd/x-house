<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;


use App\Http\Middleware\AdminMiddlware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/all-req', [CategoryController::class, 'getByAdmin']);

Route::get('/categories/all', [CategoryController::class, 'getAll']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/product',[ProductController::class, 'index']);
Route::get('/product/{id}',[ProductController::class, 'show']);

Route::post('orders', [OrderController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function () {

    
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::middleware([AdminMiddlware::class])->prefix('admin')->group(function () {

        
        Route::get('/order/count', [OrderController::class, 'count']);
        
        Route::apiResource('orders', OrderController::class);
        Route::get('all-order',[ OrderController::class, 'orders']);
        
        Route::get('/revnue/count', [OrderController::class, 'getRevenue']);
        
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('product', ProductController::class);

        Route::get('/all/product', [ProductController::class, 'getByAdmin']);
        Route::apiResource('orders', OrderController::class);
        Route::get('all-order',[ OrderController::class, 'orders']);
        

        Route::get('/order/count', [OrderController::class, 'count']);
        Route::get('/revnue/count', [OrderController::class, 'getRevenue']); 

    });

   
});
   






