<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\EmailModel;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\QuizModel;
use App\Models\PaymentModel;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function index(Request $request)
    {

        $query = PaymentModel::with('user:id,name', 'quiz:id,title')->latest();
        if($request->search){
            $query->where('user:name', 'like', '%' . $request->search . '%')
            ->orWhere('quiz:title', 'like', '%' . $request->search . '%');
        }
        $data = $query->paginate($request->per_page ?? 5);
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
            'cancel_url' => url('/payment-cancel?session_id={CHECKOUT_SESSION_ID}'),
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

                'amount' => $session->amount_total / 100,
                'transaction_id' => $session->payment_intent,
                'status' => PaymentModel::PAID,
            ]
        );

        $admin = User::where('role_id', getRoleId('admin'))->first();
        if ($admin) {
            $admin->notify(new UserNotification(
                'New payment',
                'A new payment has been made by a user',
                'payment',
                $admin->id
            ));
        }

        $user = User::find($userId);
        $subject = 'Payment Success';
        $total = $session->amount_total / 100;
        $messageText = "
          Name: {$user->name} 
          Email: {$user->email} 
          Amount: ($total)
          Payment successfull!";

        $mailData = ['message' => $messageText];
           $status = 'pending';
        try{

            Mail::to($user->email)->send(new ContactMail($subject, $mailData));
            $status = 'success';
        }catch(\Exception $e){
           $status = 'failed';
        }

        EmailModel::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'subject' => $subject,
            'message' => $mailData['message'],
            'status' => $status
        ]);

        if ($admin) {
            $adminStatus = 'pending';

            try{
                Mail::to($admin->email)->send(new ContactMail($subject, $mailData));
                $adminStatus = 'success';
            }catch(\Exception $e){
                $adminStatus = 'failed';
            }

            EmailModel::create([
                'user_id' => $admin->id,
                'email' => $admin->email,
                'subject' => $subject,
                'message' => $mailData['message'],
                'status' => $adminStatus
            ]);
        }

      

        return redirect('/discover');
    }

    public function paymentCancel(Request $request)
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
                'amount' => $session->amount_total / 100,
                'transaction_id' => $session->payment_intent,
                'status' => PaymentModel::FAILED,
            ]
        );

        $admin = User::where('role_id', getRoleId('admin'))->first();

        if ($admin) {
            $admin->notify(new UserNotification(
                'Payment failed',
                'A payment has been failed by a user',
                'payment',
                $admin->id
            ));
        };

        $user = User::find($userId);
        $subject = 'Payment Failed';

        $total = $session->amount_total / 100;
        $messageText = "
                Name: {$user->name} 
                Email: {$user->email} 
                Amount: ($total)
                Payment failed!";

        $mailData = ['message' => $messageText];
        $status = 'pending';
        try{

            Mail::to($user->email)->send(new ContactMail($subject, $mailData));
            $status = 'success';
        }catch(\Exception $e){
           $status = 'failed';
        }

        EmailModel::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'subject' => $subject,
            'message' => $mailData['message'],
            'status' => $status
        ]);

        if ($admin) {
            $adminStatus = 'pending';

            try{
                Mail::to($admin->email)->send(new ContactMail($subject, $mailData));
                $adminStatus = 'success';
            }catch(\Exception $e){
                $adminStatus = 'failed';
            }

            EmailModel::create([
                'user_id' => $admin->id,
                'email' => $admin->email,
                'subject' => $subject,
                'message' => $mailData['message'],
                'status' => $adminStatus
            ]);
        }

        return redirect('/discover');
    }
}
