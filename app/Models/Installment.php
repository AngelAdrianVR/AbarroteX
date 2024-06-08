<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'notes',
        'group_id',
        'user_id',
    ];

    // relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    } 
}
