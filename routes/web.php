<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;


    
Route::get('/payment-success',[PaymentController::class,'paymentSuccess']);
Route::get('/payment-cancel', [PaymentController::class, 'paymentCancel']);

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
