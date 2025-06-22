<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\Client;
use App\Models\CreditSaleData;
use App\Models\GlobalProductStore;
use App\Models\Installment;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Quote;
use App\Models\Sale;
use App\Notifications\BasicNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class QuoteController extends Controller
{
    const STATUS_PAID = 'Pagado';
    const STATUS_PARTIAL = 'Pago parcial';

    public function index()
    {
        $quotes = Quote::where('store_id', auth()->user()->store_id)->latest()->get()->take(20);
        $total_quotes = Quote::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('Quote/Index', compact('quotes', 'total_quotes'));
    }

    public function create()
    {
        $clients = Client::where('store_id', auth()->user()->store_id)->get(['id', 'name', 'company']);

        return inertia('Quote/Create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company' => 'nullable|string|max:255',
            'contact_name' => 'required|string|max:150',
            'phone' => 'nullable|string|min:10|max:10',
            'email' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            'payment_conditions' => 'nullable|string|max:100',
            'expired_date' => 'nullable|date|after:today',
            'iva_included' => 'nullable',
            'has_discount' => 'nullable',
            'is_percentage_discount' => 'nullable',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0|max:9999999',
            'delivery_type' => 'nullable|string|max:255',
            'delivery_cost' => 'nullable|numeric|min:0|max:9999999',
            'total' => 'required|numeric|min:0|max:9999999',
            'products' => 'nullable',
            'services' => 'nullable',
            'show_payment_conditions' => 'boolean',
            'show_address' => 'boolean',
            'show_expiration' => 'boolean',
            'client_id' => 'nullable',
        ]);

        $storeId = auth()->user()->store_id;
        $last_quote = Quote::where('store_id', $storeId)->latest('id')->first();
        $folio = $last_quote ? intval($last_quote->folio) + 1 : 1;
        $validated['delivery_cost'] = $request->delivery1 ?? $request->delivery2;

        Quote::create($validated + [
            'store_id' => auth()->user()->store_id,
            'folio' => $folio,
        ]);

        return to_route('quotes.index');
    }

    public function show($encoded_quote_id)
    {
        $decoded_quote_id = base64_decode($encoded_quote_id);
        $store_id = auth()->user()->store_id;
        $quote = Quote::with(['client', 'store'])->findOrFail($decoded_quote_id);

        // Ruta a la vista de Inertia (ej: 'Quote/Show14.vue')
        $customViewPath = resource_path("js/Pages/Quote/Show{$store_id}.vue");

        // Usar la vista personalizada si existe, sino la predeterminada
        $view = File::exists($customViewPath)
            ? "Quote/Show{$store_id}"
            : "Quote/Show";

        // return $quote;
        return inertia($view, compact('quote'));
    }

    public function edit($encoded_quote_id)
    {
        $clients = Client::where('store_id', auth()->user()->store_id)->get(['id', 'name', 'company']);

        // Decodificar el ID
        $decoded_quote_id = base64_decode($encoded_quote_id);

        $quote = Quote::with(['client:id,name'])
            ->findOrFail($decoded_quote_id);

        return inertia('Quote/Edit', compact('quote', 'clients'));
    }


    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'company' => 'nullable|string|max:255',
            'contact_name' => 'required|string|max:150',
            'phone' => 'nullable|string|min:10|max:10',
            'email' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            'payment_conditions' => 'nullable|string|max:100',
            'expired_date' => 'nullable|date|after:today',
            'iva_included' => 'nullable',
            'has_discount' => 'nullable',
            'is_percentage_discount' => 'nullable',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0|max:9999999',
            'delivery_type' => 'nullable|string|max:255',
            'delivery_cost' => 'nullable|numeric|min:0|max:9999999',
            'total' => 'required|numeric|min:0|max:9999999',
            'products' => 'nullable',
            'services' => 'nullable',
            'show_payment_conditions' => 'boolean',
            'show_address' => 'boolean',
            'show_expiration' => 'boolean',
            'client_id' => 'nullable',
        ]);

        $validated['delivery_cost'] = $request->delivery1 ?? $request->delivery2;
        $validated['client_id'] = $request->client_id;
        $quote->update($validated);

        //codifica el id del quote
        $encoded_quote_id = base64_encode($quote->id);

        // return to_route('quotes.show', ['quote' => $encoded_quote_id]); //para mandar al show
        return to_route('quotes.index');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
    }

    public function searchQuote(Request $request)
    {
        $query = $request->input('query');
        $storeId = auth()->user()->store_id;

        // Si el query es tipo C-0001, extraer el número
        $folioNumber = null;
        if (preg_match('/^C-(\d{4})$/i', $query, $matches)) {
            $folioNumber = intval(ltrim($matches[1], '0'));
        } elseif (preg_match('/^\d{4}$/', $query)) {
            // Si el query es solo un número de 4 dígitos con ceros a la izquierda
            $folioNumber = intval(ltrim($query, '0'));
        }

        $quotes = Quote::where('store_id', $storeId)
            ->where(function ($q) use ($query, $folioNumber) {
                $q->where('contact_name', 'like', "%$query%")
                    ->orWhere('company', 'like', "%$query%");
                if ($folioNumber !== null) {
                    $q->orWhere('folio', $folioNumber);
                } else {
                    $q->orWhere('folio', 'like', "%$query%");
                }
            })
            ->get();

        return response()->json(['items' => $quotes]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;

        $quotes = Quote::where('store_id', auth()->user()->store_id)->latest()->skip($offset)->take(20)->get();

        return response()->json(['items' => $quotes]);
    }

    // public function updateStatus(Quote $quote, Request $request)
    // {
    //     $quote->update([
    //         'status' => $request->status
    //     ]);

    //     // Crear venta si est[a] pagada la cot
    //     if ($request->status == "Pagado" || $request->status == "Pago parcial") {
    //         $installment = $request->amount < $request->grand_total
    //             ? $request->amount
    //             : null;

    //         // crear al cliente si es abono o si se especificó desde el pago
    //         if ($request->create_client) {
    //             $client = Client::create([
    //                 'company' => $quote->company,
    //                 'name' => $quote->contact_name,
    //                 'phone' => $quote->phone,
    //                 'email' => $quote->email,
    //                 'notes' => 'Cliente agregado automáticamente por pago de cotización',
    //                 'store_id' => auth()->user()->store_id,
    //             ]);

    //             $quote->client_id = $client->id;
    //             $quote->save();
    //         }

    //         // Revisar si tiene adeudo la venta para registrar abono
    //         if ($quote->remainig) {
    //             $this->storeInstallment();
    //         } else {
    //             // Registrar por primera vez la venta
    //             $this->storeEachProductSold($quote->products, $request->payment_method, $quote, $installment, $request->limit_date);
    //         }

    //         if ($installment) {
    //             //envitar negativos
    //             if ($installment > $quote->remaining) {
    //                 $installment = $quote->remaining;
    //             }

    //             // actualizar la cantidad pendiente de pago
    //             if ($quote->remaining) {
    //                 // ya tenia abonos, se resta el abono actual
    //                 $quote->decrement('remaining', $installment);
    //             } else {
    //                 //no tenia abonos, se resta el actual al total
    //                 $quote->remaining = $request->grand_total - $installment;
    //                 $quote->save();
    //             }

    //             // si ya se pagó por completo
    //             if ($quote->remaining == 0) {
    //                 $quote->status = "Pagado";
    //                 $quote->save();
    //             }
    //         }
    //     }
    // }

    /**
     * Update the quote status and handle related operations
     * 
     * @param Quote $quote
     * @param Request $request
     * @return void
     */
    public function updateStatus(Quote $quote, Request $request)
    {
        $this->validateStatusRequest($request);

        // Asegurar que todas las transacciones de base de datos se registran correctamente, si no, no registrar nada
        DB::transaction(function () use ($quote, $request) {
            $quote->update(['status' => $request->status]);

            if ($this->isPaidStatus($request->status)) {
                $this->handlePaidQuote($quote, $request);
            }
        });
    }

    private function validateStatusRequest(Request $request)
    {
        $validStatuses = ['Pagado', 'Pago parcial', 'Rechazada', 'Autorizada', 'Sin enviar a cliente', 'Esperando respuesta'];

        $rules = [
            'status' => ['required', 'string', 'in:' . implode(',', $validStatuses)],
        ];

        // Solo validar amount y payment_method si el estado es de pago
        if (in_array($request->status, ['Pago parcial'])) {
            $rules['amount'] = ['required', 'numeric', 'min:1'];
            $rules['payment_method'] = ['required', 'string', 'in:Efectivo,Tarjeta'];
        }

        // Validar campos adicionales para pagos parciales
        if ($request->status === 'Pago parcial') {
            $rules['limit_date'] = ['nullable', 'date', 'after_or_equal:today'];
        }

        $request->validate($rules);
    }

    private function createClientFromQuote(Quote $quote)
    {
        return Client::create([
            'company' => $quote->company,
            'name' => $quote->contact_name,
            'phone' => $quote->phone,
            'email' => $quote->email,
            'notes' => 'Cliente agregado automáticamente por pago de cotización',
            'store_id' => auth()->user()->store_id,
        ]);
    }

    private function isPaidStatus($status)
    {
        return in_array($status, [self::STATUS_PAID, self::STATUS_PARTIAL]);
    }

    private function handlePaidQuote(Quote $quote, Request $request)
    {
        $installment = $this->calculateInstallment($quote, $request);

        if ($request->create_client) {
            $new_client = $this->createClientFromQuote($quote);
            $quote->client_id = $new_client->id;
        }

        $quote->remaining
            ? $this->storeInstallment($quote, $request, $installment)
            : $this->storeEachProductSold($quote->products, $request, $quote, $installment);

        if ($installment) {
            $this->updateQuoteRemaining($quote, $request, $installment);
        }
    }

    private function calculateInstallment(Quote $quote, Request $request)
    {
        // ya hay abonos registrados
        if ($quote->remaining !== null) {
            return min($request->amount, $quote->remaining);
        } else {
            return $request->amount < $request->grand_total
                ? $request->amount
                : null;
        }
    }

    private function updateQuoteRemaining(Quote $quote, Request $request, $installment)
    {
        // hay abonos
        if ($quote->remaining !== null) {
            $quote->remaining = max(0, $quote->remaining - $installment);
        } else { // primer pago
            $quote->remaining = max(0, $request->grand_total - $installment);
        }

        if ($quote->remaining == 0) {
            $quote->status = self::STATUS_PAID;
        }

        $quote->save();
    }

    private function storeInstallment(Quote $quote, Request $request, $installment)
    {
        $store_id = auth()->user()->store_id;

        // Obtener el folio de venta por cotizacion
        $folio = $quote->sales->first()->folio;

        // Registrar el abono
        $credit_sale_data = CreditSaleData::firstWhere([
            'folio' => $folio,
            'store_id' => $store_id,
        ]);

        Installment::create([
            'amount' => $installment,
            'payment_method' => $request->payment_method,
            'notes' => 'Abono a cotización',
            'credit_sale_data_id' => $credit_sale_data->id,
            'user_id' => auth()->id(),
        ]);

        // Se paga por completo la cotización. Actualizar el status del credito
        if ($quote->remaining <= $installment) {
            $credit_sale_data->status = 'Pagado';
            $credit_sale_data->save();
        }

        // Registrar movimiento de caja si es pago en efectivo
        if ($request->payment_method === 'Efectivo') {
            CashRegisterMovement::create([
                'amount' => $installment,
                'type' => 'Ingreso',
                'notes' => 'Abono adicional hecho en la cotización con folio C-' . str_pad($quote->id, 4, '0'),
                'cash_register_id' => auth()->user()->cash_register_id,
            ]);

            $cash_register = CashRegister::find(auth()->user()->cash_register_id);
            $cash_register->current_cash += $installment;
            $cash_register->save();
        }

        // Actualizar deuda del cliente
        $client = Client::find($quote->client_id);
        $client->debt = max(0, ($client->debt ?? 0) - $installment);
        $client->save();
    }

    private function storeEachProductSold($products_sold, $request, $quote, $installment)
    {
        $store_id = auth()->user()->store_id;
        // Generar un id unico para productos vendidos a este cliente
        $last_sale = Sale::where('store_id', $store_id)->latest('id')->first();
        $folio = $last_sale ? intval($last_sale->folio) + 1 : 1;

        // obtiene la caja registradora asignada al cajero
        $cash_register = CashRegister::find(auth()->user()->cash_register_id);

        foreach ($products_sold as $sale) {
            $product_id = $sale['product_id'];
            $is_global_product = !$sale['isLocal'];
            $current_product = $is_global_product
                ? GlobalProductStore::find($product_id)
                : Product::find($product_id);

            $product_name = $is_global_product
                ? $current_product->globalProduct->name
                : $current_product->name;
            if (auth()->user()->store->type == 'Boutique / Tienda de Ropa / Zapatería') {
                // agregar color y talla al nombre
                $product_name .= "({$current_product['additional']['color']['name']}-{$current_product['additional']['size']['name']})";
            }

            // obtiene un estracto para referir a la promoción en caso de tener
            // $promotions = $this->getPromotion($sale);

            //regiatra cada producto vendido
            Sale::create([
                'current_price' => $sale['price'],
                'quantity' => $sale['quantity'],
                'folio' => $folio,
                'payment_method' => $request->payment_method,
                'product_name' => $product_name,
                'product_id' => $product_id,
                'is_global_product' => $is_global_product,
                'original_price' => $current_product->public_price != $sale['price'] ? $current_product->public_price : 0,
                'client_id' => $quote->client_id,
                'store_id' => $store_id,
                'cash_register_id' => auth()->user()->cash_register_id,
                'user_id' => auth()->id(),
                'quote_id' => $quote->id,
                'created_at' => now(),
            ]);

            //Desontar cantidades del stock de cada producto vendido (sólo si se configura para tomar en cuenta el inventario).
            $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
            // if ($is_inventory_on) {

            if ($current_product->current_stock <= $sale['quantity']) {
                $current_product->update(['current_stock' => 0]);

                if ($is_inventory_on) {
                    // notificar si no hay existencias
                    $title = "Sin stock!";
                    $description = "Te has quedado sin existencias del producto <span class='text-primary'>$product_name</span>";
                    $url =  $is_global_product
                        ? route('global-product-store.show', base64_encode($current_product->id))
                        : route('products.show', base64_encode($current_product->id));

                    auth()->user()->notify(new BasicNotification($title, $description, $url));
                }
            } else {
                $current_product->decrement('current_stock', $sale['quantity']);

                // notificar si ha llegado al limite de existencias bajas
                if ($current_product->current_stock <= $current_product->min_stock && $is_inventory_on) {
                    $title = "Bajo stock!";
                    $description = "Producto <span class='text-primary'>$product_name</span> alcanzó el nivel mínimo establecido";
                    $url =  $is_global_product
                        ? route('global-product-store.show', base64_encode($current_product->id))
                        : route('products.show', base64_encode($current_product->id));
                    auth()->user()->notify(new BasicNotification($title, $description, $url));
                }
            }
            // }
            //Registra el historial de venta de cada producto
            $size = '';
            if ($current_product->additional) {
                // agregar color y talla al historial si es que la tiene
                $size = ' talla ' . $current_product->additional['size']['name'] . ' color ' . $current_product->additional['color']['name'];
            }
            ProductHistory::create([
                'description' => 'Registro de venta. ' . $sale['quantity'] . ' pieza(s)' . $size,
                'type' => 'Venta',
                'user_id' => auth()->id(),
                'historicable_id' => $product_id,
                'historicable_type' => $is_global_product
                    ? GlobalProductStore::class
                    : Product::class,
                'created_at' => $created_at ?? now(),
            ]);
        }

        //Crea registro de venta a crédito si así lo fue
        if ($installment) {
            $new_credit_sale_data = CreditSaleData::create([
                'folio' => $folio,
                'store_id' => $store_id,
                'client_id' => $quote->client_id,
                'expired_date' => $request->limit_date,
                'status' => 'Parcial',
            ]);

            // registrar pago parcial o abono
            Installment::create([
                'amount' => $installment,
                'payment_method' => $request->payment_method,
                'notes' => 'Anticipo de la cotización',
                'credit_sale_data_id' => $new_credit_sale_data->id,
                'user_id' => auth()->id(),
            ]);

            //crea un movimiento de caja para guardar el abono si paga en efectivo y no con tarjeta
            CashRegisterMovement::create([
                'amount' => $installment,
                'type' => 'Ingreso',
                'notes' => 'Primer abono hecho en la venta con folio ' . $folio,
                'cash_register_id' => auth()->user()->cash_register_id,
            ]);

            //Suma la cantidad abonada
            $cash_register->current_cash += $installment;
            $cash_register->save();

            // sumar la deuda al cliente tomando en cuenta el abono
            $client = Client::find($quote->client_id);
            Log::info($client);
            if ($client->debt) {
                $client->debt += $request->grand_total - $installment;
            } else {
                $client->debt = $request->grand_total - $installment;
                Log::info($request->grand_total);
                Log::info($installment);
            }

            $client->save();
        } else if ($request->payment_method === 'Efectivo') {
            //Suma la cantidad total de dinero vendido del producto al dinero actual de la caja si el pago fue en efectivo y no con tarjeta
            $cash_register->current_cash += $request->grand_total;
            $cash_register->save();
        }

        return $folio;
    }
}
