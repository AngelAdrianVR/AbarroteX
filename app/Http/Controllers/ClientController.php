<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CreditSaleData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    public function index()
    {
        $clients = Client::where('store_id', auth()->user()->store_id)->latest()->get()->take(20);
        $total_clients = Client::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('Client/Index', compact('clients', 'total_clients'));
    }


    public function create()
    {
        return inertia('Client/Create');
    }


    public function store(Request $request)
    {
        $request->validate([
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

        Client::create($request->all() + ['store_id' => auth()->user()->store_id]);
    }


    public function show($encoded_client_id)
    {
        // Decodificar el ID
        $decoded_client_id = base64_decode($encoded_client_id);

        $client = Client::find($decoded_client_id);
        $clients = Client::where('store_id', auth()->user()->store_id)->latest()->get(['id', 'name']);

        return inertia('Client/Show', compact('client', 'clients'));
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


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;

        $clients = Client::where('store_id', auth()->user()->store_id)->latest()->skip($offset)->take(20)->get();

        return response()->json(['items' => $clients]);
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

        $items = $this->getGroupedSalesByDate($sales, true);

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

    // private
    private function getGroupedSalesByDate($sales, $returnSales = false)
    {
        return $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->toDateString();
        })->map(function ($sales) use ($returnSales) {
            $totalQuantity = $sales->sum('quantity');
            $totalSale = $sales->sum(function ($sale) {
                return $sale->quantity * $sale->current_price;
            });
            $uniqueFolios = $sales->unique('folio')->count();

            $salesByFolio = $sales->groupBy('folio')->map(function ($folioSales) {
                $firstSale = $folioSales->first();

                // Calcular el total de todos los productos en la venta
                $totalSale = $folioSales->sum(function ($sale) {
                    return $sale->quantity * $sale->current_price;
                });

                return [
                    'products' => $folioSales->map(function ($sale) {
                        return [
                            'id' => $sale->id,
                            'current_price' => $sale->current_price,
                            'product_name' => $sale->product_name,
                            'product_id' => $sale->product_id,
                            'is_global_product' => $sale->is_global_product,
                            'quantity' => $sale->quantity,
                            'refunded_at' => $sale->refunded_at,
                            'cash_register_id' => $sale->cash_register_id,
                            'store_id' => $sale->store_id,
                            'created_at' => $sale->created_at,
                            'updated_at' => $sale->updated_at,
                        ];
                    })->values(),
                    'credit_data' => $firstSale->credit_data,
                    'folio' => $firstSale->folio,
                    'user_name' => $firstSale->user->name,
                    'client_name' => $firstSale->client?->name ?? 'Público en general',
                    'total_sale' => $totalSale,
                ];
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_sale' => $totalSale,
                'unique_folios' => $uniqueFolios,
                'sales' => $returnSales ? $salesByFolio : [],
            ];
        });
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
