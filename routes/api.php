<?php

use App\Http\Controllers\Api\Apicontroller;
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

Route::get('/info-bulles', [Apicontroller::class, 'getInfoBulles']);
Route::get('/playlist-du-jour', [ApiController::class, 'getPlaylistDuJour']);
Route::get('/videos', [ApiController::class, 'getVideos']);
Route::get('/search/videos', [ApiController::class, 'getVideossearch']);

