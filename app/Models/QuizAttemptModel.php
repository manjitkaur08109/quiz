<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAttemptModel extends Model
{
    use HasUuids, HasFactory;
      // ✅ Table name confirm
    protected $table = 'quiz_attempts';

    // ✅ Agar UUID use kar raha hai (toh keep this)
    public $incrementing = false;
    protected $keyType = 'string';

    // ✅ Fillable fields
    protected $fillable = ['quiz_id', 'user_id','score','total_questions',
        'correct_answers',
        'marks_obtained',];


    // ✅ Cast JSON fields properly
    protected $casts = [
        'correct_answers' => 'array',
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
