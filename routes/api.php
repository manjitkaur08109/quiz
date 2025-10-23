<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/get-dashboard',[DashboardController::class,'getDashboard']);

    Route::prefix('category')->group(function(){
        Route::get('/index', [CategoryController::class, 'index']);
    Route::post('/store',[CategoryController::class,'store']);
    Route::get('/show/{id}',[CategoryController::class,'show']);
    Route::put('/update/{id}',[CategoryController::class,'update']);
      Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('quiz')->group(function(){
    Route::get('/index', [QuizController::class, 'index']);
    Route::get('/create', [QuizController::class, 'create']);
    Route::post('/store', [QuizController::class, 'store']);
    Route::get('/show/{id}', [QuizController::class, 'show']);
      Route::put('/update/{id}', [QuizController::class, 'update']);
      Route::delete('/delete/{id}', [QuizController::class, 'destroy']);
});

    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
