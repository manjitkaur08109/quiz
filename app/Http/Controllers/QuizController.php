<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\QuizModel;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Symfony\Component\Console\Question\Question;
use Symfony\Contracts\Service\Attribute\Required;

class QuizController extends Controller {
    function index() {
        $data = QuizModel::latest()->with( [ 'category' ] )->get();
        return $this->actionSuccess( 'Quiz fetch successfully', $data );
    }

    function create() {
        $categories = CategoryModel::all( [ 'id', 'title' ] );
        return $this->actionSuccess( 'quiz create', $categories );
    }

    function store( Request $request ) {

        $request->validate( [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'correctAnswer' => 'required|string',
            'category_id' => 'required|exists:category,id',
        ] );

        $quiz = QuizModel::create( [
            'title' => $request->title,
            'description' => $request->description,
            'question' => $request->question,
            'options' => json_encode( $request->options ),
            'correct_answer' => $request->correctAnswer,
            'category_id' => $request->category_id,
        ] );
        return $this->actionSuccess( 'Quiz added successfully', $quiz );
    }

    function show( $id ) {
        $quiz = QuizModel::findOrFail( $id );
        return $this->actionSuccess( 'success', $quiz );
    }

    function update( Request $request, $id ) {
        $quiz = QuizModel::findOrFail( $id );
        $quiz->update( $request->all() );
        return $this->actionSuccess( 'Quiz updated successfully', $quiz );
    }

    function destroy( $id ) {
        $quiz = QuizModel::findOrFail( $id );
        $quiz-> delete();
        return $this->actionSuccess( 'Quiz deleted successfully' );
    }
}
