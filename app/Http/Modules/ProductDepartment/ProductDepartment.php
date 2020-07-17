<?php

namespace App\Http\Modules\ProductDepartment;

use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductDepartment extends Model
{
    use SecureDeletes;
    
    /** 
     * Table Associated with the model
     */ 
    protected $table = 'product_departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'        
    ];
   

}
