<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'type',
        'module',
        'description',
        'options',
    ];

    // relaciones
    public function authStore(): BelongsToMany
    {
        return $this->belongsToMany(Store::class)
            ->withPivot([
                'value',
            ])->withTimestamps()
            ->where('stores.id', auth()->user()->store_id);
    }
}
