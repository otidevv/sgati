<?php

use App\Http\Controllers\ApiGatewayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Gateway
|--------------------------------------------------------------------------
| Ruta pública que actúa como proxy para los servicios expuestos.
| URL: /api/gw/{slug}  o  /api/gw/{slug}/alguna/ruta
|
| Soporta todos los métodos HTTP. No requiere sesión de usuario.
*/

Route::any('gw/{slug}/{path?}', [ApiGatewayController::class, 'handle'])
    ->where('path', '.*')
    ->name('gateway.handle');
