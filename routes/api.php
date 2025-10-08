<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/get-dashboard',[DashboardController::class,'getDashboard']);
// Route::get('/quiz',[QuizController::class,'quiz']);
// Category Routes
// Route::prefix('category')->group(function () {
//     Route::get('/index', [CategoryController::class, 'index']);
//     Route::get('/create' , [CategoryController::class , 'create']);
//     Route::post('/store' , [CategoryController::class , 'store']);
//     Route::get('/show/{id}', [CategoryController::class, 'show']);
//     Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
//     Route::get('/edit/{id}',[CategoryController::class,'edit']);
//     Route::put('/update',[CategoryController::class,'update']);
// });
