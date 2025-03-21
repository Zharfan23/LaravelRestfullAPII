<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReviewController;

// Home route
Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->middleware('api')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('books', BookController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('loans', LoanController::class);
    Route::apiResource('reviews', ReviewController::class);
});