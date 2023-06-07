<?php

use App\Http\Controllers\CharacterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('characters', CharacterController::class);
Route::get('searchCharacter/{name?}/{age?}', [CharacterController::class, 'search']);

//devuelve Not found (404) en GETS
Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found.'
    ], 404);
});
