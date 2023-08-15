<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\App\PostController as PostControllerForApp;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\App\SettingsController;
use App\Http\Middleware\AppLocale;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('v1')->group(function(){

    Route::get('settings', [SettingsController::class, 'index']);
    Route::middleware([
        AppLocale::class
    ])->group(function () {
        Route::get('translates', [SettingsController::class, 'translates']);
        Route::get('posts', [PostControllerForApp::class, 'index']);
    });

    Route::prefix('admin')->group(function() {
        Route::post('login', [AdminController::class, 'login']);
        Route::get('logout', [AdminController::class, 'logout'])->middleware('auth:admin');

        Route::get('posts', [PostController::class, 'index'])->middleware('auth:admin');
        Route::get('posts/{id}', [PostController::class, 'show'])->middleware('auth:admin');
        Route::post('posts', [PostController::class, 'store'])->middleware('auth:admin');
        Route::put('posts/{id}', [PostController::class, 'update'])->middleware('auth:admin');
        Route::delete('posts/{id}', [PostController::class, 'destroy'])->middleware('auth:admin');
    });
});
