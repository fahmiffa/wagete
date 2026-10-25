<?php

use App\Http\Controllers\Api\WhatsappGatewayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua route di bawah ini memiliki prefix /api dan dilindungi oleh
| middleware VerifyApiKey (header X-API-Key wajib disertakan).
|
*/

Route::middleware('api.key')->prefix('whatsapp')->group(function () {
    Route::get('status', [WhatsappGatewayController::class, 'status']);
    Route::get('login',  [WhatsappGatewayController::class, 'login']);
    Route::post('send',  [WhatsappGatewayController::class, 'send']);
});
