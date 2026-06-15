<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ReversalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Livewire\Wallet\Home as WalletHome;

Route::get('/', WalletHome::class)->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/wallet', [WalletController::class, 'show']);
    Route::post('/deposits', [DepositController::class, 'store']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
    Route::post(
        '/transactions/{transaction}/reverse',
        [ReversalController::class, 'store']
    );
});

Route::get('/docs', function () {
    return redirect('/docs/api');
});