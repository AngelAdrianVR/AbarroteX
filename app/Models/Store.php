<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'contact_phone',
        'address',
        'plan',
        'next_payment',
    ];

    protected $casts = [
        'next_payment' => 'date',
    ];

    //relationships
    public function users() :HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products() :HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function expenses() :HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function sales() :HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
