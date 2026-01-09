<?php

use App\Http\Controllers\LogementController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('logements', LogementController::class);
});