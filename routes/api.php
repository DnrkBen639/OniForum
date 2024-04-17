<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\loginController;
use  App\Http\Controllers\UsuarioController;
use  App\Http\Controllers\PublicacionController;
use App\Http\COntrollers\NotificacionController;
use App\Http\COntrollers\AmistadController;

Route::post('register', [loginController::class, 'register']);

Route::post('login', [loginController::class, 'login']);

Route::group( ['middleware' => ["auth:sanctum"]], function () {
    
    Route::get('user-profile', [UsuarioController::class, 'userProfile']);

    Route::get('logout', [UsuarioController::class, 'logout']);

    Route::post('updateImage', [UsuarioController::class, 'PhotoUpdate']);

    Route::post('Publicar', [PublicacionController::class, 'Publicar']);

    Route::post('readRequest', [PublicacionController::class, 'readRequest']);

    Route::post('openPost', [PublicacionController::class, 'openPost']);

    Route::get('openNotif', [NotificacionController::class, 'openNotif']);

    Route::post('SearchUser', [AmistadController::class, 'SearchUser']);

    Route::post('SendFriendReq', [AmistadController::class, 'SendFriendReq']);

    Route::post('AcceptFriend', [AmistadController::class, 'AcceptFriend']);

    Route::post('DenyFriend', [AmistadController::class, 'DenyFriend']);
    
    Route::post('LikePost', [PublicacionController::class, 'LikePost']);

    Route::post('SharePost', [PublicacionController::class, 'SharePost']);

    Route::post('ComentPost', [PublicacionController::class, 'ComentPost']);

    Route::get('Feed', [loginController::class, 'Feed']);
});
