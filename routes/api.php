<?php

use App\Http\Controllers\LineBotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Line Webhook
Route::post('/webhook', [LineBotController::class, 'webhook']);

Route::get('data-covid', [LineBotController::class, 'getDataCovid']);
