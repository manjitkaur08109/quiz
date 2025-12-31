<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class PaymentModel extends Model
{
    use HasUuids;
    protected $table = 'payments';
    protected $fillable = [
        'user_id',
        'quiz_id',
        'amount',
        'transaction_id',
        'status',
    ];
    const PAID = 'paid';
    const FAILED = 'failed';

    // ✅ ADD THIS
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // (optional)
    public function quiz()
    {
        return $this->belongsTo(QuizModel::class, 'quiz_id');
    }
}

