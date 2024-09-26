<?php

use App\Http\Controllers\HotelController;

Route::group(['prefix' => 'hotels'], function () {
    Route::get('/list', [HotelController::class, 'list']);
    Route::post('/save', [HotelController::class, 'save']);
    Route::post('/delete/{id}', [HotelController::class, 'delete']);
    Route::get('/show/{id}', [HotelController::class, 'show']);
});
