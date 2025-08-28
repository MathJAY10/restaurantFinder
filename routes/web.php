<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;

Route::get('/', [RestaurantController::class, 'index']); 
Route::get('/search', [RestaurantController::class, 'search'])->name('search'); // Changed from POST to GET