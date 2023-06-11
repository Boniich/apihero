<?php

use App\Http\Controllers\CharacterController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);


Route::apiResource('characters', CharacterController::class);
Route::get('searchCharacter/{name?}/{age?}', [CharacterController::class, 'search']);

// films



Route::get('movies', [FilmController::class, 'index']);
Route::get('searchMovies/{title?}', [FilmController::class, 'search']);
Route::get('movies/{id}', [FilmController::class, 'show']);
Route::post('movies', [FilmController::class, 'store']);
Route::put('movies/{id}', [FilmController::class, 'update']);
Route::patch('movies/{id}', [FilmController::class, 'update']);
Route::delete('movies/{id}', [FilmController::class, 'destroy']);



//devuelve Not found (404) en GETS
Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found.'
    ], 404);
});
