<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CashCutController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CashRegisterMovementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\EzyProfileController;
use App\Http\Controllers\GlobalProductController;
use App\Http\Controllers\GlobalProductStoreController;
use App\Http\Controllers\LogoController;
use App\Http\Controllers\OnlineSaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductHistoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StoreController;
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
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth', 'activeSuscription', 'roles:Administrador']);
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


//global-product-store routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('global-product-store', GlobalProductStoreController::class)->middleware(['auth', 'activeSuscription', 'verified']);
Route::get('global-product-select', [GlobalProductStoreController::class, 'selectGlobalProducts'])->name('global-product-store.select')->middleware('auth');
Route::post('global-product-store/transfer', [GlobalProductStoreController::class, 'transfer'])->name('global-product-store.transfer')->middleware('auth');
Route::put('global-product-store-entry/{global_product_store_id}', [GlobalProductStoreController::class, 'entryStock'])->name('global-product-store.entry')->middleware('auth');
Route::get('global-product-store-fetch-history/{global_product_store_id}/{month}/{year}', [GlobalProductStoreController::class, 'fetchHistory'])->name('global-product-store.fetch-history')->middleware('auth');


//categories routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('categories', CategoryController::class)->middleware('auth');


//brands routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('brands', BrandController::class)->middleware('auth');


//sales routes-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::resource('sales', SaleController::class)->middleware('auth')->middleware(['auth', 'activeSuscription', 'roles:Administrador,Cajero', 'verified']);
Route::get('sales-point', [SaleController::class, 'pointIndex'])->name('sales.point')->middleware(['auth', 'activeSuscription', 'verified']);
Route::post('sales-get-by-page/{currentPage}', [SaleController::class, 'getItemsByPage'])->name('sales.get-by-page')->middleware('auth');
Route::get('sales-search', [SaleController::class, 'searchProduct'])->name('sales.search')->middleware('auth');
Route::get('sales-print-ticket/{created_at}', [SaleController::class, 'printTicket'])->middleware('auth')->name('sales.print-ticket');
Route::get('sales-fetch-cash-register-sales/{cash_register_id}', [SaleController::class, 'fetchCashRegisterSales'])->middleware('auth')->name('sales.fetch-cash-register-sales');
Route::post('sales-sync-localstorage', [SaleController::class, 'syncLocalstorage'])->middleware('auth')->name('sales.sync-localstorage');


//expenses routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::resource('expenses', ExpenseController::class)->middleware(['auth', 'activeSuscription', 'roles:Administrador', 'verified']);
Route::get('expenses-get-by-page/{currentPage}', [ExpenseController::class, 'getItemsByPage'])->name('expenses.get-by-page')->middleware('auth');
Route::get('expenses-filter', [ExpenseController::class, 'filterExpenses'])->name('expenses.filter')->middleware('auth');
Route::get('expenses-print-expenses/{expense_id}', [ExpenseController::class, 'printExpenses'])->middleware('auth')->name('expenses.print-expenses');


//history routes---------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('product-histories', ProductHistoryController::class)->middleware('auth');


//store routes-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('stores', StoreController::class)->middleware(['auth']);
Route::get('stores-get-settings-by-module/{store}/{module}', [StoreController::class, 'getSettingsByModule'])->middleware('auth')->name('stores.get-settings-by-module');
Route::put('stores/toggle-setting-value/{store}/{setting_id}', [StoreController::class, 'toggleSettingValue'])->middleware('auth')->name('stores.toggle-setting-value');
Route::put('stores-update-online-sales-info/{store}', [StoreController::class, 'updateOnlineSalesInfo'])->middleware('auth')->name('stores.update-online-sales-info');
Route::get('stores-fetch-store-info/{store}', [StoreController::class, 'fetchStoreInfo'])->middleware('auth')->name('stores.fetch-store-info');


// User routes-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('users', UserController::class)->middleware('auth');
Route::get('users-get-notifications', [UserController::class, 'getNotifications'])->middleware('auth')->name('users.get-notifications');
Route::post('users-read-notifications', [UserController::class, 'readNotifications'])->middleware('auth')->name('users.read-user-notifications');
Route::post('users-delete-notifications', [UserController::class, 'deleteNotifications'])->middleware('auth')->name('users.delete-user-notifications');
Route::put('users-reset-password/{user}', [UserController::class, 'resetPassword'])->middleware('auth')->name('users.reset-password');


//settings routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::resource('settings', SettingController::class)->middleware(['auth', 'activeSuscription', 'roles:Administrador', 'verified']);
Route::get('settings-get-by-module/{module}', [SettingController::class, 'getByModule'])->middleware('auth')->name('settings.get-by-module');


//cards routes----------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::resource('cards', CardController::class)->middleware('auth');


//ezy profile routes-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::put('ezy-profile/update-basic', [EzyProfileController::class, 'updateBasic'])->middleware('auth')->name('ezy-profile.update-basic');
Route::put('ezy-profile/update-suscription', [EzyProfileController::class, 'updateSuscription'])->middleware('auth')->name('ezy-profile.update-suscription');


//soporte routes--------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::get('support/index', [SupportController::class, 'index'])->middleware('auth')->name('supports.index');
Route::get('support/faqs', [SupportController::class, 'faqs'])->middleware('auth')->name('supports.faqs');
Route::get('support/create-report', [SupportController::class, 'createReport'])->middleware('auth')->name('supports.create-report');


//soporte report routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------
Route::resource('support-reports', SupportReportController::class)->middleware('auth');


//payments routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------
Route::resource('payments', PaymentController::class)->middleware('auth');


//Cash register routes--------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------
Route::resource('cash-registers', CashRegisterController::class)->middleware(['auth', 'activeSuscription', 'roles:Administrador,Cajero', 'verified']);
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
Route::get('online-sales-client-index/{store_id}', [OnlineSaleController::class, 'clientIndex'])->name('online-sales.client-index'); //index de clientes
Route::get('online-sales/{offset}{limit}/load-more-products', [OnlineSaleController::class, 'loadMoreProducts'])->name('online-sales.load-more-products'); //carga mas products con scroll
Route::get('online-sales-show-local-product/{product_id}', [OnlineSaleController::class, 'ShowLocalProduct'])->name('online-sales.show-local-product');
Route::get('online-sales-show-global-product/{global_product_id}', [OnlineSaleController::class, 'ShowGlobalProduct'])->name('online-sales.show-global-product');
Route::get('online-sales-cart', [OnlineSaleController::class, 'cartIndex'])->name('online-sales.cart');
Route::get('online-sales-fetch-product/{product_id}/{is_local}', [OnlineSaleController::class, 'fetchProduct'])->name('online-sales.fetch-product');
Route::get('online-sales-search-products/{store_id}', [OnlineSaleController::class, 'searchProducts'])->name('online-sales.search-products');
Route::get('online-sales-get-logo/{store_id}', [OnlineSaleController::class, 'getLogo'])->name('online-sales.get-logo');
Route::get('online-sales-filter', [OnlineSaleController::class, 'filterOnlineSales'])->name('online-sales.filter')->middleware('auth');
Route::put('online-sales-update-status/{online_sale}', [OnlineSaleController::class, 'updateOnlineSaleStatus'])->name('online-sales.update-status')->middleware('auth');
Route::get('online-sales-fetch-all-products', [OnlineSaleController::class, 'fetchAllProducts'])->name('online-sales.fetch-all-products');


// comandos Artisan
Route::get('/backup', function () {
    Artisan::call('storage:link');
    return 'linked!!';
});