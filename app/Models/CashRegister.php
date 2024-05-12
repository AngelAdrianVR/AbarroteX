<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'started_cash',
        'current_cash',
        'max_cash',
        'is_active',
        'store_id',
    ];

    //relationships
    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function movements() :HasMany
    {
        return $this->hasMany(CashRegisterMovement::class);
    }

    public function cashCuts() :HasMany
    {
        return $this->hasMany(CashCut::class);
    }

    public function sales() :HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
