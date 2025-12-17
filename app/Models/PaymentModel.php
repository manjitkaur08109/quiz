<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $fillable = [
        'user_id',
        'quiz_id',
        'status',
    ];
}
