<?php

namespace App\Http\Modules\Products;

use App\Traits\SecureDeletes;
use App\Http\Modules\ProductCategory\ProductCategory;
use App\Http\Modules\ProductSubCategories\ProductSubCategories;
use App\Http\Modules\ProductCountries\ProductCountries;
use App\Http\Modules\Brand\Brand;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'is_all_countries',
        'brand_id',
        'product_category_id',
        'product_subcategory_id',
        'is_taxable',
        'is_inventoriable',
        'uom_id',
        'minimal_expresion',
        'suggested_price',
    ];

    /** 
     * Table Associated with the model
     */ 
    protected $table = 'products';

    protected $with = [
        'product_category',
        'product_subcategory',
        'brand',
        'all_countries'
    ];

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function product_subcategory()
    {
        return $this->belongsTo(ProductSubCategories::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function all_countries()
    {
        return $this->hasMany(ProductCountries::class, 'product_id');
    }
}
