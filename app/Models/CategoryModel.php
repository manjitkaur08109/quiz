<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\CategoryModel;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model {
    use HasUuids;
    protected $table = 'category';
    
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [ 'title','description' ];
}
