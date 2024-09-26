<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ClassesController;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\SubjectController;
use \App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('classes',ClassesController::class);
Route::apiResource('subjects',SubjectController::class);
Route::apiResource('admins',AdminController::class);
Route::apiResource('announcements',AnnouncementController::class);



Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);