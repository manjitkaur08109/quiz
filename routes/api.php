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
use App\Http\Controllers\RoleController;


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
    Route::prefix('quiz')->group(function () {
        Route::get('/index', [QuizController::class, 'index']);
    });


    Route::prefix('category')->group(function () {
        Route::get('/index', [CategoryController::class, 'index']);
    });
 
    Route::prefix('rolepermission')->group(function () {
        Route::get('/index', [RoleController::class, 'index'])->middleware('permission:view rolepermission');
        Route::post('/copy', [RoleController::class, 'copy'])->middleware('permission:view rolepermission');
        Route::post('/store', [RoleController::class, 'store'])->middleware('permission:create rolepermission');
        Route::get('/show/{id}', [RoleController::class, 'show'])->middleware('permission:view rolepermission');
        Route::put('/update/{id}', [RoleController::class, 'update'])->middleware('permission:edit rolepermission');
        Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->middleware('permission:delete rolepermission');
        Route::get('/get-permissions', [RoleController::class, 'getPermissions'])->middleware('permission:view rolepermission');
    });

    Route::middleware(['admin'])->group(function () {

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:view user');

        Route::delete('/users/{id}', [UserController::class, 'destroy'])
            ->middleware('permission:delete user');

        Route::post('/impersonate', [UserController::class, 'impersonate'])
            ->middleware('permission:view user');

        Route::get('/get-dashboard', [DashboardController::class, 'getDashboard'])->middleware('permission:view dashboard');

        Route::get('/payments/index', [PaymentController::class, 'index'])->middleware('permission:view payment');

        Route::prefix('category')->group(function () {
            Route::post('/store', [CategoryController::class, 'store'])
                ->middleware('permission:create category');
            Route::get('/show/{id}', [CategoryController::class, 'show'])
                ->middleware('permission:view category');
            Route::put('/update/{id}', [CategoryController::class, 'update'])
                ->middleware('permission:edit category');
            Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])
                ->middleware('permission:delete category');
        });

        Route::prefix('quiz')->group(function () {
            Route::get('/payment/create', [QuizController::class, 'create'])->middleware('permission:create quiz');
            Route::post('/store', [QuizController::class, 'store'])->middleware('permission:create quiz');
            Route::get('/show/{id}', [QuizController::class, 'show'])->middleware('permission:view quiz');
            Route::put('/update/{id}', [QuizController::class, 'update'])->middleware('permission:edit quiz');
            Route::delete('/delete/{id}', [QuizController::class, 'destroy'])->middleware('permission:delete quiz');
        });
    });

    Route::middleware(['user'])->group(function () {

        Route::prefix('quiz-attempt')->group(function () {
            Route::post('/store', [QuizAttemptController::class, 'store']);
            Route::get('/index', [QuizAttemptController::class, 'index']);
        });
    });
});
