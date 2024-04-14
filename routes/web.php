<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EzyProfileController;
use App\Http\Controllers\GlobalProductController;
use App\Http\Controllers\GlobalProductStoreController;
use App\Http\Controllers\MyPaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductHistoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-get-day-data', [DashboardController::class, 'getDayData'])->name('dashboard.get-day-data');
    Route::get('/dashboard-get-week-data', [DashboardController::class, 'getWeekData'])->name('dashboard.get-week-data');
    Route::get('/dashboard-get-month-data', [DashboardController::class, 'getMonthData'])->name('dashboard.get-month-data');
});


//products routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::resource('products', ProductController::class)->middleware('auth');
Route::post('products/update-with-media/{product}', [ProductController::class, 'updateWithMedia'])->name('products.update-with-media')->middleware('auth');
Route::put('products-entry/{product_id}', [ProductController::class, 'entryStock'])->name('products.entry')->middleware('auth');
Route::get('products-search', [ProductController::class, 'searchProduct'])->name('products.search')->middleware('auth');
Route::get('products-get-product-scaned/{product_id}', [ProductController::class, 'getProductScaned'])->name('products.get-product-scaned')->middleware('auth');
Route::get('products-fetch-history/{product_id}', [ProductController::class, 'fetchHistory'])->name('products.fetch-history')->middleware('auth');
Route::get('products-get-by-page/{currentPage}', [ProductController::class, 'getItemsByPage'])->name('products.get-by-page')->middleware('auth');


//Global products routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('global-products', GlobalProductController::class)->middleware('auth');
Route::post('products/update-with-media/{global_product}', [GlobalProductController::class, 'updateWithMedia'])->name('products.update-with-media')->middleware('auth');
Route::get('global-products-select', [GlobalProductController::class, 'selectGlobalProducts'])->name('global-products.select')->middleware('auth');
Route::get('global-products-get-by-page/{currentPage}', [GlobalProductController::class, 'getItemsByPage'])->name('global-products.get-by-page')->middleware('auth');
Route::get('global-products-fetch-product-info/{global_product_id}', [GlobalProductController::class, 'fetchProductInfo'])->name('global-products.fetch-info-product')->middleware('auth');
Route::get('global-products-filter', [GlobalProductController::class, 'filter'])->name('global-products.filter')->middleware('auth');


//global-product-store routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('global-product-store', GlobalProductStoreController::class)->middleware('auth');
Route::post('global-product-store/transfer-products', [GlobalProductStoreController::class, 'transferProducts'])->name('global-product-store.transfer-products')->middleware('auth');
Route::put('global-product-store-entry/{product_id}', [GlobalProductStoreController::class, 'entryStock'])->name('global-product-store.entry')->middleware('auth');


//categories routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('categories', CategoryController::class)->middleware('auth');


//brands routes----------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('brands', BrandController::class)->middleware('auth');


//sales routes-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
Route::resource('sales', SaleController::class)->middleware('auth');
Route::get('sales-point', [SaleController::class, 'pointIndex'])->name('sales.point')->middleware('auth');
Route::get('sales-get-by-page/{currentPage}', [SaleController::class, 'getItemsByPage'])->name('sales.get-by-page')->middleware('auth');
Route::get('sales-search', [SaleController::class, 'searchProduct'])->name('sales.search')->middleware('auth');


//clients routes-------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------
// Route::resource('clients', ClientController::class)->middleware('auth');
// Route::get('clients-get-pendent-amount/{client}', [ClientController::class, 'getClientPendentAmount'])->name('clients.get-pendent-amount')->middleware('auth');
// Route::get('clients-get-by-id/{client}', [ClientController::class, 'getById'])->middleware('auth')->name('clients.get-by-id');
// Route::get('clients-get-by-page/{currentPage}', [ClientController::class, 'getItemsByPage'])->name('clients.get-by-page')->middleware('auth');
// Route::get('clients-search', [ClientController::class, 'search'])->name('clients.search')->middleware('auth');


//payments routes-------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------
// Route::resource('payments', PaymentController::class)->middleware('auth');


//history routes-------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('product-histories', ProductHistoryController::class)->middleware('auth');


//store routes-------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::resource('stores', StoreController::class)->middleware('auth');
Route::get('stores-get-settings-by-module/{store}/{module}', [StoreController::class, 'getSettingsByModule'])->middleware('auth')->name('stores.get-settings-by-module');
Route::put('stores/toggle-setting-value/{store}/{setting_id}', [StoreController::class, 'toggleSettingValue'])->middleware('auth')->name('stores.toggle-setting-value');

// User routes-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
Route::get('users-get-notifications', [UserController::class, 'getNotifications'])->middleware('auth')->name('users.get-notifications');
Route::post('users-read-notifications', [UserController::class, 'readNotifications'])->middleware('auth')->name('users.read-user-notifications');
Route::post('users-delete-notifications', [UserController::class, 'deleteNotifications'])->middleware('auth')->name('users.delete-user-notifications');


//settings routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::resource('settings', SettingController::class)->middleware('auth');
Route::get('settings-get-by-module/{module}', [SettingController::class, 'getByModule'])->middleware('auth')->name('settings.get-by-module');


//cards routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::resource('cards', CardController::class)->middleware('auth');


//ezy profile routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::put('ezy-profile/update-basic', [EzyProfileController::class, 'updateBasic'])->middleware('auth')->name('ezy-profile.update-basic');
Route::put('ezy-profile/update-suscription', [EzyProfileController::class, 'updateSuscription'])->middleware('auth')->name('ezy-profile.update-suscription');


//soporte routes-------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
Route::get('support/index', [SupportController::class, 'index'])->middleware('auth')->name('supports.index');
Route::get('support/faqs', [SupportController::class, 'faqs'])->middleware('auth')->name('supports.faqs');
