<?php

use App\Livewire\Auth\System\SystemLogin;
use App\Livewire\Dashboard\DashboardComponent;
use App\Livewire\POS\POSComponent;
use App\Livewire\PosSystemTest;
use App\Livewire\Products\ProductManagement;
use App\Livewire\Reports\ReportsComponent;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Shifts\ShiftManagement;
use App\Livewire\Stock\StockManagement;
use App\Livewire\System\CompanyManagement;
use App\Livewire\System\PlanManagement;
use App\Livewire\System\SubscriptionManagement;
use App\Livewire\System\SystemDashboard;
use App\Livewire\System\UserManagement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
     Route::get('/dashboard', DashboardComponent::class)->name('dashboard');
    
    // POS System
    Route::get('/pos', POSComponent::class)->name('pos');
    
    // Shift Management
    Route::get('/shifts', ShiftManagement::class)->name('shifts');
    
    // Products Management
    Route::get('/products', ProductManagement::class)->name('products');
    
    // Stock Management
    Route::get('/stock', StockManagement::class)->name('stock');
    
    // Reports
    Route::get('/reports', ReportsComponent::class)->name('reports');
    
    // Redirect root to dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
});

require __DIR__.'/auth.php';
