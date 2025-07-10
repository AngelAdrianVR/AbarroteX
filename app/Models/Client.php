<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company', //nombre de la empresa en caso de tener
        'name', //nombre del contacto
        'phone',
        'email',
        'notes',
        'debt',
        'street',
        'suburb',
        'ext_number',
        'int_number',
        'town',
        'postal_code',
        'polity_state',
        'razon_social',
        'rfc',
        'tax_regime',
        'store_id',
    ];

    //relationships
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function creditSales(): HasMany
    {
        return $this->hasMany(CreditSaleData::class);
    }

    // methods
    public function calcTotalDebt()
    {
        $totalDebt = 0;

        // Obtener todas las ventas a crédito del cliente
        $creditSales = $this->creditSales()->where('status', '!=', 'Reembolsado')->get();

        foreach ($creditSales as $creditSale) {
            // Obtener todas las ventas (productos vendidos) para cada folio y tienda
            $sales = \App\Models\Sale::where('folio', $creditSale->folio)
                ->where('store_id', $creditSale->store_id)
                ->get();

            // Calcular el total de las ventas (precio actual * cantidad)
            $totalSaleAmount = $sales->sum(function ($sale) {
                return $sale->current_price * $sale->quantity;
            });

            // Obtener todos los abonos realizados para esta venta a crédito
            $installments = \App\Models\Installment::where('credit_sale_data_id', $creditSale->id)->get();

            // Calcular el total de los abonos
            $totalInstallments = $installments->sum('amount');

            // Calcular la deuda restante: total de la venta - total de abonos
            $debtForSale = $totalSaleAmount - $totalInstallments;

            // Sumar la deuda de esta venta al total de la deuda del cliente
            $totalDebt += $debtForSale;
        }

        return $totalDebt;
    }
}
