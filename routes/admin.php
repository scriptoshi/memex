<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FactoriesController;
use App\Http\Controllers\Admin\HoldersController;
use App\Http\Controllers\Admin\LaunchpadsController;
use App\Http\Controllers\Admin\MsgsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TradesController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
#users
Route::name('users.')->controller(DashboardController::class)->group(function () {
    Route::get('/users', 'index')->name('index');
    Route::put('/users/toggle/{user}', 'toggle')->name('toggle');
    Route::put('/users/banned/{user}', 'banned')->name('banned');
});
#users


#factories
Route::name('factories.')->controller(FactoriesController::class)->group(function () {
    Route::get('/factories', 'index')->name('index');
    Route::get('/factories/create', 'create')->name('create');
    Route::post('/factories/store', 'store')->name('store');
    Route::get('/factories/{factory}/show', 'show')->name('show');
    Route::get('/factories/{factory}/edit', 'edit')->name('edit');
    Route::put('/factories/{factory}', 'update')->name('update');
    Route::put('/factories/toggle/{factory}', 'toggle')->name('toggle');
    Route::delete('/factories/{factory}', 'destroy')->name('destroy');
});
#factories



#launchpads
Route::name('launchpads.')->controller(LaunchpadsController::class)->group(function () {
    Route::get('/launchpads', 'index')->name('index');
    Route::put('/launchpads/toggle/{launchpad}', 'toggle')->name('toggle');
    Route::put('/launchpads/kingofthehill/{launchpad}', 'kingofthehill')->name('kingofthehill');
    Route::put('/launchpads/featured/{launchpad}', 'featured')->name('featured');
    Route::delete('/launchpads/{launchpad}', 'destroy')->name('destroy');
});
#launchpads



#holders
Route::name('holders.')->controller(HoldersController::class)->group(function () {
    Route::get('/holders', 'index')->name('index');
    Route::get('/holders/create', 'create')->name('create');
    Route::post('/holders/store', 'store')->name('store');
    Route::get('/holders/{holder}/show', 'show')->name('show');
    Route::get('/holders/{holder}/edit', 'edit')->name('edit');
    Route::put('/holders/{holder}', 'update')->name('update');
    Route::put('/holders/toggle/{holder}', 'toggle')->name('toggle');
    Route::delete('/holders/{holder}', 'destroy')->name('destroy');
});
#holders



#msgs
Route::name('msgs.')->controller(MsgsController::class)->group(function () {
    Route::get('/msgs', 'index')->name('index');
    Route::put('/msgs/status/{msg}/{status}', 'status')->name('status');
    Route::delete('/msgs/{msg}', 'destroy')->name('destroy');
});
#msgs



#trades
Route::name('trades.')->controller(TradesController::class)->group(function () {
    Route::get('/trades', 'index')->name('index');
    Route::get('/trades/create', 'create')->name('create');
    Route::post('/trades/store', 'store')->name('store');
    Route::get('/trades/{trade}/show', 'show')->name('show');
    Route::get('/trades/{trade}/edit', 'edit')->name('edit');
    Route::put('/trades/{trade}', 'update')->name('update');
    Route::put('/trades/toggle/{trade}', 'toggle')->name('toggle');
    Route::delete('/trades/{trade}', 'destroy')->name('destroy');
});
#trades
#settings
Route::name('settings.')->controller(SettingsController::class)->group(function () {
    Route::put('/settings', 'update')->name('update');
});
#settings