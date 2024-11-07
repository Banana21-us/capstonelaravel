<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SectionController;
use \App\Http\Controllers\ClassesController;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\SubjectController;
use \App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ParentGuardianController;
use App\Http\Controllers\StudentController;

Route::post('/login',[AuthController::class,'login']);

// Route::middleware(['online'])->group(function (){
    Route::apiResource('classes',ClassesController::class);
    Route::apiResource('dashboard',EnrollmentController::class);


    Route::apiResource('classes',ClassesController::class);
    Route::get('classes-list', [ClassesController::class, 'getclasslist']);
    Route::get('/class/sections', [ClassesController::class, 'getSection']);
    Route::get('/class-subjects', [ClassesController::class, 'getclasssubjects']);
    Route::delete('/classes/{classes}', [ClassesController::class, 'destroy']);
    Route::patch('/classes/{class}', [ClassesController::class, 'update']);


    Route::apiResource('admins',AdminController::class);
    Route::apiResource('announcements',AnnouncementController::class);
    Route::apiResource('student', StudentController::class);
    Route::apiResource('parentguardian',ParentGuardianController::class);


    Route::apiResource('sections',SectionController::class);
    Route::delete('/sections/{gradeLevel}/{strand}', [SectionController::class, 'destroy']);
    Route::put('/sections/{gradeLevel}/{strand}', [SectionController::class, 'update']);
    Route::delete('/section/removesection/{id}', [SectionController::class, 'removeSection']);


    Route::apiResource('subjects',SubjectController::class);
    Route::put('/subjects/{gradeLevel}/{strand}', [SubjectController::class, 'update']);
    Route::delete('/subjects/{gradeLevel}/{strand}', [SubjectController::class, 'destroy']);
    Route::delete('/subject/removesubject/{id}', [SubjectController::class, 'removesubject']);


    Route::patch('/parentguardian/{email}', [ParentGuardianController::class, 'update']);
    Route::delete('/parentguardian/{email}', [ParentGuardianController::class, 'destroy']);
    Route::delete('parentguardian/{email}/remove', [ParentGuardianController::class, 'remove']);
    Route::get('/parentguardianfilter', [ParentGuardianController::class, 'getfilteredParents']);

    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);
    Route::delete('announcements/{ancmnt_id}', 'AnnouncementController@destroy')->name('announcement.destroy');
    
    Route::delete('admins/{admin_id}', 'AdminController@destroy')->name('admins.destroy');
    Route::put('/admins/{admin}', [AdminController::class, 'update']);
    
    Route::post('/register',[AuthController::class,'register']);
    Route::put('/update-password', [AuthController::class, 'updatePass']);
    Route::post('/upload-image', [AuthController::class, 'uploadImage']);
    Route::get('assets/adminPic/{filename}', function ($filename) {
        $path = public_path('assets/adminPic/' . $filename);
        
        if (file_exists($path)) {
            return response()->file($path);
        }
    
        abort(404);
    });
// });
Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);


// Route::get('/user', function (Request $request) {
//     return $request->user();
// });