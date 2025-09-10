<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SearchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CartController;

Route::get('/', [SearchController::class, 'index'])->name('search.index');

Route::get('/company/{country}/{id}', [CompanyController::class, 'show'])->name('company.show');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');
