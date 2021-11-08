<?php

use App\Http\Controllers\LineBotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/get-message', function (Request $request) {

    logger("message request : ", $request->all());
});

Route::post('/get-message', [LineBotController::class, 'getMessage']);

Route::post('/webhook', [LineBotController::class, 'webhook']);
