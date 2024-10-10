<?php

use App\Http\Controllers\HotelController;

Route::apiResource('hotels', HotelController::class);
Route::post('hotels/{hotel}/restore', [HotelController::class, 'restore']);
