<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model {
    use HasUuids;
    protected $table = 'category';
    protected $fillable = [ 'title','description' ];
}
