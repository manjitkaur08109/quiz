<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/get-dashboard',[DashboardController::class,'getDashboard']);
Route::prefix('category')->group(function(){
    Route::post('/store',[CategoryController::class,'store']);
    Route::get('/show/{id}',[CategoryController::class,'show']);
    Route::put('/update/{id}',[CategoryController::class,'update']);
     Route::get('/index', [CategoryController::class, 'index']);
      Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
});

