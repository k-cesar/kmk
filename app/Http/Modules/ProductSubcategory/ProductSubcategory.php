<?php

namespace App\Http\Modules\ProductSubcategory;

use App\Support\Helper;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Modules\ProductCategory\ProductCategory;

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

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['productCategory'];

    /**
     * Set the productSubcategory's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Helper::strToUpper($value);
    }

     /**
     * Get the Product Category that owns the product Subcategory.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}