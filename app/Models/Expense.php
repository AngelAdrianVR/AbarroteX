<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'concept',
        'quantity',
        'current_price',
        'store_id',
    ];

    //relationships 
    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

}
