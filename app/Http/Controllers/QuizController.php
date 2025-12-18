<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\QuizAttemptModel;
use App\Models\QuizModel;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Question\Question;
use Symfony\Contracts\Service\Attribute\Required;
use App\Models\PaymentModel;
use App\Notifications\UserNotification;

class QuizController extends Controller {
    function index( Request $request ) {
        $userId = Auth::user()->id;
        $query = QuizModel::with( [ 'category', 'attempts' ] )->latest();
        if ( $request->has( 'category_id' ) ) {
            $query->where( 'category_id', $request->category_id );
        }
        $quizzes = $query->get();
        foreach ( $quizzes as $quiz ) {
            $quiz->attemptedBy = $quiz->attempts->pluck( 'user_id' )->toArray();
              $quiz->is_paid = PaymentModel::where('user_id', $userId ,)
                ->where('quiz_id', $quiz->id)
                ->where('status', 'paid')
                ->exists();
        }
        return $this->actionSuccess( 'Quiz fetch successfully', $quizzes );
    }



    function store( Request $request ) {

        $request->validate( [
            'title' => 'required|string|max:255|unique:quiz,title',
            'passing_score' => 'required|numeric|min:0|max:100',
            'description' => 'required|string',
            'category_id' => 'required|exists:category,id',
            'price' => 'required_if:is_paid,true|nullable|numeric|min:0|max:1000',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correctAnswer' => 'required|string',

        ] );
        $exists = QuizModel::where( 'title', $request->title )
        ->exists();
        if ( $exists ) {
            return $this->actionSuccess( 'This quiz title already exists!' );
        }

        $quiz = QuizModel::create( [
            'title' => $request->title,
            'passing_score' => $request->passing_score,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'questions' => $request->questions,
        ] );

        $users = User::where('account_type', 'user')->get();
        foreach($users as $user){
            $user->notify(new UserNotification('New Quiz Added', 
                        "A new quiz '{$quiz->title}' is available now!",
             'quiz'));
        }
        
        

        return $this->actionSuccess( 'Quiz added successfully', $quiz );
    }

    function show( $id ) {
        $quiz = QuizModel::findOrFail( $id );
        return $this->actionSuccess( 'Quiz show', $quiz );
    }

    function update( Request $request, $id ) {
        $quiz = QuizModel::findOrFail( $id );

        $request->validate( [
            'title' => 'required|string|max:255',
            'passing_score' => 'required|numeric|min:0|max:100',
            'description' => 'required|string',
            'category_id' => 'required|exists:category,id',
            'price' => 'required_if:is_paid,true|nullable|numeric|min:0|max:1000',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correctAnswer' => 'required|string',

        ] );
        $exists = QuizModel::where( 'title', $request->title )
        ->whereNot( 'id', $request->id )
        ->exists();
        if ( $exists ) {
            return $this->actionFailure( 'This quiz title already exists.' );
        }

        $quiz->update( [
            'title' => $request->title,
            'passing_score' => $request->passing_score,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'questions' => $request->questions,
        ] );

        return $this->actionSuccess( 'Quiz updated successfully', $quiz );
    }

    function destroy( $id ) {
        $quiz = QuizModel::findOrFail( $id );
        $quiz-> delete();
        return $this->actionSuccess( 'Quiz deleted successfully' );
    }

}
