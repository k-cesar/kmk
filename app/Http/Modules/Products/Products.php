<?php

namespace App\Http\Modules\Products;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use SecureDeletes;
    
    /** 
     * Table Associated with the model
     */ 
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'brand_id',
        'product_category_id',
        'product_subcategory_id',
        'is_taxable',
        'is_inventoriable',
        'uom_id',
        'minimal_expresion',
        'suggested_price',
    ];
}
