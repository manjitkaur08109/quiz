<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\QuizModel;
use App\Models\PaymentModel;
use App\Models\User;
use App\notifications\UserNotification;



class PaymentController extends Controller
{
    public function index(Request $request) {

        $data = PaymentModel::with('user:id,name','quiz:id,title')->latest()->get();
        return $this->actionSuccess('payment fetch successfully', $data);
    }
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
            
            'amount' => $session->amount_total/100, 
            'transaction_id' => $session->payment_intent,
            'status' => PaymentModel::PAID,
        ]);

        $admin = User::where('account_type', 'admin')->first();
        if ($admin) {
            $admin->notify(new UserNotification(
                'New payment',
                'A new payment has been made by a user',
                'payment'
            ));
        }
        
        return redirect('/quiz');
}

public function paymentCancel() {
    return redirect('/quiz');
}
}

