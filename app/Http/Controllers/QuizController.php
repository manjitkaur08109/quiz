<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\QuizModel;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Question\Question;
use Symfony\Contracts\Service\Attribute\Required;

class QuizController extends Controller {
    function index(Request $request) {
        $query = QuizModel::with(['category', 'attempts'])->latest();
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $quizzes = $query->get();
        foreach ($quizzes as $quiz) {
            $quiz->attemptedBy = $quiz->attempts->pluck('user_id')->toArray();
        }
        return $this->actionSuccess('Quiz fetch successfully', $quizzes);
    }

    function create() {
        $categories = CategoryModel::all( [ 'id', 'title' ] );
        return $this->actionSuccess( 'quiz create', $categories );
    }

    function store( Request $request ) {

        $request->validate( [
            'title' => 'required|string|max:255|unique:quiz,title',
            'passing_score' => 'required|numeric|min:0|max:100',
            'description' => 'required|string',
            'category_id' => 'required|exists:category,id',
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
            'questions' => $request->questions,
        ] );

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
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correctAnswer' => 'required|string',

            ] );
        $exists = QuizModel::where( 'title', $request->title )
        ->whereNot( 'id', $request->id )
        ->exists();
        if ( $exists ) {
            return $this->actionSuccess( 'This quiz title already exists.' );
        }

        $quiz->update( [
            'title' => $request->title,
            'passing_score' => $request->passing_score,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'questions' => $request->questions,
        ] );

        return $this->actionSuccess( 'Quiz updated successfully', $quiz );
    }

    function destroy( $id ) {
        $quiz = QuizModel::findOrFail( $id );
        $quiz-> delete();
        return $this->actionSuccess( 'Quiz deleted successfully' );
    }
    public function myLearning(Request $request)
{
    $userId = Auth::id(); // 👤 Get logged-in user ID

    // Build query: only quizzes that this user has attempted
    $query = QuizModel::with(['category', 'attempts' => function ($q) use ($userId) {
        $q->where('user_id', $userId);
    }])
    ->whereHas('attempts', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
    ->latest();

    // Optional category filter
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Fetch all matched quizzes
    $quizzes = $query->get();

    // Return response
    return $this->actionSuccess('My Learning quizzes fetched successfully', $quizzes);
}

}
