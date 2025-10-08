<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class QuizModel extends Model {
    use HasUuids;
    protected $table = 'quiz';
    protected $fillable = [ 'title', 'description', 'category_id', ];
}
