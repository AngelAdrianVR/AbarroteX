<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'business_line_name'
    ];

    //relationships
    public function products() :HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function globalProducts() :HasMany
    {
        return $this->hasMany(GlobalProduct::class);
    }
}
