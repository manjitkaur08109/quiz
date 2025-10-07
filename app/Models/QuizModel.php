<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizModel extends Model
{
    protected $table = 'quiz';
    protected $fillable = ['title','description', 'category_id',];
}
