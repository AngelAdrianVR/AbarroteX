<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\Client;
use App\Models\CreditSaleData;
use App\Models\Installment;
use App\Models\OnlineSale;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function index()
    {
        return inertia('Client/Index');
    }

    public function create()
    {
        return inertia('Client/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company' => 'nullable|string|max:150',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|min:10|max:10',
            'notes' => 'nullable|string|max:255',
            'street' => $request->addAddress ? 'required|string|max:255' : 'nullable',
            'suburb' => $request->addAddress ? 'required|string|max:255' : 'nullable',
            'ext_number' => $request->addAddress ? 'required|string|max:8' : 'nullable',
            'int_number' => 'nullable|string|max:8',
            'postal_code' => $request->addAddress ? 'required|string|max:6' : 'nullable',
            'town' => $request->addAddress ? 'required|string|max:100' : 'nullable',
            'polity_state' => $request->addAddress ? 'required|string|max:100' : 'nullable',
            'razon_social' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
            'rfc' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
            'tax_regime' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
        ]);

        Client::create($request->all() + ['store_id' => auth()->user()->store_id]);
    }

    /**
     * Registra un abono de un cliente y lo distribuye entre sus deudas pendientes.
     * usado en Pages/Client/Show, 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeInstallment(Request $request, Client $client)
    {
        // 1. Validar el monto del abono entrante
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amountToApply = (float) $validated['amount'];
        $originalAmount = $amountToApply;

        // Usar una transacción para garantizar la integridad de los datos
        return DB::transaction(function () use ($client, $amountToApply, $originalAmount) {

            // 2. Obtener todas las ventas a crédito pendientes del cliente, de la más antigua a la más nueva
            $pendingCreditSales = CreditSaleData::where('client_id', $client->id)
                ->where('status', '!=', 'Pagado')
                ->orderBy('created_at', 'asc')
                ->get();

            $user = auth()->user();
            $cashRegister = CashRegister::findOrFail($user->cash_register_id);

            // 3. Iterar sobre cada deuda pendiente y aplicar el abono
            foreach ($pendingCreditSales as $creditSale) {
                if ($amountToApply <= 0) {
                    break; // Terminar si ya no hay monto que aplicar
                }

                $sales = Sale::where([
                    'folio' => $creditSale->folio,
                    'store_id' => $user->store_id,
                ])->get();

                // Calcular el saldo restante de esta venta específica
                $totalSaleAmount = $sales->sum(function ($sale) {
                    $price_to_use = $sale->discounted_price !== null
                        ? $sale->discounted_price
                        : $sale->current_price;

                    return $sale->quantity * $price_to_use;
                });

                $totalPaid = $creditSale->installments()->sum('amount');
                $dueAmount = $totalSaleAmount - $totalPaid;

                if ($dueAmount <= 0) {
                    continue; // Omitir si por alguna razón esta deuda ya está saldada
                }

                // Determinar cuánto se pagará a esta deuda
                $paymentForThisSale = min($amountToApply, $dueAmount);

                // Registrar el abono (Installment)
                Installment::create([
                    'amount' => $paymentForThisSale,
                    'credit_sale_data_id' => $creditSale->id,
                    'client_id' => $client->id,
                    'user_id' => $user->id,
                ]);

                // Registrar el movimiento en caja
                CashRegisterMovement::create([
                    'amount' => $paymentForThisSale,
                    'type' => 'Ingreso',
                    'notes' => "Abono a venta con folio {$creditSale->folio}",
                    'cash_register_id' => $cashRegister->id,
                ]);

                // Actualizar el estado de la venta a crédito
                if (($totalPaid + $paymentForThisSale) >= $totalSaleAmount) {
                    $creditSale->status = 'Pagado';
                } else {
                    $creditSale->status = 'Parcial';
                }
                $creditSale->save();

                // Reducir el monto restante del abono
                $amountToApply -= $paymentForThisSale;
            }

            // 4. Si queda un sobrante, registrarlo como saldo a favor del cliente
            if ($amountToApply > 0) {
                Installment::create([
                    'amount' => $amountToApply,
                    'client_id' => $client->id, // Se asocia directamente al cliente
                    'credit_sale_data_id' => null, // Sin venta a crédito específica
                    'user_id' => $user->id,
                ]);

                // Registrar también este ingreso en caja
                CashRegisterMovement::create([
                    'amount' => $amountToApply,
                    'type' => 'Ingreso',
                    'notes' => "Abono a saldo de cliente {$client->name}",
                    'cash_register_id' => $cashRegister->id,
                ]);
            }

            // 5. Actualizar la deuda total del cliente y el efectivo en caja
            $client->debt -= $originalAmount;
            $client->save();

            $cashRegister->current_cash += $originalAmount;
            $cashRegister->save();
        });
    }

    public function show($encoded_client_id)
    {
        // Decodificar el ID
        $decoded_client_id = base64_decode($encoded_client_id);
        $store_id = auth()->user()->store_id;
        $client = Client::find($decoded_client_id);
        $clients = Client::where('store_id', $store_id)->latest()->get(['id', 'name']);
        $installments = $client->installments()->latest('created_at')->get()->groupBy(function ($installment) {
            return Carbon::parse($installment->created_at)->isoFormat('DD MMMM, YYYY h:mm a');
        });
        // return $installments;
        return inertia('Client/Show', compact('client', 'clients', 'installments'));
    }

    public function edit($encoded_client_id)
    {
        // Decodificar el ID
        $decoded_client_id = base64_decode($encoded_client_id);

        $client = Client::find($decoded_client_id);

        return inertia('Client/Edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'company' => 'nullable|string|max:150',
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:10',
            'notes' => 'nullable|string|max:255',
            'street' => $request->addAddress ? 'required|string|max:255' : 'nullable',
            'suburb' => $request->addAddress ? 'required|string|max:255' : 'nullable',
            'ext_number' => $request->addAddress ? 'required|string|max:8' : 'nullable',
            'int_number' => 'nullable|string|max:8',
            'postal_code' => $request->addAddress ? 'required|string|max:6' : 'nullable',
            'town' => $request->addAddress ? 'required|string|max:100' : 'nullable',
            'polity_state' => $request->addAddress ? 'required|string|max:100' : 'nullable',
            'razon_social' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
            'rfc' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
            'tax_regime' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
        ]);

        $client->update($request->all());

        return to_route('clients.index');
    }

    public function destroy(Client $client)
    {
        $client->delete();
    }

    public function getDataForTable()
    {
        $perPage = request('pageSize', 100);
        $page = request('page', 1);

        $clients = Client::where('store_id', auth()->user()->store_id)
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $data = [
            'clients' => $clients->items(),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $clients->total(),
            ]
        ];

        return response()->json(compact('data'));
    }

    public function searchClient(Request $request)
    {
        $query = $request->input('query');

        $clients = Client::where('store_id', auth()->user()->store_id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('phone', 'like', "%$query%")
                    ->orWhere('rfc', 'like', "%$query%");
            })
            ->get();

        return response()->json(['items' => $clients]);
    }

    public function PrintCreditHistorical(Client $client)
    {
        return inertia('Client/PrintCreditHistorical', compact('client'));
    }

    public function PrintCashHistorical(Client $client)
    {
        return inertia('Client/PrintCashHistorical', compact('client'));
    }

    // API
    public function getClientSales(Client $client)
    {
        $sales = $client->sales()
            ->with(['cashRegister:id,name', 'user:id,name'])
            ->where('store_id', auth()->user()->store_id)
            ->get();

        $this->addCreditDataToSales($sales);

        $items = $this->getGroupedSalesByDate($sales, null, null, true, null);

        return response()->json(['items' => $items]);
    }

    public function getClientQuotes(Client $client)
    {
        $quotes = $client->quotes()
            ->where('store_id', auth()->user()->store_id)
            ->get();

        return response()->json(['items' => $quotes]);
    }

    public function getClientRentals(Client $client)
    {
        $rentals = $client->rentals()
            ->with(['product'])
            ->where('store_id', auth()->user()->store_id)
            ->get();

        return response()->json(['items' => $rentals]);
    }

    public function getClientInfo(Client $client)
    {
        return response()->json(compact('client'));
    }

    // private
    /**
     * Agrupa las ventas por fecha, incluyendo ventas normales, cotizaciones y ventas en línea.
     *
     * @param Collection|null $sales Ventas normales de tienda.
     * @param Collection|null $onlineSales Ventas en línea.
     * @param Collection|null $installments Ventas a plazos.
     * @param bool $returnSales Indica si se deben retornar los detalles de las ventas agrupadas.
     * @return Collection
     */
    private function getGroupedSalesByDate(
        $sales = null,
        $onlineSales = null,
        $installments = null,
        bool $returnSales = false,
        $serviceOrders = null,
    ): Collection {
        // Asegurarse de que las colecciones no sean nulas
        $sales = collect($sales);
        $onlineSales = collect($onlineSales);
        $installments = collect($installments);
        $serviceOrders = collect($serviceOrders);
        // 1. Filtrar ventas en línea (solo las entregadas o reembolsadas)
        $filteredOnlineSales = $onlineSales->filter(function ($onlineSale) {
            return $onlineSale->delivered_at || $onlineSale->refunded_at;
        });

        // 2. Combinar todas las ventas para agruparlas por fecha
        $allSales = $sales->merge($filteredOnlineSales)->merge($serviceOrders);

        return $allSales->groupBy(function ($sale) {
            if (isset($sale->current_price)) {
                return Carbon::parse($sale->created_at)->toDateString(); // Venta normal o cotización
            } elseif ($sale instanceof OnlineSale) { // Venta en linea
                if ($sale->delivered_at) {
                    return Carbon::parse($sale->delivered_at)->toDateString();
                } elseif ($sale->refunded_at) {
                    return Carbon::parse($sale->refunded_at)->toDateString();
                } else {
                    return Carbon::parse($sale->created_at)->toDateString(); // fallback
                }
            } else {
                return Carbon::parse($sale->paid_at)->toDateString(); // Orden de servicio
            }
        })->map(function ($dailySales) use ($returnSales, $installments) {
            // 3. Clasificar las ventas del día
            $sale = $dailySales[0];
            if (isset($sale['current_price'])) {
                $date = Carbon::parse($sale['created_at'])->toDateString(); // Venta normal o cotización
            } elseif ($sale instanceof OnlineSale) { // Venta en linea
                if ($sale['delivered_at']) {
                    $date = Carbon::parse($sale['delivered_at'])->toDateString();
                } elseif ($sale['refunded_at']) {
                    $date = Carbon::parse($sale['refunded_at'])->toDateString();
                } else {
                    $date = Carbon::parse($sale['created_at'])->toDateString(); // fallback
                }
            } else {
                $date = Carbon::parse($sale['paid_at'])->toDateString(); // Orden de servicio
            }
            $normalSales = $dailySales->filter(fn($sale) => isset($sale->current_price) && !$sale->quote_id);
            $quoteSales = $dailySales->filter(fn($sale) => isset($sale->current_price) && $sale->quote_id);
            $onlineSales = $dailySales->filter(fn($sale) => $sale instanceof OnlineSale);
            $serviceOrdersGroup = $dailySales->filter(fn($sale) => $sale instanceof \App\Models\ServiceReport);


            // 4. Calcular totales y cantidades
            $totalSale = $normalSales->sum(fn($sale) => $this->calculateSaleAmount($sale));
            $totalQuotesSale = $quoteSales->sum(fn($sale) => $this->calculateQuoteSaleAmount($sale));
            $totalServiceOrders = $serviceOrdersGroup->sum('total_cost'); // total_cost + advance_payment
            $totalOnlineSale = $onlineSales->sum(function ($onlineSale) {
                return ($onlineSale->status == 'Entregado' || $onlineSale->status == 'Reembolsado')
                    ? $onlineSale->total + $onlineSale->delivery_price
                    : 0;
            });

            // 5. Calcular cantidades de productos
            $totalQuantityNormalSale = $normalSales->sum('quantity');
            $totalQuantityQuoteSale = $quoteSales->sum('quantity');
            $totalQuantityOnlineSale = $onlineSales->sum(fn($onlineSale) => count($onlineSale->products ?? []));
            $totalServiceFolios = $serviceOrdersGroup->unique('folio')->count();

            // 6. Calcular folios únicos
            $normalFolios = $normalSales->unique('folio')->count();
            $quoteFolios = $quoteSales->unique('folio')->count();
            $onlineFolios = $onlineSales->count();


            // 7. Agrupar ventas por folio si returnSales es true
            $normalSalesByFolio = $returnSales ? $normalSales->groupBy('folio')->map(fn($folioSales) => $this->formatNormalSaleByFolio($folioSales)) : [];
            $quoteSalesByFolio = $returnSales ? $quoteSales->groupBy('folio')->map(fn($folioSales) => $this->formatQuoteSaleByFolio($folioSales)) : [];

            return [
                'date' => $date,
                'total_normal_quantity' => $totalQuantityNormalSale,
                'total_quote_quantity' => $totalQuantityQuoteSale,
                'total_online_quantity' => $totalQuantityOnlineSale,
                'total_sale' => $totalSale,
                'total_quotes_sale' => $totalQuotesSale,
                'online_sales_total' => $totalOnlineSale,
                'total_day_sale' => $totalSale + $totalQuotesSale + $totalOnlineSale,
                'normal_folios' => $normalFolios,
                'quote_folios' => $quoteFolios,
                'online_folios' => $onlineFolios,
                'sales' => $normalSalesByFolio,
                'quote_sales' => $quoteSalesByFolio,
                'online_sales' => $returnSales ? $onlineSales->values() : [],
                'installments' => $returnSales ? $installments->values() : [],
                'total_service_orders' => $totalServiceOrders,
                'service_folios' => $totalServiceFolios,
                'total_day_sale' => $totalSale + $totalQuotesSale + $totalOnlineSale + $totalServiceOrders,
                'service_orders' => $returnSales ? $serviceOrdersGroup->values() : [],
            ];
        });
    }

    /**
     * Calcula el monto de una venta normal o de cotización.
     *
     * @param object $sale
     * @return float
     */
    private function calculateSaleAmount(object $sale): float
    {
        $priceToUse = ($sale->discounted_price !== null && $sale->discounted_price >= 0)
            ? $sale->discounted_price
            : $sale->current_price;

        return $sale->quantity * $priceToUse;
    }

    /**
     * Calcula el monto de una venta de cotización, incluyendo descuentos e IVA.
     *
     * @param object $sale
     * @return float
     */
    private function calculateQuoteSaleAmount(object $sale): float
    {
        $baseAmount = $this->calculateSaleAmount($sale);
        $discounted = 0;

        if (isset($sale->quote->is_percentage_discount)) {
            $discounted = $sale->quote->is_percentage_discount
                ? $sale->quote->percentage * 0.01 * $sale->quote->total
                : $sale->quote->discount;
        }

        $iva = 0;
        if (isset($sale->quote->iva_included) && $sale->quote->iva_included === false) {
            $iva = $sale->quote->total * 0.16;
        }

        return $baseAmount + ($sale->quote->delivery_cost ?? 0) + $iva - $discounted;
    }

    /**
     * Formatea los datos de una venta normal agrupada por folio.
     *
     * @param Collection $folioSales
     * @return array
     */
    private function formatNormalSaleByFolio(Collection $folioSales): array
    {
        $firstSale = $folioSales->first();

        return [
            'products' => $folioSales->map(fn($sale) => $this->mapProductDetails($sale))->values(),
            'credit_data' => $firstSale->credit_data,
            'folio' => $firstSale->folio,
            'user_name' => $firstSale->user->name,
            'client_name' => $firstSale->client?->name ?? 'Público en general',
            'total_sale' => $folioSales->sum(fn($sale) => $this->calculateSaleAmount($sale)),
        ];
    }

    /**
     * Formatea los datos de una venta de cotización agrupada por folio.
     *
     * @param Collection $folioSales
     * @return array
     */
    private function formatQuoteSaleByFolio(Collection $folioSales): array
    {
        $firstSale = $folioSales->first();

        return [
            'products' => $folioSales->map(fn($sale) => $this->mapProductDetails($sale, true))->values(),
            'credit_data' => $firstSale->credit_data,
            'quote' => $firstSale->quote,
            'folio' => $firstSale->folio,
            'user_name' => $firstSale->user->name,
            'client_name' => $firstSale->client?->name ?? 'Público en general',
            'total_sale' => $folioSales->sum(fn($sale) => $this->calculateQuoteSaleAmount($sale)),
        ];
    }

    /**
     * Mapea los detalles de un producto de venta.
     *
     * @param object $sale
     * @param bool $isQuote Indica si es una venta de cotización para incluir 'quote_id'.
     * @return array
     */
    private function mapProductDetails(object $sale, bool $isQuote = false): array
    {
        $details = [
            'id' => $sale->id,
            'current_price' => $sale->current_price,
            'discounted_price' => $sale->discounted_price,
            'promotions_applied' => $sale->promotions_applied,
            'product_name' => $sale->product_name,
            'product_id' => $sale->product_id,
            'is_global_product' => $sale->is_global_product,
            'quantity' => $sale->quantity,
            'original_price' => $sale->original_price,
            'payment_method' => $sale->payment_method,
            'refunded_at' => $sale->refunded_at,
            'cash_register_id' => $sale->cash_register_id,
            'store_id' => $sale->store_id,
            'created_at' => $sale->created_at,
            'updated_at' => $sale->updated_at,
        ];

        if ($isQuote) {
            $details['quote_id'] = $sale->quote_id;
        }

        return $details;
    }

    private function addCreditDataToSales($sales)
    {
        $folios = $sales->unique('folio')->pluck('folio');

        $folios->each(function ($folio) use ($sales) {
            // Buscar CreditSaleData relacionado usando el folio
            $creditData = CreditSaleData::where([
                'folio' => $folio,
                'store_id' => auth()->user()->store_id,
            ])->first();

            if ($creditData) {
                // Obtener los installments relacionados
                $installments = $creditData->installments;

                // Agregar credit_data a las ventas del mismo folio
                $sales->where('folio', $folio)->each(function ($sale) use ($creditData, $installments) {
                    $sale->credit_data = [
                        'id' => $creditData->id,
                        'expired_date' => $creditData->expired_date,
                        'status' => $creditData->status,
                        'installments' => $installments,
                    ];
                });
            } else {
                // Si no hay credit_data, agregar un array vacío a las ventas del mismo folio
                $sales->where('folio', $folio)->each(function ($sale) {
                    $sale->credit_data = null;
                });
            }
        });
    }
}
