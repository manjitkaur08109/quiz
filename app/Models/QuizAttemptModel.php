<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAttemptModel extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'quiz_attempts';
    protected $fillable = ['quiz_id', 'user_id','score','total_questions',
        'attempted_answers',
        'passing_score',
        'marks_obtained',];
    protected $casts = [
        'attempted_answers' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(QuizModel::class, 'quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
