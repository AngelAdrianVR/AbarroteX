<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'is_active',
        'next_payment',
        'status',
        'seller_id',
        'suscription_period',
        'default_card_id', //tarjata para pagar
    ];

    protected $casts = [
        'next_payment' => 'date',
    ];

    //relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function cashRegisters() :HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function supportReports(): HasMany
    {
        return $this->hasMany(SupportReport::class);
    }

    public function settings(): BelongsToMany
    {
        return $this->belongsToMany(Setting::class)
            ->withPivot([
                'value',
            ]);
    }

    public function globalProducts() :BelongsToMany
    {
        return $this->belongsToMany(GlobalProduct::class, 'global_product_stores')
            ->withPivot([
                'id',
                'public_price',
                'cost',
                'min_stock',
                'max_stock',
                'current_stock',
            ])->withTimestamps();
    }
}
