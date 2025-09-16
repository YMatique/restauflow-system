<?php

use App\Livewire\Auth\System\SystemLogin;
use App\Livewire\Dashboard\DashboardComponent;
use App\Livewire\Inventory\InventoryCreation;
use App\Livewire\Inventory\InventoryManagement;
use App\Livewire\POS\POSComponent;
use App\Livewire\PosSystemTest;
use App\Livewire\Products\ProductManagement;
use App\Livewire\Reports\ReportsComponent;
use App\Livewire\Restflow\Dashboard;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Shifts\ShiftManagement;
use App\Livewire\Stock\ShowProductStock;
use App\Livewire\Stock\StockDetails;
use App\Livewire\Stock\StockManagement;
use App\Livewire\System\CompanyManagement;
use App\Livewire\System\PlanManagement;
use App\Livewire\System\SubscriptionManagement;
use App\Livewire\System\SystemDashboard;
use App\Livewire\System\UserManagement;
use App\Livewire\Teste;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});


Route::get('/system/login', SystemLogin::class)->name('system.login');
Route::post('/system/logout', function () {
    $user = Auth::user();

    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('system.login')->with('message', 'Logout realizado com sucesso.');
}) ->middleware('auth')->name('system.logout');


// ROTAS DO ADMIN MASTER
Route::prefix('system')->name('system.')->group(function(){
    Route::get('dashboard', SystemDashboard::class)->name('dashboard');
    Route::get('companies',CompanyManagement::class)->name('companies');
    Route::get('plans', PlanManagement::class)->name('plans');
    Route::get('subscriptions', SubscriptionManagement::class)->name('subscriptions');
    Route::get('users', UserManagement::class)->name('users');
    // Route::get('companies', \App\Livewire\System\Companies::class)->name('companies');
    // Route::get('plans', \App\Livewire\System\Plans::class)->name('plans');
    // Route::get('subscriptions', \App\Livewire\System\Subscriptions::class)->name('subscriptions');
});

// ROTAS PARA AS EMPRESA
Route::middleware(['auth'])->prefix('restaurant')->name('restaurant.')->group(function(){

    //Leva para Homepage
     Route::get('/homepage', DashboardComponent::class)->name('homepage');

    // POS System
    Route::get('/pos', POSComponent::class)->name('pos');

    // Shift Management
    Route::get('/shifts', ShiftManagement::class)->name('shifts');


    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Products Management
    Route::get('/products', ProductManagement::class)->name('products');

    Route::get('/inventory', InventoryManagement::class)->name('inventory');
    Route::get('/inventory/create', InventoryCreation::class)->name('inventory.create');


    // Stock Management
    Route::get('/stock', StockManagement::class)->name('stocks');
    Route::get('/stock/{stock}', StockDetails::class)->name('stocks.details');
    Route::get('/stock/{stock}/product/{product}', ShowProductStock::class)->name('stock.products');


    // Reports
    Route::get('/reports', ReportsComponent::class)->name('reports');

    Route::get('/teste', Teste::class)->name('teste');

    // Redirect root to dashboard
    Route::get('/', function () {
        return redirect()->route('restaurant.homepage');
    });
});



Route::get('/lang/{locale}', function ($locale) {
    // verifica se o idioma é suportado
    if (in_array($locale, ['en', 'pt'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back(); // volta para a página anterior
})->name('lang.switch');


require __DIR__.'/auth.php';
