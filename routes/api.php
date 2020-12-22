<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WineController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::resource('wines',WineController::class)->middleware('apiProtected');
Route::post('auth/login', [AuthController::class,'login']);
Route::get('auth/me', [AuthController::class,'me'])->middleware('apiProtected');
Route::post('auth/refresh', [AuthController::class,'refresh']);
Route::post('user', [UserController::class,'store']);
