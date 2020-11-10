<?php

namespace App\Http\Modules\Product;

use App\Http\Modules\Uom\Uom;
use App\Traits\SecureDeletes;
use App\Http\Modules\Brand\Brand;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Country\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\ProductCategory\ProductCategory;
use App\Http\Modules\ProductCountries\ProductCountries;
use App\Http\Modules\ProductSubcategory\ProductSubcategory;

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

    public function productCountries()
    {
        return $this->belongsToMany(Country::class, 'product_countries');
    }

    /**
     * Get the presentations for the product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presentations()
    {
        return $this->hasMany(Presentation::class);
    }

    /**
     * The stores that belong to the Product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'stock_stores')->withPivot('id', 'quantity')->withTimestamps();
    }

    /**
     * Get the uom that owns the product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }
}
