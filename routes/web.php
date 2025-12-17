<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/dashboard', function () {
    $productCount = \App\Models\Product::count();
    $stockCount = \App\Models\InventoryStock::sum('quantity');
    $lowStockCount = \App\Models\InventoryStock::where('quantity', '<', 5)->count(); // Alert if < 5

    return view('dashboard', compact('productCount', 'stockCount', 'lowStockCount'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Inventory Routes
Route::middleware('auth')->prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    
    Route::get('/inbound', [InventoryController::class, 'inbound'])->name('inbound');
    Route::post('/inbound', [InventoryController::class, 'storeInbound'])->name('storeInbound');
    
    Route::get('/outbound', [InventoryController::class, 'outbound'])->name('outbound');
    Route::post('/outbound', [InventoryController::class, 'storeOutbound'])->name('storeOutbound');
    
    Route::get('/transfer', [InventoryController::class, 'transfer'])->name('transfer');
    Route::post('/transfer', [InventoryController::class, 'storeTransfer'])->name('storeTransfer');

    Route::get('/history', [InventoryController::class, 'history'])->name('history');
    Route::get('/tracking', [InventoryController::class, 'search'])->name('tracking');
    Route::get('/items/{rak}/{variant}', [InventoryController::class, 'getItems'])->name('items');
    Route::get('/print/{id}', [InventoryController::class, 'print'])->name('print');

});

// Master Data Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::resource('warehouses', App\Http\Controllers\WarehouseController::class);
});

require __DIR__.'/auth.php';
