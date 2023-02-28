<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\UserController;

Route::post('login', [UserController::class, 'login']);

Route::group(['middleware' => ['jwt.verify:admin,kasir,manajer']], function() {
    //untuk semua endpoint yang dapat diakses oleh ketiga role
    
    //ini contoh aja
    Route::get('user', [UserController::class, 'getUser']);
    Route::get('login/check', [UserController::class, 'loginCheck']);
    Route::post('logout', [UserController::class, 'logout']);
});

Route::group(['middleware' => ['jwt.verify:admin']], function() {
    //untuk semua endpoint yang dapat diakses oleh admin
    
    //ini contoh aja
    Route::post('user', [UserController::class, 'store']);
});

Route::group(['middleware' => ['jwt.verify:kasir']], function() {
    //untuk semua endpoint yang dapat diakses oleh kasir

});

Route::group(['middleware' => ['jwt.verify:manajer']], function() {
    //untuk semua endpoint yang dapat diakses oleh kasir

});


