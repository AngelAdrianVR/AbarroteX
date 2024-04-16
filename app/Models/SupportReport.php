<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SupportReport extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'type',
        'description',
        'first_report',
        'user_id',
        'seller_id',
    ];

    // relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function seller()
    {
        return $this->belongsTo(User::class);
    }
}
