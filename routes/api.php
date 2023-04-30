<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;

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

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('/check', [AuthController::class, 'check'])->name('check');
});

Route::group(['prefix' => 'user','middleware' => ['apiJWT']], function ($router) {
    Route::get('/me', [UserController::class, 'me']);
});

Route::group(['prefix' => 'books','middleware' => ['apiJWT']], function ($router) {
    Route::get('/', [BookController::class, 'index']);
    Route::get('/{book}', [BookController::class, 'show']);
    Route::post('/', [BookController::class, 'store']);
    Route::put('/{book}', [BookController::class, 'update']);
    Route::delete('/{book}', [BookController::class, 'destroy']);
});