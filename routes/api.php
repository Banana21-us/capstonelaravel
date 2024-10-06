<?php

use App\Http\Controllers\SectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ClassesController;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\SubjectController;
use \App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ParentGuardianController;
use App\Http\Controllers\StudentController;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('classes',ClassesController::class);
Route::apiResource('subjects',SubjectController::class);
Route::apiResource('admins',AdminController::class);
Route::apiResource('announcements',AnnouncementController::class);
Route::apiResource('sections',SectionController::class);
Route::apiResource('student', StudentController::class);
Route::apiResource('parentguardian',ParentGuardianController::class);

Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);


// Route::delete('/parentguardian/{parentGuardian}', [ParentGuardianController::class, 'destroy']);
Route::delete('/parentguardian/{email}', [ParentGuardianController::class, 'destroy']);
Route::delete('announcements/{ancmnt_id}', 'AnnouncementController@destroy')->name('announcement.destroy');
Route::delete('admins/{admin_id}', 'AdminController@destroy')->name('admins.destroy');

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);