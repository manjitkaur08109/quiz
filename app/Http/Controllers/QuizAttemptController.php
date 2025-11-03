<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizAttemptModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller {

    public function store( Request $request ) {
        $validated = $request->validate( [
            'quiz_id' => 'required|uuid',
            'score' => 'required|integer',
            'passing_score' => 'required|integer',
            'total_questions' => 'required|integer',
            'attempted_answers' => 'nullable|array',
            'marks_obtained' => 'required|integer',
        ] );

        $attempt = QuizAttemptModel::create( [
            'quiz_id' => $request->quiz_id,
            'user_id' => Auth::id(),
            'score' => $request->score,
            'passing_score' => $request->passing_score,
            'total_questions' => $request->total_questions,
            'attempted_answers' => $request->attempted_answers ?? [],
            'marks_obtained' => $request->marks_obtained,
        ] );
        return response()->json( [ 'message' => 'Attempt saved', 'data' => $attempt ] );
    }

    public function index( Request $request ) {
        $attempts = QuizAttemptModel::with( 'quiz.category:id,title')
        ->where( 'user_id', Auth::id() )
        ->latest();
        if ( $request->filled( 'category_id' ) ) {
            $attempts->whereHas( 'quiz', function ( $q ) use ( $request ) {
                $q->where( 'category_id', $request->category_id );
            } );
        }
        $attempts = $attempts->get();

        return response()->json( [ 'data' => $attempts ] );
    }
    

}
