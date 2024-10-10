<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SectionController;
use \App\Http\Controllers\ClassesController;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\SubjectController;
use \App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ParentGuardianController;
use App\Http\Controllers\StudentController;

Route::post('/login',[AuthController::class,'login']);

// Route::middleware(['auth'])->group(function (){
// Route::middleware(['admin'])->group(function (){
    Route::apiResource('classes',ClassesController::class);
    Route::apiResource('subjects',SubjectController::class);
    Route::apiResource('admins',AdminController::class);
    Route::apiResource('announcements',AnnouncementController::class);
    Route::apiResource('sections',SectionController::class);
    Route::apiResource('student', StudentController::class);
    Route::apiResource('parentguardian',ParentGuardianController::class);
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);
    Route::delete('/sections/{gradeLevel}', [SectionController::class, 'destroy']);
    Route::put('/sections/{gradeLevel}', [SectionController::class, 'update']);
    Route::delete('/subjects/{gradeLevel}', [SubjectController::class, 'destroy']);
    Route::put('/subjects/{gradeLevel}', [SubjectController::class, 'update']);
    Route::patch('/parentguardian/{email}', [ParentGuardianController::class, 'update']);
    Route::delete('/parentguardian/{email}', [ParentGuardianController::class, 'destroy']);
    Route::delete('announcements/{ancmnt_id}', 'AnnouncementController@destroy')->name('announcement.destroy');
    Route::delete('admins/{admin_id}', 'AdminController@destroy')->name('admins.destroy');
    Route::post('/register',[AuthController::class,'register']);
    

// });
// });
Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);


// Route::get('/user', function (Request $request) {
//     return $request->user();
// });