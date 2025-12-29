<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EmailModel extends Model
{
    use HasUuids; 
    protected $table = 'email_logs';
    protected $fillable = ['message','status','user_id','subject'];

    const PENDING = 'pending';
    const SUCCESS = 'success';
    const FAILED = 'failed';
}
