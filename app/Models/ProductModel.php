<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasUuids;
    protected $table = 'products';
    protected $fillable = ['title','description','category_id','price'];

    public function category(){
        return $this-> hasOne(CategoryModel::class,'id','category_id');
    }
}
