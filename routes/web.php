<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
Route::get('/categories',[CategoryController::class,'categories']);
Route::get('/quiz',[QuizController::class,'quiz']);
