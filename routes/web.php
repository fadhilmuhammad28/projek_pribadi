<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('parking')->middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\ParkingController::class, 'dashboard'])->name('parking.dashboard');
    Route::get('/entry', [App\Http\Controllers\ParkingController::class, 'entry'])->name('parking.entry');
    Route::post('/entry', [App\Http\Controllers\ParkingController::class, 'storeEntry'])->name('parking.entry.store');
    Route::post('/entry/ajax', [App\Http\Controllers\ParkingController::class, 'storeEntryAjax'])->name('parking.entry.store.ajax');
    Route::get('/entry/{ticket_code}/print', [App\Http\Controllers\ParkingController::class, 'printEntryReceipt'])->name('parking.entry.receipt');
    Route::get('/exit', [App\Http\Controllers\ParkingController::class, 'exitForm'])->name('parking.exit');
    Route::post('/exit', [App\Http\Controllers\ParkingController::class, 'processExit'])->name('parking.exit.process');
    Route::post('/exit/scan', [App\Http\Controllers\ParkingController::class, 'scanTicket'])->name('parking.exit.scan');
    Route::get('/reports', [App\Http\Controllers\ParkingController::class, 'reports'])->name('parking.reports');
    Route::get('/rates', [App\Http\Controllers\ParkingController::class, 'ratesIndex'])->name('parking.rates')->middleware('admin');
    Route::post('/rates', [App\Http\Controllers\ParkingController::class, 'ratesUpdate'])->name('parking.rates.update')->middleware('admin');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/tickets/{ticket}/edit', [\App\Http\Controllers\AdminController::class, 'ticketsEdit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [\App\Http\Controllers\AdminController::class, 'ticketsUpdate'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [\App\Http\Controllers\AdminController::class, 'ticketsDestroy'])->name('tickets.destroy');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\AdminController::class, 'usersEdit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\AdminController::class, 'usersUpdate'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'usersDestroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
