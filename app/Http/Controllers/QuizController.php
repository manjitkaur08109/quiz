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
            'title' => 'required|string|max:255|unique:quiz,title',
            'description' => 'required|string',
            'category_id' => 'required|exists:category,id',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correctAnswer' => 'required|string',
            'questions.*.passingScore' => 'required|numeric|min:0|max:100',

        ] );
        $exists = QuizModel::where( 'title', $request->title )
        ->exists();
        if ( $exists ) {
            return $this->actionSuccess( 'This quiz title already exists!' );
        }

        $quiz = QuizModel::create( [
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'questions' =>  $request->questions ,
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
            'description' => 'required|string',
            'category_id' => 'required|exists:category,id',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correctAnswer' => 'required|string',
            'questions.*.passingScore' => 'required|numeric|min:0|max:100',

        ] );
        $exists = QuizModel::where( 'title', $request->title )
        ->whereNot( 'id', $request->id )
        ->exists();
        if ( $exists ) {
            return $this->actionSuccess( 'This quiz title already exists.' );
        }

        $quiz->update( [
            'title' => $request->title,
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
}
