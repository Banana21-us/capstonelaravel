<?php

use App\Http\Controllers\AuthComtroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ClassesController;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\SubjectController;


Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('classes',ClassesController::class);
Route::apiResource('subjects',SubjectController::class);

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);