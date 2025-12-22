<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/checkout', [PaymentController::class, 'checkout']);


Route::prefix('notifications')->group(function () {
    Route::get('/index', [NotificationController::class, 'index']);
    Route::get('/show/{id}', [NotificationController::class, 'show']);
    Route::post('/markAsRead/{id}', [NotificationController::class, 'markAsRead']);
    Route::delete('/destroy/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/destroyAll', [NotificationController::class, 'destroyAll']);
    
});
    Route::prefix('quiz')->group(function(){
        Route::get('/index', [QuizController::class, 'index']);
    });

    
    Route::prefix('category')->group(function(){
        Route::get('/index', [CategoryController::class, 'index']);
    });


Route::middleware(['admin'])->group(function () {
    
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
    Route::post('/impersonate', [UserController::class, 'impersonate']);
    
    Route::get('/get-dashboard',[DashboardController::class,'getDashboard']);
    
    Route::get('/payments/index', [PaymentController::class, 'index']);

    Route::prefix('category')->group(function(){
        // Route::get('/index', [CategoryController::class, 'index']);
        Route::post('/store',[CategoryController::class,'store']);
        Route::get('/show/{id}',[CategoryController::class,'show']);
        Route::put('/update/{id}',[CategoryController::class,'update']);
        Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
    });
    
    Route::prefix('quiz')->group(function(){
        // Route::get('/index', [QuizController::class, 'index']);
        Route::get('/payment/create', [QuizController::class, 'create']);
        Route::post('/store', [QuizController::class, 'store']);
        Route::get('/show/{id}', [QuizController::class, 'show']);
        Route::put('/update/{id}', [QuizController::class, 'update']);
        Route::delete('/delete/{id}', [QuizController::class, 'destroy']);
    });
    
});
    
Route::middleware(['user'])->group(function () {
     
    Route::prefix('quiz-attempt')->group(function(){
        Route::post('/store', [QuizAttemptController::class, 'store']);
        Route::get('/index', [QuizAttemptController::class, 'index']);
    });
    


});

});



