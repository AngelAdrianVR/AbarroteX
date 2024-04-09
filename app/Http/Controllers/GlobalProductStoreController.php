<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Expense;
use App\Models\GlobalProduct;
use App\Models\GlobalProductStore;
use App\Models\ProductHistory;
use Illuminate\Http\Request;

class GlobalProductStoreController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show($global_product_store_id)
    {   
        $global_product_store = GlobalProductStore::with(['globalProduct'=>['media', 'category', 'brand']])->find($global_product_store_id);

        return inertia('GlobalProductStore/Show', compact('global_product_store'));
    }

    
    public function edit($global_product_store_id)
    {
        $global_product_store = GlobalProductStore::with('globalProduct.media')->find($global_product_store_id);
        $categories = Category::all();
        $brands = Brand::all(['id', 'name']);

        return inertia('GlobalProductStore/Edit', compact('global_product_store', 'categories', 'brands'));
    }

    
    public function update(Request $request, GlobalProductStore $global_product_store)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:100',
            'public_price' => 'required|numeric|min:0|max:9999',
            'cost' => 'nullable|numeric|min:0|max:9999',
            'current_stock' => 'required|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        // //precio actual para checar si se cambió el precio y registrarlo
        // $current_price = $product->public_price;

        // if ($current_price != $request->public_price) {
        //     ProductHistory::create([
        //         'description' => 'Cambio de precio de $' . $current_price . 'MXN a $ ' . $request->public_price . 'MXN.',
        //         'type' => 'Precio',
        //         'product_id' => $product->id
        //     ]);
        // }

        $global_product_store->update($request->except('imageCover'));

        // return to_route('global-product-store.show', $global_product_store->id); descomentar cuando este listo el show de global product store
        return to_route('products.index');
    }

   
    public function destroy(GlobalProductStore $global_product_store)
    {
        $global_product_store->delete();
    }


    public function transferProducts(Request $request)
    {
        // Obtener el arreglo de productos del cuerpo de la solicitud
        $products = $request->input('products');

        // Aquí puedes manipular el arreglo de productos como desees
        // Por ejemplo, puedes iterar sobre el arreglo y hacer lo que necesites
        foreach ($products as $productId) {
            // Se obtiene el producto global con el id recibido
            $product = GlobalProduct::with(['category', 'brand'])->find($productId);

            GlobalProductStore::create([
                'public_price' => $product->public_price,
                'cost' => 0,
                'current_stock' => 1,
                'global_product_id' => $productId,
                'store_id' => auth()->user()->store_id,
            ]);
        }

        // return to_route('products.index');
    }


    public function entryStock(Request $request, $product_id)
    {
        $product = GlobalProductStore::with('globalProduct')->find($product_id);

        // Asegúrate de convertir la cantidad a un número antes de sumar
        $product->current_stock += intval($request->quantity);

        // Guarda el producto
        $product->save();

        // Crear entrada
        // ProductHistory::create([
        //     'description' => 'Entrada de producto. ' . $request->quantity . ' unidades',
        //     'type' => 'Entrada',
        //     'product_id' => $product_id
        // ]);

        // Crear egreso
        Expense::create([
            'concept' => 'Compra de producto: ' . $product->globalProduct->name,
            'current_price' => $product->cost,
            'quantity' => $request->quantity,
            'store_id' => auth()->user()->store_id,
        ]);
    }
}
