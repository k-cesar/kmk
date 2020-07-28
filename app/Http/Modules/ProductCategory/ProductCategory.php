<?php

namespace App\Http\Modules\ProductCategory;

use App\Traits\SecureDeletes;
use App\Http\Modules\ProductDepartment\ProductDepartment;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use SecureDeletes;
    
    /** 
     * Table Associated with the model
     */ 
    protected $table = 'product_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'product_department_id',
    ];
   
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['productDepartment'];

     /**
     * Get the Product department that owns the product category.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productDepartment()
    {
        return $this->belongsTo(ProductDepartment::class);
    }

}
