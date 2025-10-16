<?php

namespace App\Models;

use FFI\CData;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class QuizModel extends Model {
    use HasUuids;
    protected $table = 'quiz';
    protected $fillable = [ 'title', 'description', 'category_id', 'question', 'options', 'correct_answer' ];

    function category() {
        return $this->hasOne( CategoryModel::class, 'id', 'category_id' );
    }
}
