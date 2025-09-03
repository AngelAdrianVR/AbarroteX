<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'current_price',
        'discounted_price',
        'promotions_applied',
        'product_name',
        'quantity',
        'refunded_at',
        'original_price', //precio que indica que cambió el precio unicamente para esa venta
        'folio',
        'payment_method', //tipo de pago, puede ser Efectivo, Tarjeta, Transferencia.
        'product_id',
        'client_id',
        'is_global_product',
        'cash_register_id',
        'store_id',
        'user_id',
        'quote_id',
        'created_at',
    ];

    protected $casts = [
        'promotions_applied' => 'array',
    ];

    //relationships
    public function cashRegister() :BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function quote() :BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function client() :BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        if ($this->is_global_product) {
            return null;
            // return $this->belongsTo(GlobalProductStore::class, 'product_id');
        }
        return $this->belongsTo(Product::class);
    }
}
