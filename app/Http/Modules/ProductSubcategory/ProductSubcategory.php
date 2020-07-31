<?php

namespace App\Http\Modules\ProductSubcategory;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSubcategory extends Model
{
    use SoftDeletes, SecureDeletes;

    /** 
     * Table Associated with the model
     */ 
    protected $table = 'product_subcategories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'product_category_id'
    ];
}