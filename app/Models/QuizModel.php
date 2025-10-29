<?php

namespace App\Models;

use FFI\CData;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuizAttemptModel;

class QuizModel extends Model {
    use HasUuids;
    protected $table = 'quiz';
    protected $fillable = [ 'title', 'passing_score', 'description', 'category_id', 'questions' ];
    protected $casts= [
        'questions'=>'array'
    ];

    function category() {
        return $this->hasOne( CategoryModel::class, 'id', 'category_id' );
    }
    function getByCategory($categoryId)
    {
        $category = CategoryModel::findOrFail($categoryId);

        $quizzes = QuizModel::where('category_id', $categoryId)
            ->get()
            ->map(function ($quiz) {
                $quiz->total_questions = is_array($quiz->questions)
                    ? count($quiz->questions)
                    : 0;
                return $quiz;
            });

        return response()->json([
            'categoryTitle' => $category->title,
            'quizzes' => $quizzes
        ]);
    }
    function attempts()
{
    return $this->hasMany(QuizAttemptModel::class, 'quiz_id');
}
}
