<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductHistoryResource;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Expense;
use App\Models\GlobalProductStore;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Size;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductBoutiqueController extends Controller
{
    public function index()
    {
        return inertia('Product/Boutique/Index');
    }

    public function create()
    {
        $store = auth()->user()->store;
        $products_quantity = Product::where('store_id', $store->id)->get()->count();
        $categories = Category::whereIn('business_line_name', [$store->type, $store->id])->latest('id')->get();
        $sizes = Size::whereIn('category', $categories->pluck(['name']))->latest('id')->get();

        return inertia('Product/Boutique/Create', compact('products_quantity', 'categories', 'sizes'));
    }

    public function store(Request $request)
    {
        $store_id = auth()->user()->store_id;

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:products,name,NULL,id,store_id,' . $store_id,
            'code' => ['nullable', 'string', 'max:100', new \App\Rules\UniqueBoutiqueProductCode($request->code)],
            'public_price' => 'required|numeric|min:0|max:999999',
            'cost' => 'nullable|numeric|min:0|max:999999',
            'currency' => 'required|string',
            'description' => 'nullable|string|max:255',
            'has_inventory_control' => 'boolean',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
            'sizes' => 'nullable|array|min:1',
            'sizes.*.size_id' => 'required|numeric|min:1',
            'sizes.*.current_stock' => 'required|numeric|min:0',
            'sizes.*.min_stock' => 'nullable|numeric|min:0',
            'sizes.*.max_stock' => 'nullable|numeric|min:0',
        ], [
            'sizes.*.size_id.required' => 'obligatorio.',
            'sizes.*.current_stock.required' => 'obligatorio.',
            'sizes.*.current_stock.numeric' => 'Este campo debe ser un número.',
            'sizes.*.current_stock.min' => 'Este campo debe ser positivo.',
            'sizes.*.current_stock.max' => 'Este campo debe ser máximo 999,999.99',
            'sizes.*.min_stock.min' => 'Este campo debe ser positivo',
            'sizes.*.max_stock.min' => 'Este campo debe ser positivo',
        ]);

        $mediaItem = null;

        // registrar productos por talla registrada
        foreach ($validated['sizes'] as $key => $product) {
            // si el registro de talla esta vacio, saltar
            if (!$product['size_id']) continue;

            // forzar default de 1 en stock
            $product['current_stock'] = $product['current_stock'] ?? 1;

            $size = Size::where('id', $product['size_id'])->get(['id', 'name', 'short', 'category'])->first()->toArray();

            if ($validated['code']) {
                // Crear codigo unico para talla actual
                $validated['code'] = $validated['code'] . "-$key";
            }

            $new_product = Product::create($validated + [
                'store_id' => $store_id,
                'additional' => $size,
                'current_stock' => $product['current_stock'],
                'min_stock' => $product['min_stock'],
                'max_stock' => $product['max_stock'],
            ]);

            // resetear a nombre y codigo base
            $validated['code'] = $request->code;

            // primer producto registrado
            if ($key == 0) {
                //codifica el id del producto
                $encoded_product_id = base64_encode($new_product->id);

                // Guardar el archivo en la colección 'imageCover' solo en el primer producto registrado
                if ($request->hasFile('imageCover')) {
                    $mediaItem = $new_product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
                }
            } else {
                // Copiar el medio al nuevo producto registrado
                if ($mediaItem) {
                    $new_product->copyMedia($mediaItem->getPath())->usingName($mediaItem->name)->toMediaCollection('imageCover');
                }
            }
        }

        if (!request('stayInView')) {
            return to_route('boutique-products.show', $encoded_product_id);
        }
    }

    public function show($encoded_product_id)
    {
        // Decodificar el ID
        $decoded_product_id = base64_decode($encoded_product_id);

        $cash_register = auth()->user()->cashRegister;
        $product_name = Product::findOrFail($decoded_product_id)?->name;
        $products = Product::with(['category', 'media'])
            ->where([
                'store_id' => auth()->user()->store_id,
                'name' => $product_name
            ])->get();

        return inertia('Product/Boutique/Show', compact('products', 'cash_register'));
    }

    public function edit($encoded_product_id)
    {
        // Decodificar el ID
        $decoded_product_id = base64_decode($encoded_product_id);
        $product_name = Product::findOrFail($decoded_product_id)?->name;
        $products = Product::with(['category', 'media'])
            ->where([
                'store_id' => auth()->user()->store_id,
                'name' => $product_name,
            ])->get();
        $store = auth()->user()->store;
        $categories = Category::whereIn('business_line_name', [$store->type, $store->id])->latest('id')->get();
        $sizes = Size::whereIn('category', $categories->pluck(['name']))->latest('id')->get();

        return inertia('Product/Boutique/Edit', compact('products', 'categories', 'sizes'));
    }

    public function update(Request $request, $product)
    {
        $store_id = auth()->user()->store_id;

        // obtener el nombre original de los productos que fueron editados
        $productName = Product::findOrFail($product)?->name;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['nullable', 'string', 'max:100', new \App\Rules\UniqueBoutiqueProductCode($request->code, $productName)],
            'public_price' => 'required|numeric|min:0|max:999999',
            'cost' => 'nullable|numeric|min:0|max:999999',
            'currency' => 'required|string',
            'description' => 'nullable|string|max:255',
            'has_inventory_control' => 'boolean',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
            'sizes' => 'nullable|array|min:1',
            'sizes.*.id' => 'nullable',
            'sizes.*.size_id' => 'required|numeric|min:1',
            'sizes.*.current_stock' => 'required|numeric|min:0',
            'sizes.*.min_stock' => 'nullable|numeric|min:0',
            'sizes.*.max_stock' => 'nullable|numeric|min:0',
        ], [
            'sizes.*.size_id.required' => 'obligatorio.',
            'sizes.*.current_stock.required' => 'obligatorio.',
            'sizes.*.current_stock.numeric' => 'Este campo debe ser un número.',
            'sizes.*.current_stock.min' => 'Este campo debe ser positivo.',
            'sizes.*.current_stock.max' => 'Este campo debe ser máximo 999,999.99',
            'sizes.*.min_stock.min' => 'Este campo debe ser positivo',
            'sizes.*.max_stock.min' => 'Este campo debe ser positivo',
        ]);

        // Obtener los IDs de las tallas enviadas en la solicitud
        $updatedSizes = collect($request->input('sizes', []))->pluck('id')->toArray();

        // Obtener los productos actuales por nombre
        $existingProducts = Product::where('name', $productName)
            ->where('store_id', $store_id)
            ->get();

        $lastConsecutive = 0;
        if ($existingProducts->isNotEmpty()) {
            // Obtener el código del último registro
            $lastCode = $existingProducts->last()->code;

            // Usar preg_match para obtener el consecutivo
            if (preg_match('/-(\d+)$/', $lastCode, $matches)) {
                $lastConsecutive = (int)$matches[1] + 1;
            }

            // obtener el codigo base del codigo existente (antes de la edicion)
            $firstCode = $existingProducts->first()->code;
            $existingBaseCode = substr($firstCode, 0, strrpos($firstCode, '-'));
        }

        // Eliminar productos que no están en la solicitud
        $existingProducts->whereNotIn('id', $updatedSizes)->each(function ($product) {
            $product->delete();
        });

        // Actualizar o crear productos por talla registrada
        foreach ($validated['sizes'] as $key => $productData) {
            // si el registro de talla está vacío, saltar
            if (!$productData['size_id']) continue;

            // forzar default de 1 en stock
            $productData['current_stock'] = $productData['current_stock'] ?? 1;

            $size = Size::where('id', $productData['size_id'])->first()->toArray();

            $existingProduct = $existingProducts->where('id', $productData['id'])->first();

            if ($existingProduct) {
                if ($validated['code']) {
                    if ($existingBaseCode == $validated['code']) {
                        // Crear código único para talla actual desde el utimo consecutivo
                        $validated['code'] = $existingProduct->code;
                    } else {
                        // Crear codigo unico para talla actual
                        $validated['code'] = $validated['code'] . "-$key";
                    }
                }
                // Actualizar producto existente
                $existingProduct->update($validated + [
                    'additional' => $size,
                    'current_stock' => $productData['current_stock'],
                    'min_stock' => $productData['min_stock'],
                    'max_stock' => $productData['max_stock'],
                ]);
                $new_product = $existingProduct;
            } else {
                if ($validated['code']) {
                    if ($existingBaseCode == $validated['code']) {
                        // Crear código único para talla actual desde el utimo consecutivo
                        $validated['code'] = $validated['code'] . "-$lastConsecutive";
                        // aumentar consecutivo
                        $lastConsecutive++;
                    } else {
                        // Crear codigo unico para talla actual
                        $validated['code'] = $validated['code'] . "-$key";
                    }
                }
                // Crear nuevo producto
                $new_product = Product::create($validated + [
                    'store_id' => $store_id,
                    'additional' => $size,
                    'current_stock' => $productData['current_stock'],
                    'min_stock' => $productData['min_stock'],
                    'max_stock' => $productData['max_stock'],
                ]);
            }
            // resetear a código base
            $validated['code'] = $request->code;

            // primer producto registrado
            if ($key == 0) {
                // codifica el id del producto
                $encoded_product_id = base64_encode($new_product->id);
            }
        }

        return to_route('boutique-products.show', $encoded_product_id);
    }

    public function updateWithMedia(Request $request, $product)
    {
        $store_id = auth()->user()->store_id;

        // obtener el nombre original de los productos que fueron editados
        $productName = Product::findOrFail($product)?->name;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['nullable', 'string', 'max:100', new \App\Rules\UniqueBoutiqueProductCode($request->code, $productName)],
            'public_price' => 'required|numeric|min:0|max:999999',
            'cost' => 'nullable|numeric|min:0|max:999999',
            'currency' => 'required|string',
            'description' => 'nullable|string|max:255',
            'has_inventory_control' => 'boolean',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
            'sizes' => 'nullable|array|min:1',
            'sizes.*.id' => 'nullable',
            'sizes.*.size_id' => 'required|numeric|min:1',
            'sizes.*.current_stock' => 'required|numeric|min:0',
            'sizes.*.min_stock' => 'nullable|numeric|min:0',
            'sizes.*.max_stock' => 'nullable|numeric|min:0',
        ], [
            'sizes.*.size_id.required' => 'obligatorio.',
            'sizes.*.current_stock.required' => 'obligatorio.',
            'sizes.*.current_stock.numeric' => 'Este campo debe ser un número.',
            'sizes.*.current_stock.min' => 'Este campo debe ser positivo.',
            'sizes.*.current_stock.max' => 'Este campo debe ser máximo 999,999.99',
            'sizes.*.min_stock.min' => 'Este campo debe ser positivo',
            'sizes.*.max_stock.min' => 'Este campo debe ser positivo',
        ]);

        $mediaItem = null;

        // Obtener los IDs de las tallas enviadas en la solicitud
        $updatedSizes = collect($request->input('sizes', []))->pluck('id')->toArray();

        // Obtener los productos actuales por nombre
        $existingProducts = Product::where('name', $productName)
            ->where('store_id', $store_id)
            ->get();

        $lastConsecutive = 0;
        if ($existingProducts->isNotEmpty()) {
            // Obtener el código del último registro
            $lastCode = $existingProducts->last()->code;

            // Usar preg_match para obtener el consecutivo
            if (preg_match('/-(\d+)$/', $lastCode, $matches)) {
                $lastConsecutive = (int)$matches[1] + 1;
            }

            // obtener el codigo base del codigo existente (antes de la edicion)
            $firstCode = $existingProducts->first()->code;
            $existingBaseCode = substr($firstCode, 0, strrpos($firstCode, '-'));
        }

        // Eliminar productos que no están en la solicitud
        $existingProducts->whereNotIn('id', $updatedSizes)->each(function ($product) {
            $product->delete();
        });

        // Actualizar o crear productos por talla registrada
        foreach ($validated['sizes'] as $key => $productData) {
            // si el registro de talla está vacío, saltar
            if (!$productData['size_id']) continue;

            // forzar default de 1 en stock
            $productData['current_stock'] = $productData['current_stock'] ?? 1;

            $size = Size::where('id', $productData['size_id'])->first()->toArray();

            $existingProduct = $existingProducts->where('id', $productData['id'])->first();

            if ($existingProduct) {
                // cambiar codigo unico
                if ($validated['code']) {
                    if ($existingBaseCode == $validated['code']) {
                        // Crear código único para talla actual desde el utimo consecutivo
                        $validated['code'] = $existingProduct->code;
                    } else {
                        // Crear codigo unico para talla actual
                        $validated['code'] = $validated['code'] . "-$key";
                    }
                }
                // Actualizar producto existente
                $existingProduct->update($validated + [
                    'additional' => $size,
                    'current_stock' => $productData['current_stock'],
                    'min_stock' => $productData['min_stock'],
                    'max_stock' => $productData['max_stock'],
                ]);
                $new_product = $existingProduct;
            } else {
                if ($validated['code']) {
                    if ($existingBaseCode == $validated['code']) {
                        // Crear código único para talla actual desde el utimo consecutivo
                        $validated['code'] = $validated['code'] . "-$lastConsecutive";
                        // aumentar consecutivo
                        $lastConsecutive++;
                    } else {
                        // Crear codigo unico para talla actual
                        $validated['code'] = $validated['code'] . "-$key";
                    }
                }
                // Crear nuevo producto
                $new_product = Product::create($validated + [
                    'store_id' => $store_id,
                    'additional' => $size,
                    'current_stock' => $productData['current_stock'],
                    'min_stock' => $productData['min_stock'],
                    'max_stock' => $productData['max_stock'],
                ]);
            }
            // resetear a código base
            $validated['code'] = $request->code;

            // primer producto registrado
            if ($key == 0) {
                // codifica el id del producto
                $encoded_product_id = base64_encode($new_product->id);

                // Eliminar imágenes antiguas solo si se proporcionan nuevas imágenes
                if ($request->hasFile('imageCover')) {
                    $new_product->clearMediaCollection('imageCover');
                }

                // Guardar el archivo en la colección 'imageCover' solo en el primer producto registrado
                if ($request->hasFile('imageCover')) {
                    $mediaItem = $new_product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
                }
            } else {
                // Copiar el medio al nuevo producto registrado
                if ($mediaItem) {
                    $new_product->copyMedia($mediaItem->getPath())->usingName($mediaItem->name)->toMediaCollection('imageCover');
                }
            }
        }

        return to_route('boutique-products.show', $encoded_product_id);
    }

    public function destroy($product)
    {
        $product_name = Product::find($product)->name;

        // automaticamente con un evento registrado en el modelo se actualizan las ventas relacionadas
        // eliminar producto
        $products = Product::where([
            'name' => $product_name,
            'store_id' => auth()->user()->store_id,
        ])->get();

        $products->each(fn ($prd) =>  $prd->delete());
    }

    public function searchProduct(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda en la base de datos local
        $local_products = Product::with(['category:id,name', 'media'])
            ->where('store_id', auth()->user()->store_id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('code', 'like', "%$query%");
            })
            ->latest('id')
            ->get(['id', 'name', 'public_price', 'cost', 'code', 'store_id', 'category_id', 'min_stock', 'max_stock', 'current_stock']);

        $global_products = GlobalProductStore::with(['globalProduct.media', 'globalProduct.category:id,name'])
            ->whereHas('globalProduct', function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%$query%")
                    ->orWhere('code', 'like', "%$query%");
            })
            ->where('store_id', auth()->user()->store_id)
            ->get();

        // Combinar los resultados en una colección
        $combined_products = $local_products->merge($global_products);

        // Tomar solo los primeros 20 elementos del arreglo combinado
        $products = $combined_products->groupBy('name');

        return response()->json(['items' => $products]);
    }

    public function entryStock(Request $request)
    {
        $messages = [
            'sizes.*.quantity.required' => 'obligatorio.',
            'sizes.*.quantity.numeric' => 'Este campo debe ser un número.',
            'sizes.*.quantity.min' => 'Este campo debe ser positivo.',
        ];

        $validated = $request->validate([
            'sizes.*.product_id' => 'required|numeric|min:1',
            'sizes.*.size_name' => 'nullable',
            'sizes.*.quantity' => 'required|numeric|min:1',
        ], $messages);

        foreach ($validated['sizes'] as $entry) {
            $product = Product::find($entry['product_id']);

            // Asegurar convertir la cantidad a un número antes de sumar
            $product->current_stock += floatval($entry['quantity']);

            // Guarda el producto
            $product->save();

            // Crear entrada
            ProductHistory::create([
                'description' => 'Entrada de producto. ' . $entry['quantity'] . ' unidad(es) de talla ' . $entry['size_name'],
                'type' => 'Entrada',
                'historicable_id' => $entry['product_id'],
                'historicable_type' => Product::class
            ]);

            // Crear gasto
            Expense::create([
                'concept' => 'Compra de producto: ' . $product->name . ' talla ' . $entry['size_name'],
                'current_price' => $product->cost ?? 0,
                'quantity' => $entry['quantity'],
                'store_id' => auth()->user()->store_id,
            ]);
        }
    }

    public function fetchHistory($product_name, $month = null, $year = null)
    {
        $products_ids = Product::where('name', $product_name)->get()->pluck('id');
        // Obtener el historial filtrado por el mes y el año proporcionados, o el mes y el año actuales si no se proporcionan
        $query = ProductHistory::whereIn('historicable_id', $products_ids)
            ->where('historicable_type', Product::class);

        if ($month && $year) {
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
        } else {
            // Obtener el mes y el año actuales
            $currentMonth = date('m');
            $currentYear = date('Y');
            $query->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear);
        }

        // Obtener el historial ordenado por fecha de creación
        $product_history = ProductHistoryResource::collection($query->latest('id')->get());

        // Agrupar por mes y año
        $groupedHistory = $product_history->groupBy(function ($item) {
            return $item->created_at->format('F Y');
        });

        // Convertir el grupo en un array
        $groupedHistoryArray = $groupedHistory->toArray();

        return response()->json(['items' => $groupedHistoryArray]);
    }

    // public function getItemsByPage($currentPage)
    // {
    //     $offset = $currentPage * 30;

    //     // obtener todo los productos
    //     $all_products = $this->getAllProducts();
    //     $products = $all_products->splice($offset)->take(30);

    //     return response()->json(['items' => $products]);
    // }

    // public function getAllUntilPage($currentPage)
    // {
    //     $items = $currentPage * 30;

    //     // obtener todo los productos
    //     $all_products = $this->getAllProducts();
    //     $products = $all_products->take($items);

    //     return response()->json(['items' => $products]);
    // }

    public function getAllProducts()
    {
        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::with(['category:id,name', 'media'])
            ->where('store_id', auth()->user()->store_id)
            ->latest('id')
            ->get(['id', 'name', 'public_price', 'cost', 'code', 'store_id', 'category_id', 'min_stock', 'max_stock', 'current_stock']);
        // productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::with(['globalProduct' => ['media', 'category']])->where('store_id', auth()->user()->store_id)->get();

        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $merged = array_merge($local_products->toArray(), $transfered_products->toArray());
        $products = collect($merged);

        return $products;
    }

    public function import(Request $request)
    {
        // Validar el archivo Excel
        $request->validate([
            // 'file' => 'required|mimes:xlsx,xls',
            'file' => 'required',
        ]);

        // Obtener el archivo Excel
        $file = $request->file('file');

        if (is_array($file)) {
            // Si se enviaron múltiples archivos, toma el primero
            $file = reset($file);
        }

        // Guardar el archivo en el almacenamiento temporal de Laravel
        $path = $file->store('temp');

        // Obtener la ruta completa del archivo
        $filePath = Storage::path($path);

        // Cargar el archivo Excel
        $spreadsheet = IOFactory::load($filePath);

        // Obtener la primera hoja de trabajo
        $worksheet = $spreadsheet->getActiveSheet();

        // validar informacion
        $errorsBag = $this->validateProductsFromFile($worksheet);

        // Si hay errores, devolverlos al cliente
        if ($errorsBag) {
            return response()->json(['errors' => $errorsBag], 400);
        } else {
            // Si no hay errores, proceder a guardar en la base de datos
            $this->storeProductsFromFile($worksheet);
        }
    }

    public function export()
    {
        $userCanSeeCost = in_array(auth()->user()->rol, ['Administrador', 'Almacenista']);
        $products = Product::where('store_id', auth()->user()->store_id)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $headers = [
            'A3' => 'Nombre del producto',
            'B3' => 'Categoría',
            'C3' => 'Talla',
            'D3' => 'Precio a público',
            'E3' => 'Precio de compra',
            'F3' => 'Código de producto',
            'G3' => 'Stock mínimo',
            'H3' => 'Stock máximo',
            'I3' => 'Stock actual',
            'J3' => 'Creado el'
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
            // Apply bold style to the header cells
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Add data rows
        $row = 4;
        foreach ($products as $product) {
            $size_short = isset($product->additional['short']) ? "-{$product->additional['short']}" : '';
            $size_name = isset($product->additional['name']) ? "{$product->additional['name']}" : '';
            $size = $size_name . $size_short; 
            $base_code = substr($product->code, 0, strrpos($product->code, '-'));

            $sheet->setCellValue('A' . $row, $product->name);
            $sheet->setCellValue('B' . $row, $product->category->name);
            $sheet->setCellValue('C' . $row, $size);
            $sheet->setCellValue('D' . $row, $product->public_price);
            if ($userCanSeeCost) $sheet->setCellValue('E' . $row, $product->cost);
            $sheet->setCellValue('F' . $row, $base_code);
            $sheet->setCellValue('G' . $row, $product->min_stock);
            $sheet->setCellValue('H' . $row, $product->max_stock);
            $sheet->setCellValue('I' . $row, $product->current_stock);
            $sheet->setCellValue('J' . $row, $product->created_at->isoFormat('DD MMMM YYYY, h:mm a'));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        // Prepare the response as a streamed response
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'EZY_productos.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Cache-Control' => 'max-age=1',
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            'Cache-Control' => 'cache, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    public function getDataForProductsView()
    {
        $page = request('page') * 30; //recibe el current page para cargar la cantidad de productos correspondiente
        $all_products = $this->getAllProducts();
        $total_products = $all_products->unique('name')->count();
        $total_local_products = $all_products->whereNull('global_product_id')->unique('name')->count();
        $total_local_products_with_sizes = $all_products->whereNull('global_product_id')->count();

        //tomar solo primeros 30 productos
        $products = $all_products->groupBy('name')->take($page);

        return response()->json(compact('products', 'total_products', 'total_local_products', 'total_local_products_with_sizes'));
    }

    private function validateProductsFromFile($worksheet)
    {
        // Almacenar los errores de validación
        $errorsBag = [];

        $columnNames = [];
        // Obtener datos y guardar en la base de datos
        foreach ($worksheet->getRowIterator() as $row) {
            if ($row->getRowIndex() < 4) {
                continue; // Saltar las primeras 3 filas
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            if ($row->getRowIndex() == 4) {
                // Obtener los nombres de columna de la fila 4 del archivo Excel
                foreach ($cellIterator as $cell) {
                    $columnNames[] = $cell->getValue();
                }
                continue;
            }

            $data = [];
            $currentColumn = 0;
            foreach ($cellIterator as $cell) {
                $columnName = $columnNames[$currentColumn++]; // Obtener el nombre de columna
                $data[$columnName] = $cell->getValue(); // Asignar el valor al array asociativo usando el nombre de columna
            }

            // Validar los datos
            $validator = Validator::make($data, [
                $columnNames[0] => 'required|string|max:120', //name
                $columnNames[1] => 'required|string|max:255', //category
                $columnNames[2] => 'required|string|max:255', // size
                $columnNames[3] => 'required|numeric|min:0|max:999999.99', // public_price
                $columnNames[4] => $data[$columnNames[4]] ? 'numeric|min:0|max:999999.99' : '', //cost
                $columnNames[5] => $data[$columnNames[5]]
                    ?  ['max:100', new \App\Rules\UniqueBoutiqueProductCode($data[$columnNames[5]])]
                    : '', //code
                $columnNames[6] => $data[$columnNames[6]] ? 'numeric|min:0|max:999999' : '', //min stock
                $columnNames[7] => $data[$columnNames[7]] ? 'numeric|min:0|max:999999' : '', //max stock
                $columnNames[8] => $data[$columnNames[8]] ? 'numeric|min:0|max:999999' : '', //current stock
            ]);

            // Si la validación falla, almacenar los errores
            if ($validator->fails()) {
                $errorsBag[] = [
                    'row' => $row->getRowIndex(),
                    'errors' => $validator->errors()->all(),
                ];
            }
        }

        return $errorsBag;
    }

    private function storeProductsFromFile($worksheet)
    {
        $base_code = '';
        $consecutive = 0;
        foreach ($worksheet->getRowIterator() as $row) {
            // iniciar codigo en nulo
            $code = null;

            if ($row->getRowIndex() < 4) {
                continue; // Saltar las primeras 3 filas
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            if ($row->getRowIndex() == 4) {
                // Obtener los nombres de columna de la cuarta fila del archivo Excel
                foreach ($cellIterator as $cell) {
                    $columnNames[] = $cell->getValue();
                }
                continue;
            }

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue(); // Asignar el valor al array asociativo usando el nombre de columna
            }

            $store_id = auth()->user()->store_id;
            $category = Category::where(function ($query) use ($store_id){
                $query->where('business_line_name', $store_id)
                    ->orWhere('business_line_name', 'Boutique / Tienda de Ropa / Zapatería');
            })
            ->where('name', $data[1])
            ->first();

            // Si la categoria ingresada por el usuario desde excel no existe, crear uno nuevo
            if (!$category) {
                $category = Category::create([
                    'name' => $data[1],
                    'business_line_name' => auth()->user()->store->id
                ]);
            }
            
            // buscar talla ingresada en BDD
            $size_exploted = explode('-', $data[2]);
            $size_name = $size_exploted[0];
            $size_short = count($size_exploted) == 2 ? $size_exploted[1] : null;
            $size = Size::where(['name' => $size_name, 'category' => $category->name])
            ->when($size_short, function ($query) use ($size_short) {
                $query->where('short', $size_short);
            })
            ->first();
            
            // Si la talla ingresada por el usuario desde excel no existe, crear una nueva
            if (!$size) {
                $size = Size::create([
                    'name' => $size_name,
                    'category' => $category->name,
                    'short' => $size_short,
                ]);
            }

            // Revision de codigos unicos
            if ($base_code == ':*') { //si es la primera iteracion comenzar el codigo base
                $base_code = $data[5];
            }

            if ($data[5]) {
                if ($base_code == $data[5]) {
                    $consecutive++;
                } else {
                    $consecutive = 0;
                    $base_code = $data[5];
                }
                $code = $base_code . "-$consecutive";
            }

            Product::create([
                'name' => $data[0],
                'category_id' => $category->id,
                'additional' => ['id' => $size->id, 'name' => $size->name, 'short' => $size->short],
                'public_price' => $data[3],
                'cost' => $data[4],
                'code' => $code,
                'min_stock' => $data[6],
                'max_stock' => $data[7],
                'current_stock' => $data[8] ?? 1,
                'store_id' => $store_id,
            ]);
        }
    }

    // public function changePrice(Request $request)
    // {
    //     $product = Product::where('store_id', auth()->user()->store_id)->where('name', $request->product['name'])->first();
    //     $product->public_price = floatval($request->newPrice); //$product->public_price = (float) $request->newPrice; tambien se puede de esa manera
    //     $product->save();
    // }
}
