<?php

namespace App\Http\Modules\ProductDepartment;

use App\Http\Modules\Company\Company;
use App\Http\Modules\Country\Country;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductDepartment extends Model
{
    /** 
     * Table Associated with the model
     */ 
    protected $table = 'product_departments';
    use SecureDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'        
    ];
   

}
