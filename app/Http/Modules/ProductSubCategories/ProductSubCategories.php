<?php

namespace App\Http\Modules\ProductSubCategories;

use App\Traits\SecureDeletes;
use App\Http\Modules\ProductCategory\ProductCategory;
use Illuminate\Database\Eloquent\Model;

class ProductSubCategories extends Model
{
    use SecureDeletes;

    protected $fillable = [
        'name',
        'product_category_id'
    ];

    protected $table = 'product_subcategories';
}