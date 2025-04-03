<?php

use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\TrackStockUpdateController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

// socialite routes
Route::get('auth/{provider}/redirect', [UserController::class , 'redirect'])->name('auth.socialite.redirect');
Route::get('oauth/{provider}/callback', [UserController::class , 'callback'])->name('auth.socialite.callback');
// socialite for api
Route::post('social/login', [UserController::class, 'socialLogin']);

// authentication routes
Route::post("login", [UserController::class, "login"]);
Route::post("register", [UserController::class, "register"]);

// public routes
// categories
Route::apiResource('categories', CategoryController::class)->only([ 'index', 'show' ]);

// products
Route::get("products/search", [ProductController::class, "search"]);
Route::apiResource('categories/{category_id}/products', ProductController::class)->only([ 'index', 'show' ]);
Route::apiResource('products', ProductController::class)->only([ 'index', 'show' ]);

// banners
Route::apiResource('banners', BannerController::class)->only([ 'index', 'show' ]);

// authenticated route
Route::middleware('auth:sanctum')->group(function(){
    // user routes
    Route::post("logout", [UserController::class, "logout"]);
    Route::get("user", [UserController::class, "show"]);
    Route::put("user", [UserController::class, "update"]);

    // product routes
    Route::put("products/{product}/request", [ProductController::class, "productRequest"])->middleware('auth:sanctum');

    // admin routes
    Route::middleware('role:admin')->group(function(){
        // categories
        Route::apiResource('categories', CategoryController::class)->only([ 'store', 'update', 'destroy' ]);

        // products
        Route::apiResource('categories/{category_id}/products', ProductController::class)->only([ 'store','update']);
        Route::apiResource('products', ProductController::class)->only([ 'store', 'update', 'destroy' ]);
        Route::apiResource('products/{product}/track-stock-updates', TrackStockUpdateController::class)->only([ 'index' ]);

        // banners
        Route::apiResource('banners', BannerController::class)->only([ 'store', 'update', 'destroy' ]);

        // orders
        Route::apiResource('orders', OrderController::class)->only([ 'index', 'show', 'update' ]);
    });
});

Route::fallback(fn() => response()->json(['message' => 'Invalid URL'], 404));
