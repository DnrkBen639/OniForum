<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\loginController;
use  App\Http\Controllers\UsuarioController;
use  App\Http\Controllers\PublicacionController;

Route::post('register', [loginController::class, 'register']);

Route::post('login', [loginController::class, 'login']);

Route::group( ['middleware' => ["auth:sanctum"]], function () {
    
    Route::get('user-profile', [UsuarioController::class, 'userProfile']);

    Route::get('logout', [UsuarioController::class, 'logout']);

    Route::post('updateImage', [UsuarioController::class, 'PhotoUpdate']);

    Route::post('Publicar', [PublicacionController::class, 'Publicar']);

    Route::post('readRequest', [PublicacionController::class, 'readRequest']);

    Route::post('openPost', [PublicacionController::class, 'openPost']);

});
