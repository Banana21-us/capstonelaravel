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
use App\Models\Announcement;

Route::post('/login',[AuthController::class,'login']);

// Route::middleware(['online'])->group(function (){
    
    // dashboard
    Route::get('dashboard',[AuthController::class,'chart']);
    Route::get('/getInquiries', [AuthController::class, 'getInquiries']);



    // classes
    Route::get('classes-list', [AuthController::class, 'getclasslist']);
    Route::get('/class-subjects', [AuthController::class, 'getclasssubjects']);
    Route::get('/class/sections', [AuthController::class, 'getSection']);
    Route::post('/storeClass',[AuthController::class,'storeClass']);
    Route::put('/classes/{class}', [AuthController::class, 'updateClass']);
    Route::delete('/classes/{classes}', [AuthController::class, 'destroyClass']);
  


    // teacher
    Route::get('/getAdminsteacher', [AuthController::class, 'getAdminsteacher']);
    Route::put('/admins/{admin}', [AuthController::class, 'updateAdminsteacher']);
    Route::delete('admins/{admin_id}', [AuthController::class, 'destroyAdminsteacher']);
    Route::post('/register',[AuthController::class,'register']); //can be admin registration as whole 



    // Announcement
    Route::get('announcements',[AuthController::class,'getAnnouncements']);
    Route::get('/announcements/{announcement}', [AuthController::class, 'showtoupdate']);
    Route::post('/postAnnouncements',[AuthController::class,'postAnnouncements']);
    Route::put('/updateAnnouncements/{announcement}', [AuthController::class, 'updateAnnouncements']);
    Route::delete('destroyannouncements/{ancmnt_id}', [AuthController::class, 'destroyAnnouncements']);



    // parent/guardian
    Route::get('student', [AuthController::class,'showStudent']); 
    Route::get('/parentguardian', [AuthController::class, 'getParent']);
    Route::post('/postparentguardian',[AuthController::class,'storeParent']);
    Route::patch('/parentguardian/{email}', [AuthController::class, 'updateParent']);
    Route::delete('/parentguardian/{email}', [AuthController::class, 'destroyParent']);
    Route::delete('parentguardian/{email}/remove', [AuthController::class, 'removeParentStudent']);



    // Section
    Route::get('sections', [AuthController::class,'getindexSection']); 
    Route::post('/postsections',[AuthController::class,'storeSection']);
    Route::put('/sections/{gradeLevel}/{strand}', [AuthController::class, 'updateSection']);
    Route::delete('/section/removesection/{id}', [AuthController::class, 'removeSection']);
    Route::delete('/sections/{gradeLevel}/{strand}', [AuthController::class, 'destroySection']);




    // Subject
    Route::get('subjects', [AuthController::class,'getindexSubject']); 
    Route::post('/postsubjects',[AuthController::class,'storeSubject']);
    Route::put('/subjects/{gradeLevel}/{strand}', [AuthController::class, 'updateSubject']);
    Route::delete('/subjects/{gradeLevel}/{strand}', [AuthController::class, 'destroySubject']);
    Route::delete('/subject/removesubject/{id}', [AuthController::class, 'removesubject']);


   
    // Account 
    Route::put('/update-password', [AuthController::class, 'updatePass']);
    Route::post('/upload-image', [AuthController::class, 'uploadImage']);
    Route::get('assets/adminPic/{filename}', function ($filename) {
        $path = public_path('assets/adminPic/' . $filename);
        
        if (file_exists($path)) {
            return response()->file($path);
        }
    
        abort(404);
    });



    // Message 
    Route::get('/getMessages', [AuthController::class, 'getMessages']);
    Route::get('/getConvo/{sid}', [AuthController::class, 'getConvo']);
    Route::post('/sendMessage', [AuthController::class, 'sendMessage']);
    Route::get('/getrecepeints', [AuthController::class, 'getrecepeints']);
    Route::post('/composemessage', [AuthController::class, 'composenewmessage']);
// });
Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);


// Route::get('/user', function (Request $request) {
//     return $request->user();
// });