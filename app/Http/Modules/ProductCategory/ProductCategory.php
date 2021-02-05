<?php

namespace App\Http\Modules\ProductCategory;

use App\Traits\SecureDeletes;
use App\Http\Modules\ProductDepartment\ProductDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes, SecureDeletes;
    
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
     * Set the productCategory's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value)), 'utf-8');
    }

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
