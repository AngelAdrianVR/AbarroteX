<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CashCutController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CashRegisterMovementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\EzyProfileController;
use App\Http\Controllers\GlobalProductController;
use App\Http\Controllers\GlobalProductStoreController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\InternalInvoiceController;
use App\Http\Controllers\LogoController;
use App\Http\Controllers\OnlineSaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductBoutiqueController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductHistoryController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RentalPaymentController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SettingHistoryController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SupportReportController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


//Dashboard routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth', 'activeSuscription', 'hasModule:Reportes']);
    Route::get('dashboard-get-day-data/{date}', [DashboardController::class, 'getDayData'])->name('dashboard.get-day-data');
    Route::get('dashboard-get-week-data/{date}', [DashboardController::class, 'getWeekData'])->name('dashboard.get-week-data');
    Route::get('dashboard-get-month-data/{date}', [DashboardController::class, 'getMonthData'])->name('dashboard.get-month-data');
});

// Route::get('update-folio', function () {
//     // Obtener las ventas de la tienda con id 5 y tomar los primeros 15
//     $sales = Sale::where('store_id', 5)->get()->skip(5797);

//     // Agrupar las ventas por 'created_at' truncado a segundos
//     $groupedSales = $sales->groupBy(function ($sale) {
//         return Carbon\Carbon::parse($sale->created_at)->format('Y-m-d H:i:s');
//     });

//     // Folio inicial
//     $folio = 3121;

//     // Iterar sobre cada grupo de ventas y actualizar el folio
//     foreach ($groupedSales as $group) {
//         foreach ($group as $sale) {
//             $sale->update(['folio' => $folio]);
//         }
//         // Incrementar el folio para el próximo grupo
//         $folio++;
//     }

//     return 'Folios actualizados correctamente!.';
// });

// Route::get('update-from-json', function () {
//     // Ruta al archivo JSON
//     $filePath = public_path('files/sales1.json');

//     // Verificar si el archivo existe
//     if (!Illuminate\Support\Facades\File::exists($filePath)) {
//         return 'Archivo JSON no encontrado. ' . $filePath;
//     }

//     // Leer el contenido del archivo JSON
//     $jsonData =  Illuminate\Support\Facades\File::get($filePath);
//     $items = json_decode($jsonData, true);

//     // Verificar si la decodificación fue exitosa
//     if (json_last_error() !== JSON_ERROR_NONE) {
//         return 'Error al decodificar el archivo JSON.';
//     }

//     foreach ($items as $itemData) {
//         // $prd = App\Models\GlobalProductStore::where('store_id', 5)
//         //     ->whereHas('globalProduct', function ($q) use ($itemData) {
//         //         $q->where('code', $itemData['code']);
//         //     })->first();

//         // if (!$prd) {
//         //     // Manejar el caso cuando el producto no se encuentra, si es necesario
//         //     continue;
//         // }

//         App\Models\Sale::create($itemData);
//     }

//     return 'items migrados correctamente!.';
// });

// ---------email de prueba
// Route::get('email-test', function () {
//     $admin = App\Models\Admin::find(1);
//     $title = "Nuevo pago registrado";
//     $description = "La tienda 'tienda de prueba' ha pagado una suscripción 'mensual' ($ 199.00).";
//     $url = 'http://localhost:8000/stores';
//     $admin->notify(new App\Notifications\BasicNotification($title, $description, $url));

//     return "Email sent to $admin->name successfuly!";
// });


// Route::get('add-categories', function () {
//     $categories = [
//         'Ropa de mujer' => ['0' => null, '1' => null, '3' => null, '5' => null, '7' => null, '9' => null, '11' => null, '13' => null, '15' => null, 'Extra chica' => 'XS', 'Chica' => 'S', 'Mediana' => 'M', 'Grande' => 'L', 'Exta grande' => 'XL', 'Doble exta grande' => '2XL', 'Triple exta grande' => '3XL'],
//         'Ropa de hombre' => ['28' => null, '29' => null, '30' => null, '31' => null, '32' => null, '33' => null, '34' => null, '36' => null, '38' => null, '40' => null, 'Extra chica' => 'XS', 'Chica' => 'S', 'Mediana' => 'M', 'Grande' => 'L', 'Exta grande' => 'XL', 'Doble exta grande' => '2XL', 'Triple exta grande' => '3XL'],
//         'Ropa de niño(a)' => ['2' => null, '4' => null, '6' => null, '8' => null, '10' => null, '12' => null, '14' => null],
//         'Calzado de mujer' => ['23' => null, '23.5' => null, '24' => null, '24.5' => null, '25' => null, '25.5' => null, '26' => null, '26.5' => null, '27' => null, '27.5' => null, '28' => null],
//         'Calzado de hombre' => ['23' => null, '23.5' => null, '24' => null, '24.5' => null, '25' => null, '25.5' => null, '26' => null, '26.5' => null, '27' => null, '27.5' => null, '28' => null, '28.5' => null, '29' => null, '29.5' => null, '30' => null, '30.5' => null, '31' => null],
//         'Calzado de niño(a)' => ['13' => null, '13.5' => null, '14' => null, '14.5' => null, '15' => null, '15.5' => null, '16' => null, '16.5' => null, '17' => null, '17.5' => null, '18' => null, '18.5' => null, '19' => null, '19.5' => null, '20' => null, '21.5' => null, '22' => null],
//         'Accesorios' => ['Chica' => 'S', 'Mediana' => 'M', 'Grande' => 'L'],
//         'Lencería' => ['32A' => null, '32B' => null, '34B' => null,'34C' => null, '36C' => null, '36D' => null, '38D' => null, '40E' => null, 'Chica' => 'S', 'Mediana' => 'M', 'Grande' => 'L', 'Extra grande' => 'XL', 'Doble extra grande' => '2XL'],
//     ];

//     foreach ($categories as $category => $sizes) {
//         Category::create([
//             'name' => $category,
//             'business_line_name' => 'Boutique / Tienda de Ropa / Zapatería',
//         ]);

//         foreach ($sizes as $size => $short) {
//             Size::create([
//                 'name' => $size,
//                 'short' => $short,
//                 'category' => $category,
//             ]);
//         }
//     }

//     return "Categorias agregadas!";
// });

//Global products routes (Catálgo base)----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('global-products', GlobalProductController::class)->middleware(['auth']);
Route::get('global-products-search', [GlobalProductController::class, 'searchProduct'])->name('global-products.search')->middleware('auth');
Route::post('global-products/update-with-media/{global_product}', [GlobalProductController::class, 'updateWithMedia'])->name('global-products.update-with-media')->middleware('auth');
// Route::get('global-products-select', [GlobalProductController::class, 'selectGlobalProducts'])->name('global-products.select')->middleware('auth');
Route::get('global-products-get-by-page/{currentPage}', [GlobalProductController::class, 'getItemsByPage'])->name('global-products.get-by-page')->middleware('auth');
Route::get('global-products-fetch-product-info/{global_product_id}', [GlobalProductController::class, 'fetchProductInfo'])->name('global-products.fetch-info-product')->middleware('auth');
Route::get('global-products-filter', [GlobalProductController::class, 'filter'])->name('global-products.filter')->middleware('auth');


//products routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::resource('products', ProductController::class)->middleware('auth')->middleware(['auth', 'activeSuscription', 'verified']);
Route::post('products/update-with-media/{product}', [ProductController::class, 'updateWithMedia'])->name('products.update-with-media')->middleware('auth');
Route::put('products-entry/{product_id}', [ProductController::class, 'entryStock'])->name('products.entry')->middleware('auth');
Route::get('products-search', [ProductController::class, 'searchProduct'])->name('products.search')->middleware('auth');
Route::get('products-get-product-scaned/{product_id}', [ProductController::class, 'getProductScaned'])->name('products.get-product-scaned')->middleware('auth');
Route::get('products-fetch-history/{product_id}/{month}/{year}', [ProductController::class, 'fetchHistory'])->name('products.fetch-history')->middleware('auth');
Route::get('products-get-by-page/{currentPage}', [ProductController::class, 'getItemsByPage'])->name('products.get-by-page')->middleware('auth');
Route::get('products-get-all-until-page/{currentPage}', [ProductController::class, 'getAllUntilPage'])->name('products.get-all-until-page')->middleware('auth');
Route::post('products/import', [ProductController::class, 'import'])->name('products.import')->middleware('auth');
Route::get('products-export', [ProductController::class, 'export'])->name('products.export')->middleware('auth');
Route::get('products-get-all-for-indexedDB', [ProductController::class, 'getAllForIndexedDB'])->name('products.get-all-for-indexedDB')->middleware('auth');
Route::post('products-get-data-for-products-view', [ProductController::class, 'getDataForProductsView'])->name('products.get-data-for-products-view')->middleware('auth');
Route::post('products-change-price', [ProductController::class, 'changePrice'])->name('products.change-price')->middleware('auth'); //cambia el precio del producto desde el punto de venta


// productos de boutique
//-------------------------------------------------------------------------------------------------
Route::resource('boutique-products', ProductBoutiqueController::class)->middleware('auth')->middleware(['auth', 'activeSuscription', 'verified']);
Route::post('boutique-products/update-with-media/{product}', [ProductBoutiqueController::class, 'updateWithMedia'])->name('boutique-products.update-with-media')->middleware('auth');
Route::put('boutique-products-entry', [ProductBoutiqueController::class, 'entryStock'])->name('boutique-products.entry')->middleware('auth');
Route::get('boutique-products-search', [ProductBoutiqueController::class, 'searchProduct'])->name('boutique-products.search')->middleware('auth');
Route::get('boutique-products-get-product-scaned/{product_id}', [ProductBoutiqueController::class, 'getProductScaned'])->name('boutique-products.get-product-scaned')->middleware('auth');
Route::get('boutique-products-fetch-history/{product_name}/{month}/{year}', [ProductBoutiqueController::class, 'fetchHistory'])->name('boutique-products.fetch-history')->middleware('auth');
Route::get('boutique-products-get-by-page/{currentPage}', [ProductBoutiqueController::class, 'getItemsByPage'])->name('boutique-products.get-by-page')->middleware('auth');
Route::get('boutique-products-get-all-until-page/{currentPage}', [ProductBoutiqueController::class, 'getAllUntilPage'])->name('boutique-products.get-all-until-page')->middleware('auth');
Route::post('boutique-products/import', [ProductBoutiqueController::class, 'import'])->name('boutique-products.import')->middleware('auth');
Route::get('boutique-products-export', [ProductBoutiqueController::class, 'export'])->name('boutique-products.export')->middleware('auth');
Route::get('boutique-products-get-all-for-indexedDB', [ProductBoutiqueController::class, 'getAllForIndexedDB'])->name('boutique-products.get-all-for-indexedDB')->middleware('auth');
Route::post('boutique-products-get-data-for-products-view', [ProductBoutiqueController::class, 'getDataForProductsView'])->name('boutique-products.get-data-for-products-view')->middleware('auth');
Route::post('boutique-products-change-price', [ProductBoutiqueController::class, 'changePrice'])->name('boutique-products.change-price')->middleware('auth'); //cambia el precio del producto desde el punto de venta
Route::get('boutique-products-get-by-name/{product_name}', [ProductBoutiqueController::class, 'getByName'])->name('boutique-products.get-by-name')->middleware('auth');


//rentals routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::resource('rentals', RentalController::class)->middleware(['auth', 'activeSuscription', 'hasModule:Renta de productos', 'verified']);
Route::put('rentals/update-status/{rental}', [RentalController::class, 'updateStatus'])->name('rentals.update-status')->middleware(['auth', 'activeSuscription', 'verified']);
Route::get('rentals-search', [RentalController::class, 'searchRent'])->name('rentals.search')->middleware('auth');
Route::get('rentals-get-by-page/{currentPage}', [RentalController::class, 'getItemsByPage'])->name('rentals.get-by-page')->middleware('auth');
Route::get('rentals-print-contract/{rental}', [RentalController::class, 'printContract'])->name('rentals.print-contract')->middleware('auth');


//rental payments routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::resource('rental-payments', RentalPaymentController::class)->middleware(['auth', 'activeSuscription', 'verified']);


//services routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::resource('services', ServiceController::class)->middleware('auth')->middleware(['auth', 'activeSuscription', 'hasModule:Servicios', 'verified']);
Route::get('services-get-by-page/{currentPage}', [ServiceController::class, 'getItemsByPage'])->name('services.get-by-page')->middleware('auth');
Route::get('services-search', [ServiceController::class, 'searchService'])->name('services.search')->middleware('auth');
Route::get('services-fetch-all-products', [ServiceController::class, 'fetchAllServices'])->name('services.fetch-all-services');
Route::post('services/update-with-media/{service}', [ServiceController::class, 'updateWithMedia'])->name('services.update-with-media')->middleware('auth');


//global-product-store routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('global-product-store', GlobalProductStoreController::class)->middleware(['auth', 'activeSuscription', 'verified']);
Route::get('global-product-store-get-data-for-base-catalog-view', [GlobalProductStoreController::class, 'getDataForBaseCatalogView'])->name('global-product-store.get-data-for-base-catalog-view')->middleware('auth');
Route::post('global-product-store/transfer', [GlobalProductStoreController::class, 'transfer'])->name('global-product-store.transfer')->middleware('auth');
Route::put('global-product-store-entry/{global_product_store_id}', [GlobalProductStoreController::class, 'entryStock'])->name('global-product-store.entry')->middleware('auth');
Route::get('global-product-store-fetch-history/{global_product_store_id}/{month}/{year}', [GlobalProductStoreController::class, 'fetchHistory'])->name('global-product-store.fetch-history')->middleware('auth');
Route::post('global-product-store-change-price', [GlobalProductStoreController::class, 'changePrice'])->name('global-product-store.change-price')->middleware('auth'); //cambia el precio del producto desde el punto de venta


//categories routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('categories', CategoryController::class)->middleware('auth');


//brands routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('brands', BrandController::class)->middleware('auth');


//sales routes-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::resource('sales', SaleController::class)->except('show')->middleware('auth')->middleware(['auth', 'activeSuscription', 'hasModule:Ventas registradas', 'verified']);
Route::get('sales/{date}/{cashRegisterId}', [SaleController::class, 'show'])->name('sales.show')->middleware(['auth', 'activeSuscription', 'verified']);
Route::get('sales-point', [SaleController::class, 'pointIndex'])->name('sales.point')->middleware(['auth', 'activeSuscription', 'verified']);
Route::post('sales-get-by-page/{currentPage}', [SaleController::class, 'getItemsByPage'])->name('sales.get-by-page')->middleware('auth');
Route::get('sales-search', [SaleController::class, 'searchProduct'])->name('sales.search')->middleware('auth');
Route::get('sales-print-ticket/{folio}', [SaleController::class, 'printTicket'])->middleware('auth')->name('sales.print-ticket');
Route::get('sales-fetch-cash-register-sales/{cash_register_id}', [SaleController::class, 'fetchCashRegisterSales'])->middleware('auth')->name('sales.fetch-cash-register-sales');
Route::post('sales-sync-localstorage', [SaleController::class, 'syncLocalstorage'])->middleware('auth')->name('sales.sync-localstorage');
Route::post('sales/refund/{saleFolio}', [SaleController::class, 'refund'])->middleware('auth')->name('sales.refund');
Route::post('sales/update-group-sale', [SaleController::class, 'updateGroupSale'])->middleware('auth')->name('sales.update-group-sale');


//expenses routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::resource('expenses', ExpenseController::class)->middleware(['auth', 'activeSuscription', 'hasModule:Gastos', 'verified']);
Route::delete('expenses/delete-day/{expense}', [ExpenseController::class, 'deleteDayExpenses'])->name('expenses.delete-day')->middleware('auth');
Route::get('expenses-get-by-page/{currentPage}', [ExpenseController::class, 'getItemsByPage'])->name('expenses.get-by-page')->middleware('auth');
Route::get('expenses-filter', [ExpenseController::class, 'filterExpenses'])->name('expenses.filter')->middleware('auth');
Route::get('expenses-print-expenses/{expense_id}', [ExpenseController::class, 'printExpenses'])->middleware('auth')->name('expenses.print-expenses');


//quotes routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::resource('quotes', QuoteController::class)->middleware(['auth', 'activeSuscription', 'hasModule:Cotizaciones', 'verified']);
Route::get('quotes-search', [QuoteController::class, 'searchQuote'])->name('quotes.search')->middleware('auth');
Route::get('quotes-get-by-page/{currentPage}', [QuoteController::class, 'getItemsByPage'])->name('quotes.get-by-page')->middleware('auth');
Route::post('quotes-update-status/{quote}', [QuoteController::class, 'updateStatus'])->name('quotes.update-status')->middleware('auth');


//product history routes---------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('product-histories', ProductHistoryController::class)->middleware('auth');


//setting history routes---------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('setting-histories', SettingHistoryController::class)->middleware('auth');
Route::put('role-permission/{role}/edit-role', [SettingController::class, 'updateRole'])->middleware('auth')->name('settings.role-permission.update-role');
Route::post('role-permission/store-role', [SettingController::class, 'storeRole'])->middleware('auth')->name('settings.role-permission.store-role');
Route::delete('role-permission/{role}/destroy-role', [SettingController::class, 'deleteRole'])->middleware('auth')->name('settings.role-permission.delete-role');


//store routes-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('stores', StoreController::class)->middleware(['auth']);
Route::get('stores-get-settings-by-module/{store}/{module}', [StoreController::class, 'getSettingsByModule'])->middleware('auth')->name('stores.get-settings-by-module');
Route::put('stores/toggle-setting-value/{store}/{setting_id}', [StoreController::class, 'toggleSettingValue'])->middleware('auth')->name('stores.toggle-setting-value');
Route::put('stores-update-online-sales-info/{store}', [StoreController::class, 'updateOnlineSalesInfo'])->middleware('auth')->name('stores.update-online-sales-info');
// Route::put('stores-update-printer-config/{store}', [StoreController::class, 'updatePrinterConfig'])->middleware('auth')->name('stores.update-printer-config');
Route::get('stores-fetch-store-info/{store}', [StoreController::class, 'fetchStoreInfo'])->name('stores.fetch-store-info');
Route::post('stores-store-csf', [StoreController::class, 'storeCSF'])->name('stores.store-csf');
Route::post('stores-update-modules/{store}', [StoreController::class, 'UpdateModules'])->name('store.update-modules');


// User routes-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('users', UserController::class)->middleware('auth');
Route::get('users-get-notifications', [UserController::class, 'getNotifications'])->middleware('auth')->name('users.get-notifications');
Route::post('users-read-notifications', [UserController::class, 'readNotifications'])->middleware('auth')->name('users.read-user-notifications');
Route::post('users-delete-notifications', [UserController::class, 'deleteNotifications'])->middleware('auth')->name('users.delete-user-notifications');
Route::get('users-get-online-sales-notifications', [UserController::class, 'getOnlineSalesNotifications'])->middleware('auth')->name('users.get-online-sales-notifications');
Route::post('users-read-online-sales-notifications', [UserController::class, 'readOnlineSalesNotifications'])->middleware('auth')->name('users.read-user-online-sales-notifications');
Route::put('users-reset-password/{user}', [UserController::class, 'resetPassword'])->middleware('auth')->name('users.reset-password');
Route::put('tutorials-completed', [UserController::class, 'tutorialsCompleted'])->name('users.tutorials-completed')->middleware('auth');
Route::put('users-update-printer-config/{user}', [UserController::class, 'updatePrinterConfig'])->middleware('auth')->name('users.update-printer-config');
Route::put('users-save-printer-config/{user}', [UserController::class, 'savePrinter'])->middleware('auth')->name('users.save-printer-config');


//settings routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::resource('settings', SettingController::class)->middleware(['auth', 'activeSuscription', 'hasModule:Configuraciones', 'verified']);
Route::get('settings-get-by-module/{module}', [SettingController::class, 'getByModule'])->middleware('auth')->name('settings.get-by-module');


//cards routes----------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::resource('cards', CardController::class)->middleware('auth');


//clients routes----------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------
Route::resource('clients', ClientController::class)->middleware(['auth', 'activeSuscription', 'hasModule:Clientes', 'verified']);
Route::get('clients-get-by-page/{currentPage}', [ClientController::class, 'getItemsByPage'])->name('clients.get-by-page')->middleware('auth');
Route::get('clients-search', [ClientController::class, 'searchClient'])->name('clients.search')->middleware('auth');
Route::get('clients-print-credit-historial/{client}', [ClientController::class, 'PrintCreditHistorical'])->name('clients.print-credit-historial')->middleware('auth');
Route::get('clients-print-cash-historial/{client}', [ClientController::class, 'PrintCashHistorical'])->name('clients.print-cash-historial')->middleware('auth');
Route::get('clients-get-client-sales/{client}', [ClientController::class, 'getClientSales'])->name('clients.get-client-sales')->middleware('auth');
Route::get('clients-get-client-quotes/{client}', [ClientController::class, 'getClientQuotes'])->name('clients.get-client-quotes')->middleware('auth');
Route::get('clients-get-client-rentals/{client}', [ClientController::class, 'getClientRentals'])->name('clients.get-client-rentals')->middleware('auth');
Route::get('clients-get-client-info/{client}', [ClientController::class, 'getClientInfo'])->name('clients.get-client-info')->middleware('auth');


//ezy profile routes-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::put('ezy-profile/update-basic', [EzyProfileController::class, 'updateBasic'])->middleware('auth')->name('ezy-profile.update-basic');
Route::put('ezy-profile/update-suscription', [EzyProfileController::class, 'updateSuscription'])->middleware('auth')->name('ezy-profile.update-suscription');


//soporte routes--------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::get('support/index', [SupportController::class, 'index'])->middleware('auth')->name('supports.index');
Route::get('support/faqs', [SupportController::class, 'faqs'])->middleware('auth')->name('supports.faqs');
Route::get('support/suscription', [SupportController::class, 'suscription'])->middleware('auth')->name('supports.suscription');
Route::get('support/create-report', [SupportController::class, 'createReport'])->middleware('auth')->name('supports.create-report');


//soporte report routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------
Route::resource('support-reports', SupportReportController::class)->middleware('auth');


//payments routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------
Route::resource('payments', PaymentController::class)->middleware('auth');
Route::post('payments/store-invoice/{payment}', [PaymentController::class, 'storeInvoice'])->name('payments.store-invoice');


//Cash register routes--------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------
Route::resource('cash-registers', CashRegisterController::class)->middleware(['auth', 'activeSuscription', 'hasModule:Caja', 'verified']);
Route::get('cash-registers-fetch-cash-register/{cash_register_id}', [CashRegisterController::class, 'fetchCashRegister'])->middleware('auth')->name('cash-registers.fetch-cash-register');
Route::put('cash-registers-asign/{user}/{cash_register_id}', [CashRegisterController::class, 'asignCashRegister'])->middleware('auth')->name('cash-registers.asign');


//Cash register movements routes--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('cash-register-movements', CashRegisterMovementController::class)->middleware('auth');
Route::get('cash-register-movements-fetch-total-cash-movements/{cash_register_id}', [CashRegisterMovementController::class, 'fetchTotalCashMovements'])->middleware('auth')->name('cash-register-movements.fetch-total-cash-movements');
Route::get('cash-register-movements-fetch-current-movements/{cash_register_id}', [CashRegisterMovementController::class, 'fetchCurrentMovements'])->middleware('auth')->name('cash-register-movements.fetch-current-movements');


//Cash cuts routes----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('cash-cuts', CashCutController::class)->middleware(['auth', 'activeSuscription']);
Route::get('cash-cuts-fetch-total-sales-for-cash-cut/{cash_register_id}', [CashCutController::class, 'fetchTotalSaleForCashCut'])->middleware('auth')->name('cash-cuts.fetch-total-sales-for-cash-cut');
Route::get('cash-cuts-filter', [CashCutController::class, 'filterCashCuts'])->name('cash-cuts.filter')->middleware('auth');
Route::get('cash-cuts-get-by-page/{currentPage}', [CashCutController::class, 'getItemsByPage'])->name('cash-cuts.get-by-page')->middleware('auth');
Route::get('cash-cuts-get-movements/{cash_cut}', [CashCutController::class, 'getCashCutMovements'])->name('cash-cuts.get-movements')->middleware('auth');


//Tutorial routes-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('tutorials', TutorialController::class)->middleware('auth');


//Abonos routes-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('installments', InstallmentController::class)->middleware('auth');


//rutas de tallas-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('sizes', SizeController::class)->middleware('auth');


//rutas de colores-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('colors', ColorController::class)->middleware('auth');


//Banners online store routes------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('banners', BannerController::class)->middleware('auth');
Route::post('banners/update-with-media/{banner}', [BannerController::class, 'updateWithMedia'])->name('banners.update-with-media');


//logos online store routes------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('logos', LogoController::class)->middleware('auth');
Route::post('logos/update-with-media/{logo}', [LogoController::class, 'updateWithMedia'])->name('logos.update-with-media');


//online sales routes----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------
Route::resource('online-sales', OnlineSaleController::class);
Route::get('online-sales/create', [OnlineSaleController::class, 'create'])->name('online-sales.create'); //create para no usar middleware porque no dejaba al cliente finalizar pedido hasta loguearse
Route::post('online-sales/store', [OnlineSaleController::class, 'store'])->name('online-sales.store'); //store para no usar middleware porque no dejaba al cliente finalizar pedido hasta loguearse
Route::get('online-sales-client-index/{encoded_store_id}', [OnlineSaleController::class, 'clientIndex'])->name('online-sales.client-index'); //index de clientes
Route::post('online-sales/load-more-products', [OnlineSaleController::class, 'loadMoreProducts'])->name('online-sales.load-more-products'); //carga mas products con scroll
Route::get('online-sales-show-local-product/{product_id}', [OnlineSaleController::class, 'ShowLocalProduct'])->name('online-sales.show-local-product');
Route::get('online-sales-show-global-product/{global_product_id}', [OnlineSaleController::class, 'ShowGlobalProduct'])->name('online-sales.show-global-product');
Route::get('online-sales-cart', [OnlineSaleController::class, 'cartIndex'])->name('online-sales.cart');
Route::get('online-sales-fetch-product/{product_id}/{is_local}', [OnlineSaleController::class, 'fetchProduct'])->name('online-sales.fetch-product');
Route::get('online-sales-search-products/{store_id}', [OnlineSaleController::class, 'searchProducts'])->name('online-sales.search-products');
Route::get('online-sales-get-logo/{store_id}', [OnlineSaleController::class, 'getLogo'])->name('online-sales.get-logo');
Route::get('online-sales-filter', [OnlineSaleController::class, 'filterOnlineSales'])->name('online-sales.filter')->middleware('auth');
Route::put('online-sales-update-status/{online_sale}', [OnlineSaleController::class, 'updateOnlineSaleStatus'])->name('online-sales.update-status')->middleware('auth');
Route::get('online-sales-fetch-all-products', [OnlineSaleController::class, 'fetchAllProducts'])->name('online-sales.fetch-all-products');
Route::post('online-sales-get-by-page/{currentPage}', [OnlineSaleController::class, 'getItemsByPage'])->name('online-sales.get-by-page')->middleware('auth');
Route::get('online-sales-get-sales-by-date/{date}', [OnlineSaleController::class, 'getSalesByDate'])->name('online-sales.get-sales-by-date');
Route::post('online-sales/refund/{onlineSale}', [OnlineSaleController::class, 'refund'])->name('online-sales.refund');
Route::post('online-sales/cancel/{onlineSale}', [OnlineSaleController::class, 'cancel'])->name('online-sales.cancel');
Route::get('online-sales-show-service/{service}', [OnlineSaleController::class, 'showService'])->name('online-sales.show-service');
Route::get('online-sales-quote-service/{service}', [OnlineSaleController::class, 'quoteService'])->name('online-sales.quote-service');


//Internal invoices routes----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------
// Route::resource('internal-invoices', InternalInvoiceController::class);


//rutas de stripe-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::post('/stripe', [StripeController::class, 'index'])->name('stripe.index');
Route::post('/stripe-checkout', [StripeController::class, 'checkout'])->name('checkout');
Route::get('/stripe-success', [StripeController::class, 'success'])->name('stripe.success');
Route::get('/stripe-cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
Route::get('/stripe-error', [StripeController::class, 'error'])->name('stripe.error');


//rutas de reportes de servicio --------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('service-reports', ServiceReportController::class);
Route::get('services-get-by-page/{currentPage}', [ServiceReportController::class, 'getItemsByPage'])->name('service-reports.get-by-page')->middleware('auth');
Route::get('services-search', [ServiceReportController::class, 'searchServiceReport'])->name('service-reports.search')->middleware('auth');


// comandos Artisan
Route::get('/backup', function () {
    Artisan::call('storage:link');
    return 'linked!!';
});
