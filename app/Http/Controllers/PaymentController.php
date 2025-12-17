<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\QuizModel;
use App\Models\PaymentModel;



class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $quiz = QuizModel::findOrFail($request->quiz_id);
        Stripe::setApiKey(config('services.stripe.secret'));
        
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'inr',
                     'product_data' => [
                    'name' => $quiz->title,
                ],
                'unit_amount' => $quiz->price * 100, 
                ],
                'quantity' => 1,
                ]],
                'mode' => 'payment',
                
        'metadata' => [
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
        ],

        'success_url' => url('/payment-success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/payment-cancel'),
        ]);

        return $this->actionSuccess('Checkout successful', $session->url);
    }

    
public function paymentSuccess(Request $request)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    $session = Session::retrieve($request->session_id);

    $userId = $session->metadata->user_id ?? null;
    $quizId = $session->metadata->quiz_id ?? null;

    if (!$userId || !$quizId) {
        abort(403, 'Invalid payment data');
    }

    PaymentModel::firstOrCreate(
        [
            'user_id' => $userId,
            'quiz_id' => $quizId,
        ],
        [
            'status' => 'paid',
        ]);
        return redirect('/quiz');
}

public function paymentCancel() {
    return redirect('/quiz');
}
}

