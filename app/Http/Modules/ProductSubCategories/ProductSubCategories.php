<?php

namespace App\Http\Modules\ProductSubCategories;

use App\Traits\SecureDeletes;
use App\Http\Modules\ProductCategory\ProductCategory;
use Illuminate\Database\Eloquent\Model;

class ProductSubCategories extends Model
{
    use SecureDeletes;

    protected $table = 'product_subcategories';

    protected $fillable = [
        'name',
        'product_category_id'
    ];

    protected $with = ['productCategory'];

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}