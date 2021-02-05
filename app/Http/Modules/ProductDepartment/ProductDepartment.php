<?php

namespace App\Http\Modules\ProductDepartment;

use App\Support\Helper;
use App\Traits\SecureDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDepartment extends Model
{
    use SoftDeletes, SecureDeletes;
    
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

    /**
     * Set the productDepartment's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Helper::strToUpper($value);
    }
   

}
