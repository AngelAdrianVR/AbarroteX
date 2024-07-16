<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductHistoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\CashRegisterMovement;
use App\Models\Category;
use App\Models\Expense;
use App\Models\GlobalProductStore;
use App\Models\Product;
use App\Models\ProductHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductController extends Controller
{
    public function index()
    {
        return inertia('Product/Index');
    }


    public function create()
    {
        $store_id = auth()->user()->store_id;
        $products_quantity = Product::where('store_id', $store_id)->get()->count();
        $store = auth()->user()->store;
        $categories = Category::whereIn('business_line_name', [$store->type, $store->id])->get();
        $brands = Brand::whereIn('business_line_name', [$store->type, $store->id])->get();

        return inertia('Product/Create', compact('products_quantity', 'categories', 'brands'));
    }


    public function store(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $vailidated = $request->validate([
            'name' => 'required|string|max:100|unique:products,name,NULL,id,store_id,' . $store_id,
            'code' => ['nullable', 'string', 'max:100', new \App\Rules\UniqueProductCode()],
            'public_price' => 'required|numeric|min:0|max:999999',
            'currency' => 'required|string',
            'cost' => 'nullable|numeric|min:0|max:999999',
            'current_stock' => 'nullable|numeric|min:0|max:9999',
            'description' => 'nullable|string|max:255',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
        ]);

        // forzar default de 1 en stock
        $vailidated['current_stock'] = $vailidated['current_stock'] ?? 1;
        $product = Product::create($vailidated + ['store_id' => $store_id]);

        // Guardar el archivo en la colección 'imageCover'
        if ($request->hasFile('imageCover')) {
            $product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
        }

        //codifica el id del producto
        $encoded_product_id = base64_encode($product->id);

        if (!request('stayInView')) {
            return to_route('products.show', $encoded_product_id);
        }
    }

    public function show($encoded_product_id)
    {
        // Decodificar el ID
        $decoded_product_id = base64_decode($encoded_product_id);

        $cash_register = auth()->user()->cashRegister;
        $product = ProductResource::make(Product::with('category', 'brand')
            ->where('store_id', auth()->user()->store_id)
            ->findOrFail($decoded_product_id));

        return inertia('Product/Show', compact('product', 'cash_register'));
    }


    public function edit($encoded_product_id)
    {
        // Decodificar el ID
        $decoded_product_id = base64_decode($encoded_product_id);

        $product = ProductResource::make(Product::with('category', 'brand')
            ->where('store_id', auth()->user()->store_id)
            ->findOrFail($decoded_product_id));
        $store = auth()->user()->store;
        $categories = Category::whereIn('business_line_name', [$store->type, $store->id])->get();
        $brands = Brand::whereIn('business_line_name', [$store->type, $store->id])->get();

        return inertia('Product/Edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:products,name,' . $product->id,
            'code' => ['nullable', 'string', 'max:100', new \App\Rules\UniqueProductCode($product->id)],
            'public_price' => 'required|numeric|min:0|max:999999',
            'currency' => 'required|string',
            'cost' => 'nullable|numeric|min:0|max:999999',
            'description' => 'nullable|string|max:255',
            'current_stock' => 'nullable|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
        ]);

        //precio actual para checar si se cambió el precio y registrarlo
        $current_price = $product->public_price;

        if ($current_price != $request->public_price) {
            ProductHistory::create([
                'description' => 'Cambio de precio de $' . $current_price . 'MXN a $ ' . $request->public_price . 'MXN.',
                'type' => 'Precio',
                'historicable_id' => $product->id,
                'historicable_type' => Product::class
            ]);
        }

        $product->update($request->except('imageCover'));

        // media
        // Eliminar imágenes antiguas solo si se borró desde el input y no se agregó una nueva
        if ($request->imageCoverCleared) {
            $product->clearMediaCollection('imageCover');
        }

        //codifica el id del producto
        $encoded_product_id = base64_encode($product->id);

        return to_route('products.show', ['product' => $encoded_product_id]);
    }

    public function updateWithMedia(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:products,name,' . $product->id,
            'code' => ['nullable', 'string', 'max:100', new \App\Rules\UniqueProductCode($product->id)],
            'public_price' => 'required|numeric|min:0|max:9999',
            'currency' => 'required|string',
            'cost' => 'nullable|numeric|min:0|max:9999',
            'description' => 'nullable|string|max:255',
            'current_stock' => 'nullable|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
        ]);

        //precio actual para checar si se cambió el precio y registrarlo
        $current_price = $product->public_price;
        if ($current_price != $request->public_price) {
            ProductHistory::create([
                'description' => 'Cambio de precio de $' . $current_price . 'MXN a $ ' . $request->public_price . 'MXN.',
                'type' => 'Precio',
                'historicable_id' => $product->id,
                'historicable_type' => Product::class
            ]);
        }

        $product->update($request->except('imageCover'));

        // media ------------
        // Eliminar imágenes antiguas solo si se proporcionan nuevas imágenes
        if ($request->hasFile('imageCover')) {
            $product->clearMediaCollection('imageCover');
        }

        // Guardar el archivo en la colección 'imageCover'
        if ($request->hasFile('imageCover')) {
            $product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
        }

        //codifica el id del producto
        $encoded_product_id = base64_encode($product->id);

        return to_route('products.show', ['product' => $encoded_product_id]);
    }


    public function destroy(Product $product)
    {
        // automaticamente con un evento registrado en el modelo se actualizan las ventas relacionadas
        // eliminar producto
        $product->delete();
    }


    public function searchProduct(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda en la base de datos local
        $local_products = Product::with(['category', 'brand', 'media'])
            ->where('store_id', auth()->user()->store_id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('code', 'like', "%$query%");
            })
            ->get();

        $global_products = GlobalProductStore::with(['globalProduct.media'])
            ->whereHas('globalProduct', function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%$query%")
                    ->orWhere('code', $query);
            })
            ->where('store_id', auth()->user()->store_id)
            ->get();

        // Combinar los resultados en una colección
        $combined_products = $local_products->merge($global_products);

        // Tomar solo los primeros 20 elementos del arreglo combinado
        $products = $combined_products;

        return response()->json(['items' => $products]);
    }

    public function entryStock(Request $request, $product_id)
    {
        $messages = [
            'cash_amount.required_if' => 'El monto a retirar es obligatorio cuando el pago se realiza mediante la caja registradora.',
            'cash_amount.numeric' => 'El monto a retirar debe ser un número.',
            'cash_amount.min' => 'El monto a retirar debe ser al menos 1.',
        ];

        $request->validate([
            'quantity' => 'required|numeric|min:1',
            'is_paid_by_cash_register' => 'boolean',
            'cash_amount' => 'required_if:is_paid_by_cash_register,true|nullable|numeric|min:1',
        ], $messages);
        
        $product = Product::find($product_id);

        // Asegúrate de convertir la cantidad a un número antes de sumar
        $product->current_stock += floatval($request->quantity);

        // Guarda el producto
        $product->save();

        // Crear entrada
        ProductHistory::create([
            'description' => 'Entrada de producto. ' . $request->quantity . ' unidades',
            'type' => 'Entrada',
            'historicable_id' => $product_id,
            'historicable_type' => Product::class
        ]);

        // Crear gasto
        $expense = Expense::create([
            'concept' => 'Compra de producto: ' . $product->name,
            'current_price' => $product->cost ?? 0,
            'quantity' => $request->quantity,
            'store_id' => auth()->user()->store_id,
            'amount_from_cash_register' => $request->cash_amount,
        ]);

        // restar de caja en caso de que el usuario asi lo haya especificado
        if ($request->is_paid_by_cash_register) {
            $unit = $request->quantity == 1 ? 'unidad' : 'unidades';
            $cash_register = auth()->user()->cashRegister;
            $cash_register->decrement('current_cash', $request->cash_amount);

            // crear movimiento de caja
            CashRegisterMovement::create([
                'amount' => $request->cash_amount,
                'type' => 'Retiro',
                'notes' => "Compra de $product->name ($request->quantity $unit)",
                'cash_register_id' => $cash_register->id,
                'expense_id' => $expense->id,
            ]);
        }
    }

    public function fetchHistory($product_id, $month = null, $year = null)
    {
        // Obtener el historial filtrado por el mes y el año proporcionados, o el mes y el año actuales si no se proporcionan
        $query = ProductHistory::where('historicable_id', $product_id)
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

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;

        // obtener todo los productos
        $all_products = $this->getAllProducts();
        $products = $all_products->splice($offset)->take(30);

        return response()->json(['items' => $products]);
    }

    public function getAllUntilPage($currentPage)
    {
        $items = $currentPage * 30;

        // obtener todo los productos
        $all_products = $this->getAllProducts();
        $products = $all_products->take($items);

        return response()->json(['items' => $products]);
    }

    public function getAllProducts()
    {
        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::with(['category:id,name', 'brand:id,name', 'media'])
            ->where('store_id', auth()->user()->store_id)
            ->latest('id')
            ->get(['id', 'name', 'public_price', 'code', 'store_id', 'category_id', 'brand_id', 'min_stock', 'max_stock', 'current_stock']);

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
            'A3' => 'Nombre',
            'B3' => 'Precio a publico',
            'C3' => 'Precio de compra',
            'D3' => 'Codigo',
            'E3' => 'Stock minimo',
            'F3' => 'Stock maximo',
            'G3' => 'Stock actual',
            'H3' => 'Categoria',
            'I3' => 'Proveedor',
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
            $sheet->setCellValue('A' . $row, $product->name);
            $sheet->setCellValue('B' . $row, $product->public_price);
            if ($userCanSeeCost) $sheet->setCellValue('C' . $row, $product->cost);
            $sheet->setCellValue('D' . $row, $product->code);
            $sheet->setCellValue('E' . $row, $product->min_stock);
            $sheet->setCellValue('F' . $row, $product->max_stock);
            $sheet->setCellValue('G' . $row, $product->current_stock);
            $sheet->setCellValue('H' . $row, $product->category->name);
            $sheet->setCellValue('I' . $row, $product->brand->name);
            $sheet->setCellValue('J' . $row, $product->created_at->isoFormat('DD MMMM YYYY'));
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

    public function getAllForIndexedDB()
    {
        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::where('store_id', auth()->user()->store_id)
            ->latest()
            ->get()
            ->map(function ($product) {
                return [
                    'id' => 'local_' . $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'public_price' => $product->public_price,
                    'current_stock' => $product->current_stock,
                    'image_url' => $product->image_url = $product->getFirstMediaUrl('imageCover'),
                ];
            })->toArray();

        // productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::query()
            ->where('store_id', auth()->user()->store_id)
            ->get()
            ->map(function ($tp) {
                return [
                    'id' => 'global_' . $tp->id,
                    'name' => $tp->globalProduct->name,
                    'code' => $tp->globalProduct->code,
                    'public_price' => $tp->public_price,
                    'current_stock' => $tp->current_stock,
                    'image_url' => $tp->globalProduct->getFirstMediaUrl('imageCover'),
                ];
            })->toArray();


        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $products = collect(array_merge($local_products, $transfered_products));

        return response()->json(compact('products', 'local_products', 'transfered_products'));
    }

    public function getDataForProductsView()
    {
        $page = request('page') * 30; //recibe el current page para cargar la cantidad de productos correspondiente
        $all_products = $this->getAllProducts();
        $total_products = $all_products->count();
        $total_local_products = $all_products->whereNull('global_product_id')->count();

        //tomar solo primeros 30 productos
        $products = $all_products->take($page);

        return response()->json(compact('products', 'total_products', 'total_local_products'));
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
                $columnNames[0] => 'required|string|max:120|unique:products,name',
                $columnNames[1] => 'required|numeric|min:0|max:999999',
                $columnNames[2] => $data[$columnNames[2]] ? 'numeric|min:0|max:999999' : '',
                $columnNames[3] => $data[$columnNames[3]]
                    ?  ['max:100', new \App\Rules\UniqueProductCode()]
                    : '',
                $columnNames[4] => $data[$columnNames[4]] ? 'numeric|min:0|max:999999' : '',
                $columnNames[5] => $data[$columnNames[5]] ? 'numeric|min:0|max:999999' : '',
                $columnNames[6] => $data[$columnNames[6]] ? 'numeric|min:0|max:999999' : '',
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
        foreach ($worksheet->getRowIterator() as $row) {
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

            Product::create([
                'name' => $data[0],
                'public_price' => $data[1],
                'cost' => $data[2] ?? 1,
                'code' => $data[3],
                'min_stock' => $data[4] ?? 1,
                'max_stock' => $data[5] ?? 1,
                'current_stock' => $data[6] ?? 1,
                'store_id' => auth()->user()->store_id,
                'category_id' => 1, //Abarrotes por defecto
                'brand_id' => 1, //Generico por defecto
            ]);
        }
    }

    public function changePrice(Request $request)
    {
        $product = Product::where('store_id', auth()->user()->store_id)->where('name', $request->product['name'])->first();
        $product->public_price = floatval($request->newPrice); //$product->public_price = (float) $request->newPrice; tambien se puede de esa manera
        $product->save();
    }
}
