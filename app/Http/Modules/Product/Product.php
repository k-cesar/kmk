<?php

namespace App\Http\Modules\Product;

use App\Traits\SecureDeletes;
use App\Http\Modules\ProductCategory\ProductCategory;
use App\Http\Modules\ProductSubcategory\ProductSubcategory;
use App\Http\Modules\ProductCountries\ProductCountries;
use App\Http\Modules\Brand\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, SecureDeletes;

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


    protected $with = [
        'productCategory',
        'productSubcategory',
        'brand',
        'all_countries'
    ];

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productSubcategory()
    {
        return $this->belongsTo(ProductSubcategory::class);
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
