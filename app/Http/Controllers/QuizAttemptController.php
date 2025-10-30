<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizAttemptModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller {
    // Store a new quiz attempt

    public function store( Request $request ) {
        $validated = $request->validate( [
            'quiz_id' => 'required|uuid',
            'score' => 'required|integer',
            'total_questions' => 'required|integer',
            'correct_answers' => 'nullable|array',
            'marks_obtained' => 'required|integer',
        ] );

        $attempt = QuizAttemptModel::create( [
            'quiz_id' => $request->quiz_id,
            'user_id' => Auth::id(),
            'score' => $request->score,
            'total_questions' => $request->total_questions,
            'correct_answers' => $request->correct_answers ?? [],
            'marks_obtained' => $request->marks_obtained,
        ] );
        return response()->json( [ 'message' => 'Attempt saved', 'data' => $attempt ] );
    }

    // List all attempts for the authenticated user

    public function index( Request $request ) {
        $attempts = QuizAttemptModel::where( 'user_id', Auth::id() )->get();
        return response()->json( [ 'data' => $attempts ] );
    }
}
