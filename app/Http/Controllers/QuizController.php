<?php

namespace App\Http\Controllers;

use App\Models\QuizModel;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Symfony\Component\Console\Question\Question;
use Symfony\Contracts\Service\Attribute\Required;

class QuizController extends Controller {
    function index() {
        $data = QuizModel::latest()->get();
        return $this->actionSuccess( 'Quiz fetch successfully', $data );
    }

    function store( Request $request ) {
        $validated = $request->validate( [
            'title' => 'required',
            'description' => 'nullable',
            'category' => 'required',
            'question' => 'required',
            'options' => 'required|array',
            'correctAnswer' => 'required',
        ] );

        $quiz = QuizModel::create( [
            'title' => $validated[ 'title' ],
            'description' => $validated[ 'description' ],
            'category' => $validated[ 'category' ],
            'question' => $validated[ 'question' ],
            'options' => json_encode( $validated[ 'options' ] ),
            'correct_answer' => $validated[ 'correctAnswer' ],
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
