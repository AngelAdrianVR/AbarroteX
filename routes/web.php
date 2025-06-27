<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CashCutController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CashRegisterMovementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountTicketController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\EzyProfileController;
use App\Http\Controllers\GlobalProductController;
use App\Http\Controllers\GlobalProductStoreController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\OnlineSaleController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductBoutiqueController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductHistoryController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\ProntipagosController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RentalPaymentController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ScaleController;
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
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Inertia\Inertia;
use Laravel\Jetstream\Agent;


Route::get('/', function () {
    $agent = new Agent();

    if ($agent->isDesktop() || $agent->isLaptop()) {
        return inertia('Welcome');
    } else {
        return inertia('WelcomeMobile');
    }
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

//Global products routes (Catálgo base)----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('global-products', GlobalProductController::class)->middleware(['auth']);
Route::get('global-products-search', [GlobalProductController::class, 'searchProduct'])->name('global-products.search')->middleware('auth');
Route::post('global-products/update-with-media/{global_product}', [GlobalProductController::class, 'updateWithMedia'])->name('global-products.update-with-media')->middleware('auth');
// Route::get('global-products-select', [GlobalProductController::class, 'selectGlobalProducts'])->name('global-products.select')->middleware('auth');
Route::get('global-products-get-by-page/{currentPage}', [GlobalProductController::class, 'getItemsByPage'])->name('global-products.get-by-page')->middleware('auth');
Route::get('global-products-fetch-product-info/{global_product_id}', [GlobalProductController::class, 'fetchProductInfo'])->name('global-products.fetch-info-product')->middleware('auth');
Route::post('global-products-filter', [GlobalProductController::class, 'filter'])->name('global-products.filter')->middleware('auth');


//products routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::resource('products', ProductController::class)->middleware('auth')->middleware(['auth', 'activeSuscription', 'verified']);
Route::post('products/update-with-media/{product}', [ProductController::class, 'updateWithMedia'])->name('products.update-with-media')->middleware('auth');
Route::put('products-entry/{product_id}', [ProductController::class, 'entryStock'])->name('products.entry')->middleware('auth');
Route::put('products-out/{product_id}', [ProductController::class, 'outStock'])->name('products.out')->middleware('auth');
Route::put('products-inventory-update/{product_id}', [ProductController::class, 'inventoryUpdate'])->name('products.inventory-update')->middleware('auth');
Route::post('products-massive-update-stock', [ProductController::class, 'massiveUpdateStock'])->name('products.massive-update-stock');
Route::put('products-price-update/{product_id}', [ProductController::class, 'priceUpdate'])->name('products.price-update')->middleware('auth');
Route::get('products-search', [ProductController::class, 'searchProduct'])->name('products.search')->middleware('auth');
Route::get('products-get-product-scaned/{product_id}', [ProductController::class, 'getProductScaned'])->name('products.get-product-scaned')->middleware('auth');
Route::get('products-fetch-history/{product_id}/{month}/{year}', [ProductController::class, 'fetchHistory'])->name('products.fetch-history')->middleware('auth');
Route::get('products-get-by-page/{currentPage}', [ProductController::class, 'getItemsByPage'])->name('products.get-by-page')->middleware('auth');
Route::get('products-get-all-until-page/{currentPage}', [ProductController::class, 'getAllUntilPage'])->name('products.get-all-until-page')->middleware('auth');
Route::post('products/import', [ProductController::class, 'import'])->name('products.import')->middleware('auth');
Route::get('products-export', [ProductController::class, 'export'])->name('products.export')->middleware('auth');
Route::get('products-get-all-for-indexedDB', [ProductController::class, 'getAllForIndexedDB'])->name('products.get-all-for-indexedDB')->middleware('auth');
Route::get('products-get-by-id-for-indexedDB/{product}', [ProductController::class, 'getByIdForIndexedDB'])->name('products.get-by-id-for-indexedDB')->middleware('auth');
Route::post('products-get-data-for-products-view', [ProductController::class, 'getDataForProductsView'])->name('products.get-data-for-products-view')->middleware('auth');
Route::post('products-change-price', [ProductController::class, 'changePrice'])->name('products.change-price')->middleware('auth'); //cambia el precio del producto desde el punto de venta
Route::get('products-filter-by-provider', [ProductController::class, 'filterByProvider'])->name('products.filter-by-provider');


// productos de boutique
//-------------------------------------------------------------------------------------------------
Route::resource('boutique-products', ProductBoutiqueController::class)->middleware('auth')->middleware(['auth', 'activeSuscription', 'verified']);
Route::post('boutique-products/update-with-media/{product}', [ProductBoutiqueController::class, 'updateWithMedia'])->name('boutique-products.update-with-media')->middleware('auth');
Route::put('boutique-products-entry', [ProductBoutiqueController::class, 'entryStock'])->name('boutique-products.entry')->middleware('auth');
Route::put('boutique-products-out', [ProductBoutiqueController::class, 'outStock'])->name('boutique-products.out')->middleware('auth');
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
Route::put('global-product-store-out/{global_product_store_id}', [GlobalProductStoreController::class, 'outStock'])->name('global-product-store.out')->middleware('auth');
Route::put('global-product-store-inventory-update/{global_product_store_id}', [GlobalProductStoreController::class, 'inventoryUpdate'])->name('global-product-store.inventory-update')->middleware('auth');
Route::put('global-product-store-price-update/{global_product_store_id}', [GlobalProductStoreController::class, 'priceUpdate'])->name('global-product-store.price-update')->middleware('auth');
Route::get('global-product-store-fetch-history/{global_product_store_id}/{month}/{year}', [GlobalProductStoreController::class, 'fetchHistory'])->name('global-product-store.fetch-history')->middleware('auth');
Route::post('global-product-store-change-price', [GlobalProductStoreController::class, 'changePrice'])->name('global-product-store.change-price')->middleware('auth'); //cambia el precio del producto desde el punto de venta


//categories routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('categories', CategoryController::class)->middleware('auth');


//brands routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('brands', BrandController::class)->middleware('auth');
Route::get('brands-fetch-all', [BrandController::class, 'fetchAll'])->name('brand.fetch-all');



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


//store routes-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('stores', StoreController::class)->middleware(['auth']);
Route::get('stores-get-settings-by-module/{store}/{module}', [StoreController::class, 'getSettingsByModule'])->middleware('auth')->name('stores.get-settings-by-module');
Route::put('stores/toggle-setting-value/{store}/{setting_id}', [StoreController::class, 'toggleSettingValue'])->middleware('auth')->name('stores.toggle-setting-value');
Route::put('stores-update-online-sales-info/{store}', [StoreController::class, 'updateOnlineSalesInfo'])->middleware('auth')->name('stores.update-online-sales-info');
Route::get('stores-fetch-store-info/{store}', [StoreController::class, 'fetchStoreInfo'])->name('stores.fetch-store-info'); //se usa en rutas de tienda en linea donde no hay usuario autenticado
Route::post('stores-store-csf', [StoreController::class, 'storeCSF'])->name('stores.store-csf');
Route::post('stores-update-modules/{store}', [StoreController::class, 'UpdateModules'])->name('store.update-modules');
Route::post('stores/store-logo', [StoreController::class, 'storeLogo'])->name('stores.store-logo');
Route::post('stores/store-banner', [StoreController::class, 'storeBanner'])->name('stores.store-banner');
Route::post('stores/store-online-properties', [StoreController::class, 'storeOnlineProperties'])->name('stores.store-online-properties');
Route::post('/stores/update-settings', [StoreController::class, 'updateSettings'])->name('stores.update-settings');



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
Route::get('role-permission/create-role', [SettingController::class, 'createRole'])->middleware('auth')->name('settings.role-permission.create-role');
Route::get('role-permission/edit-role/{role}', [SettingController::class, 'editRole'])->middleware('auth')->name('settings.role-permission.edit-role');
Route::put('role-permission/{role}/update-role', [SettingController::class, 'updateRole'])->middleware('auth')->name('settings.role-permission.update-role');
Route::post('role-permission/store-role', [SettingController::class, 'storeRole'])->middleware('auth')->name('settings.role-permission.store-role');
Route::delete('role-permission/{role}/destroy-role', [SettingController::class, 'deleteRole'])->middleware('auth')->name('settings.role-permission.delete-role');


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
Route::get('cash-registers-max-cash-notify', [CashRegisterController::class, 'sendMaxCashNotification'])->middleware('auth')->name('cash-registers.max-cash-notify');
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
Route::get('cash-cuts-print/{created_at}', [CashCutController::class, 'print'])->name('cash-cuts.print');


//Tutorial routes-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('tutorials', TutorialController::class)->middleware('auth');
Route::get('/tutorials/{tutorial}/increment-views', [TutorialController::class, 'incrementViews'])->name('tutorials.increment-views');


//Abonos routes-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('installments', InstallmentController::class)->middleware('auth');


//rutas de tallas-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('sizes', SizeController::class)->middleware('auth');


//rutas de colores-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('colors', ColorController::class)->middleware('auth');


//online sales routes----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------
Route::resource('online-sales', OnlineSaleController::class)->middleware(['auth', 'activeSuscription', 'hasModule:Tienda en línea', 'verified'])->except('show');
Route::get('/online-sales/{online_sale}', [OnlineSaleController::class, 'show'])->name('online-sales.show')->middleware(['auth', 'activeSuscription', 'hasModule:Tienda en línea', 'verified', 'isOwnResource']);
Route::get('mx/{slug}/create', [OnlineSaleController::class, 'create'])->name('online-sales.create'); //create para no usar middleware porque no dejaba al cliente finalizar pedido hasta loguearse
Route::post('online-sales/store', [OnlineSaleController::class, 'store'])->name('online-sales.store'); //store para no usar middleware porque no dejaba al cliente finalizar pedido hasta loguearse
Route::get('online-sales-client-index/{encoded_store_id}', [OnlineSaleController::class, 'clientIndexOld'])->name('online-sales.client-index-old'); //index de clientes
Route::get('mx/{slug}', [OnlineSaleController::class, 'clientIndex'])->name('online-sales.client-index'); //index de clientes
Route::post('online-sales/load-more-products', [OnlineSaleController::class, 'loadMoreProducts'])->name('online-sales.load-more-products'); //carga mas products con scroll
Route::get('mx/{slug}/show-l/{product_id}', [OnlineSaleController::class, 'showLocalProduct'])->name('online-sales.show-local-product');
Route::get('mx/{slug}/show-g/{global_product_id}', [OnlineSaleController::class, 'showGlobalProduct'])->name('online-sales.show-global-product');
Route::get('mx/{slug}/cart', [OnlineSaleController::class, 'cartIndex'])->name('online-sales.cart');
Route::get('online-sales-fetch-product/{product_id}/{is_local}', [OnlineSaleController::class, 'fetchProduct'])->name('online-sales.fetch-product');
Route::get('online-sales-search-products/{store_id}', [OnlineSaleController::class, 'searchProducts'])->name('online-sales.search-products');
Route::get('online-sales-filter', [OnlineSaleController::class, 'filterOnlineSales'])->name('online-sales.filter')->middleware('auth');
Route::put('online-sales-update-status/{online_sale}', [OnlineSaleController::class, 'updateOnlineSaleStatus'])->name('online-sales.update-status')->middleware('auth');
Route::get('online-sales-fetch-all-products', [OnlineSaleController::class, 'fetchAllProducts'])->name('online-sales.fetch-all-products');
Route::post('online-sales-get-by-page/{currentPage}', [OnlineSaleController::class, 'getItemsByPage'])->name('online-sales.get-by-page')->middleware('auth');
Route::get('online-sales-get-sales-by-date/{date}', [OnlineSaleController::class, 'getSalesByDate'])->name('online-sales.get-sales-by-date');
Route::post('online-sales/refund/{onlineSale}', [OnlineSaleController::class, 'refund'])->name('online-sales.refund');
Route::post('online-sales/cancel/{onlineSale}', [OnlineSaleController::class, 'cancel'])->name('online-sales.cancel');
Route::get('mx/{slug}/show-service/{service}', [OnlineSaleController::class, 'showService'])->name('online-sales.show-service');
Route::get('online-sales-quote-service/{service}', [OnlineSaleController::class, 'quoteService'])->name('online-sales.quote-service');
Route::get('mx/{slug}/thanks/{encoded_store_id}', [OnlineSaleController::class, 'thanks'])->name('online-sales.thanks');


//Internal invoices routes----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------
// Route::resource('internal-invoices', InternalInvoiceController::class);


//rutas de stripe-----------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::post('/stripe', [StripeController::class, 'index'])->name('stripe.index');
Route::post('/stripe-upgrade-subscription', [StripeController::class, 'upgradeSubscription'])->name('stripe.upgrade-subscription');
Route::post('/stripe-checkout', [StripeController::class, 'checkout'])->name('checkout');
Route::post('/stripe-update-plan-modules-checkout', [StripeController::class, 'updatePlanModulesCheckout'])->name('update-plan-modules-checkout');
Route::get('/stripe-success', [StripeController::class, 'success'])->name('stripe.success');
Route::get('/stripe-update-plan-modules-success', [StripeController::class, 'updatePlanModulesSuccess'])->name('stripe.update-plan-modules-success');
Route::get('/stripe-cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
Route::get('/stripe-error', [StripeController::class, 'error'])->name('stripe.error');


//rutas de reportes de servicio --------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('service-reports', ServiceReportController::class);
Route::get('services-get-by-page/{currentPage}', [ServiceReportController::class, 'getItemsByPage'])->name('service-reports.get-by-page')->middleware('auth');
Route::get('services-search', [ServiceReportController::class, 'searchServiceReport'])->name('service-reports.search')->middleware('auth');
Route::post('services-reports-store-phones-stores', [ServiceReportController::class, 'storePhoneStores'])->name('service-reports.store-phones')->middleware('auth');
Route::post('services-reports-update-phones-stores/{service_order}', [ServiceReportController::class, 'updatePhoneStores'])->name('service-reports.update-phones')->middleware('auth');
Route::post('services-reports-change-status/{service_report}', [ServiceReportController::class, 'changeStatus'])->name('service-reports.change-status')->middleware('auth');
Route::post('services-reports-massive-delete', [ServiceReportController::class, 'massiveDelete'])->name('service-reports.massive-delete')->middleware('auth');


//rutas de báscula ---------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::put('/scale/configure/{user}', [ScaleController::class, 'configure'])->name('scale.configure');
Route::get('/scale/get-ports', [ScaleController::class, 'getAvailablePorts'])->name('scale.get-ports');


// cupones de descuento routes ------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------
// Route::resource('discount-sticket', DiscountTicketController::class)->middleware('auth');
Route::get('discount-tickets/fetch-active-tickets', [DiscountTicketController::class, 'fetchActiveTickets'])->name('discount-tickets.fetch-active-tickets')->middleware('auth');
Route::get('my-referrals/index', [DiscountTicketController::class, 'referralsIndex'])->name('referrals.index')->middleware('auth');


//rutas de partners --------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::resource('partners', PartnerController::class);
Route::get('/partner-register', [PartnerController::class, 'landingCreate'])->name('landing.create-partner');
Route::post('/partner-recover', [PartnerController::class, 'landingRecover'])->name('landing.recover-partner');


//rutas de promociones --------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
Route::get('promotions/local/{product}', [PromotionsController::class, 'localCreate'])->name('promotions.local.create')->middleware(['auth', 'activeSuscription', 'verified']);
Route::get('promotions/global/{product}', [PromotionsController::class, 'globalCreate'])->name('promotions.global.create')->middleware(['auth', 'activeSuscription', 'verified']);
Route::get('promotions/local/{product}/edit', [PromotionsController::class, 'localEdit'])->name('promotions.local.edit')->middleware(['auth', 'activeSuscription', 'verified']);
Route::get('promotions/global/{product}/edit', [PromotionsController::class, 'globalEdit'])->name('promotions.global.edit')->middleware(['auth', 'activeSuscription', 'verified']);
Route::get('promotions-get-match/{query}', [PromotionsController::class, 'getMatches'])->name('promotions.get-match')->middleware('auth');
Route::post('promotions/store', [PromotionsController::class, 'store'])->name('promotions.store')->middleware(['auth', 'activeSuscription', 'verified']);
Route::put('promotions/update', [PromotionsController::class, 'update'])->name('promotions.update')->middleware(['auth', 'activeSuscription', 'verified']);


//rutas de Prontipagos --------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------
Route::get('/prontipagos', [ProntipagosController::class, 'index'])->name('prontipagos.index')->middleware('auth');
Route::get('/prontipagos/token', [ProntipagosController::class, 'getToken'])->name('prontipagos.get-access-token')->middleware('auth');
Route::get('/prontipagos/balance', [ProntipagosController::class, 'getCurrentBalance'])->name('prontipagos.get-current-balance')->middleware('auth');
Route::get('/prontipagos/catalog-products', [ProntipagosController::class, 'getCatalogoProducts'])->name('prontipagos.get-catalog-products')->middleware('auth');
Route::post('/prontipagos/venta', [ProntipagosController::class, 'realizarVenta'])->name('prontipagos.make-sale')->middleware('auth');
Route::get('/prontipagos/sale/satus/{id}', [ProntipagosController::class, 'getSaleStatus'])->name('prontipagos.get-sale-status')->middleware('auth');



// ver tutoriales
Route::get('/started-turtorial/pos', function () {
    if (auth()->user()->store->type == 'Boutique / Tienda de Ropa / Zapatería') {
        return inertia('StartedTutorial/BoutiqueIndex');
    } else {
        return inertia('StartedTutorial/Index');
    }
})->name('started-tutorial');

// eliminacion de archivo desde componente FileView
Route::delete('/media/{media}', function (Media $media) {
    try {
        $media->delete(); // Elimina el archivo y su registro

        return response()->json(['message' => 'Archivo eliminado correctamente.'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al eliminar el archivo.'], 500);
    }
})->name('media.delete-file');


// Actualizar el método de pago de todas las ventas a "Efectivo"
// use Illuminate\Support\Facades\DB;
// Route::get('/actualizar-payment-method', function () {
//     DB::table('sales')->update(['payment_method' => 'Efectivo']);

//     return 'Todos los registros se actualizaron con el método de pago "Efectivo".';
// });