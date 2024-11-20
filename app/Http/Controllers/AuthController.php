<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Subject;
use App\Models\Announcement;
use App\Models\ParentGuardian;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Message;
use App\Models\Section;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    // basics
    public function register(Request $request)
    {
        Log::info('Starting registration process.');
    
        $formField = $request->validate([
            "fname" => "required|max:255",
            "lname" => "required|max:255",
            "mname" => "required|max:255",
            "role" => "required|max:255",
            "address" => "required|max:255",
            "email" => "required|email|unique:admins",
            "password" => [
                "required",
                "string",
                "min:8",
                "max:255",
                "regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/"
            ]
        ]);
        
    
        Log::info('Validation successful.', ['formField' => $formField]);
    
        $formField['password'] = bcrypt($formField['password']);
        Log::info('Password encrypted.');
    
        try {
            $admin = Admin::create($formField);
            Log::info('Admin created successfully.', ['admin_id' => $admin->id]);
        } catch (\Exception $e) {
            Log::error('Error creating admin.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Registration failed'], 500);
        }
    
        return response()->json(['message' => 'Registration successful'], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            "email"=>"required|email|exists:admins",
            "password"=>"required"
        ]);
        $admin = Admin::where('email',$request->email)->first();
        if(!$admin|| !Hash::check($request->password,$admin->password)){
            return [
                "message"=>"The provider credentials are incorrect"
            ];
        }
        $token = $admin->createToken($admin->fname);
        // $token = $admin->createToken($admin->fname)->plainTextToken; 

        return [
            'admin' => $admin,
            'token' => $token->plainTextToken,
            'id'=> $admin->admin_id
        ];


    }
    // public function login(Request $request){
    //     $request->validate([
    //         'email' => 'required|email|exists:admins',
    //         'password' => 'required'
    //     ]);

    //     $admin = Admin::where('email', $request->email)->first();
    //     if(!$admin || !Hash::check($request->password,$admin->password)){
    //         return [
    //             'message' => 'The provided credentials are incorrect'
    //         ];
    //     }

    //     $token = $admin->createToken($admin->fname);

    //     return [
    //         'admin' => $admin,
    //         'token' => $token->plainTextToken
    //     ];
    // }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message'=>'You are logged out'
        ];
        // return 'logout';
    }



    // dashboard
    public function getInquiries(){
        $latestMessages = DB::table('messages')
        ->select('message_sender', DB::raw('MAX(created_at) as max_created_at'))
        ->groupBy('message_sender');
    
    $data = DB::table('messages')
        ->leftJoin('students', 'messages.message_sender', '=', 'students.LRN')
        ->leftJoin('admins', 'messages.message_reciever', '=', 'admins.admin_id')
        ->joinSub($latestMessages, 'latest_messages', function ($join) {
            $join->on('messages.message_sender', '=', 'latest_messages.message_sender')
                 ->on('messages.created_at', '=', 'latest_messages.max_created_at');
        })
        ->whereNotIn('messages.message_sender', function ($query) {
            $query->select('admin_id')->from('admins');
        })
        ->select(
            'messages.*', 
            DB::raw('CONCAT(students.fname, " ",LEFT(students.mname, 1), ". ", students.lname) AS student_name'),
            DB::raw('CONCAT(admins.fname, " ",LEFT(admins.mname, 1), ". ", admins.lname) AS admin_name')
        )
        ->orderBy('messages.created_at', 'desc') // Ensure the latest messages are on top
        ->limit(5) // Limit the results to the top 5
        ->get();
    
    return $data;
    
    }
    public function chart()
    {
        // Count the number of enrollments grouped by grade_level and strand
        $enrollmentCounts = DB::table('enrollments')
            ->select('grade_level', 'strand', DB::raw('count(*) as total'))
            ->groupBy('grade_level', 'strand')
            ->orderBy('grade_level')
            ->get();

        // Calculate total counts
        $totalEnrollments = DB::table('enrollments')->count();
        $juniorHighTotal = DB::table('enrollments')->whereIn('grade_level', ['7', '8', '9', '10'])->count();
        $seniorHighTotal = DB::table('enrollments')->whereIn('grade_level', ['11', '12'])->count();

        return response()->json([
            'enrollmentCounts' => $enrollmentCounts,
            'totalEnrollments' => $totalEnrollments,
            'juniorHighTotal' => $juniorHighTotal,
            'seniorHighTotal' => $seniorHighTotal,
        ]);
    }



    // classes
    public function getclasslist() {
        $classes = DB::table('classes as c')
            ->join('sections as s', 'c.section_id', '=', 's.section_id')
            ->join('admins as a', 'c.admin_id', '=', 'a.admin_id')
            ->join('subjects as sub', 'c.subject_id', '=', 'sub.subject_id')
            ->select(
                'c.class_id',  // Include class_id
                'c.room',
                's.grade_level as level',
                's.strand',
                's.section_name',
                'a.fname',
                'a.lname',
                'sub.subject_name',
                'c.time',
                'c.schedule',
                'c.subject_id',  
                'c.section_id',
                'c.admin_id'
            )
            ->orderBy('s.grade_level')
            ->orderByRaw("FIELD(s.strand,'-', 'STEM', 'ABM', 'HUMMS') DESC")
            ->get();
    
        return response()->json($classes);
    }
    public function getclasssubjects() {
        $subjects = Subject::all();
        $structuredSubjects = [];
        foreach ($subjects as $subject) {
            $key = "{$subject->grade_level}-{$subject->strand}";

            if (!isset($structuredSubjects[$key])) {
                $structuredSubjects[$key] = [
                    'level' => $subject->grade_level,
                    'strand' => $subject->strand,
                    'subjects' => []
                ];
            }

            $structuredSubjects[$key]['subjects'][] = [
                'subject_id' => $subject->subject_id,
                'subject_name' => $subject->subject_name,
            ];
        }

        $structuredSubjects = array_values($structuredSubjects);
        return response()->json($structuredSubjects);
    }
    public function getSection() {
        $levelsAndStrands = DB::table('sections')
            ->select('grade_level', 'strand')
            ->distinct()
            ->orderBy('grade_level')
            ->get();

        $result = [];

        foreach ($levelsAndStrands as $entry) {
            $sections = DB::table('sections')
                ->select('section_id', 'section_name', 'grade_level', 'strand')
                ->where('grade_level', $entry->grade_level)
                ->where('strand', $entry->strand)
                ->get();

            $result[] = [
                'level' => $entry->grade_level,
                'strand' => $entry->strand,
                'sections' => $sections
            ];
        }

        return response()->json($result);
    }
    public function storeClass(Request $request)
     {
         DB::beginTransaction();
         try {
             // Validation code...
             $validatedData = $request->validate([
                 'section_id' => 'required|exists:sections,section_id',
                 'room' => 'required|integer|max:999',
                 'forms' => 'required|array',
                 'forms.*.teacher' => 'required|exists:admins,admin_id',
                 'forms.*.subject_id' => 'required|exists:subjects,subject_id',
                 'forms.*.time' => 'required|string|max:255',
                 'forms.*.selectedDays' => 'required|array',
                 'forms.*.selectedDays.*' => 'required|string|max:255',
             ]);
     
             // Log the validated data to ensure all fields are present
             Log::info('Creating classes with validated data:', [
                 'section_id' => $validatedData['section_id'],
                 'room' => $validatedData['room'],
                 'forms_count' => count($validatedData['forms']),
             ]);
     
             foreach ($validatedData['forms'] as $form) {
                 // Check each form data before insertion
                 Log::info('Inserting class for teacher:', [
                     'admin_id' => $form['teacher'],
                     'section_id' => $validatedData['section_id'],
                     'room' => $validatedData['room'],
                     'time' => $form['time'],
                     'schedule' => implode(',', $form['selectedDays']),
                     'subject_id' => $form['subject_id'],
                 ]);
     
                 Classes::create([
                     'admin_id' => $form['teacher'],
                     'section_id' => $validatedData['section_id'],
                     'room' => $validatedData['room'],
                     'time' => $form['time'],
                     'schedule' => implode(',', $form['selectedDays']),
                     'subject_id' => $form['subject_id'], // Ensure this is set
                 ]);
             }
     
             DB::commit(); // Commit the transaction
             return response()->json(['message' => 'Classes successfully created'], 201);
         } catch (ValidationException $e) {
            DB::rollBack(); // Rollback on validation errors
            Log::error('Validation failed:', $e->errors());
            Log::info('Received request data:', $request->all());
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }catch (\Exception $e) {
             DB::rollBack(); // Rollback on other exceptions
             Log::error('An error occurred while creating the class:', ['error' => $e->getMessage()]);
             return response()->json(['message' => 'Failed to create class.', 'error' => $e->getMessage()], 500);
         }
    }
    public function updateClass(Request $request, $id){  
            $classes = Classes::find($id);
            DB::beginTransaction();
        
            try {
                // Validate the request data
                $validatedData = $request->validate([
                    'section_id' => 'required|exists:sections,section_id',
                    'room' => 'required|integer|max:999',
                    'forms' => 'required|array',
                    'forms.*.teacher' => 'required|exists:admins,admin_id',
                    'forms.*.subject_id' => 'required|exists:subjects,subject_id',
                    'forms.*.time' => 'required|string|max:255',
                    'forms.*.selectedDays' => 'required|array',
                    'forms.*.selectedDays.*' => 'required|string|max:255',
                ]);
        
                // Log the validated data
                Log::info('Updating class with validated data:', [
                    'class_id' => $classes->class_id,
                    'section_id' => $validatedData['section_id'],
                    'room' => $validatedData['room'],
                    'forms_count' => count($validatedData['forms']),
                ]);
        
                // Update the existing class details
                foreach ($validatedData['forms'] as $form) {
                    $classes->update([
                        'admin_id' => $form['teacher'],
                        'section_id' => $validatedData['section_id'],
                        'room' => $validatedData['room'],
                        'time' => $form['time'],
                        'schedule' => implode(',', $form['selectedDays']),
                        'subject_id' => $form['subject_id'],
                    ]);
                }
        
                DB::commit(); // Commit the transaction
                return response()->json(['message' => 'Classes successfully updated'], 200);
            } catch (ValidationException $e) {
                DB::rollBack(); // Rollback on validation errors
                Log::error('Validation failed:', $e->errors());
                return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
            }
    }
    public function destroyClass($class_id){
        Log::info('Class instance received for deletion:', ['class_id' => $class_id]);
    
        try {
            // Find the class by ID
            $class = Classes::findOrFail($class_id);  // This will throw 404 if not found
    
            Log::info('Attempting to delete class:', [
                'class_id' => $class->class_id,
                'admin_id' => $class->admin_id,
                'section_id' => $class->section_id,
                'room' => $class->room,
                'time' => $class->time,
                'schedule' => $class->schedule,
            ]);
    
            $class->delete();  
            
            return response()->json(['message' => 'Class deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting class:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete class.', 'error' => $e->getMessage()], 500);
        }
    }



    // Section 
    public function getindexSection()
    {
        $sections = Section::all();
        $organizedSections = [];
        
        foreach ($sections as $section) {
            $key = $section->grade_level . '-' . $section->strand;
            
            // Initialize the array for a new key if it doesn't exist
            if (!isset($organizedSections[$key])) {
                $organizedSections[$key] = [
                    'level' => $section->grade_level,
                    'strand' => $section->strand,
                    'sections' => []  // Change this to sections
                ];
            }
    
            $sectionEntry = [
                'name' => ucfirst($section->section_name),
                'id' => $section->section_id
            ];
            
            // Check for duplicates by name
            $sectionNames = array_column($organizedSections[$key]['sections'], 'name');
            if (!in_array($sectionEntry['name'], $sectionNames)) {
                $organizedSections[$key]['sections'][] = $sectionEntry;
            }
        }
    
        // Sort the $organizedSections array by grade level (ascending order)
        uasort($organizedSections, function($a, $b) {
            return $a['level'] <=> $b['level']; 
        });
        
        return array_values($organizedSections);
    }
    public function storeSection(Request $request)
        {
            $validatedData = $request->validate([
                'section_name' => 'required|array',
                'section_name.*' => 'required|string|max:255',
                'grade_level' => 'required|integer|max:12',
                'strand' => 'nullable|string|max:255', // Validate strand
            ]);
            
            $sections = [];
            
            foreach ($validatedData['section_name'] as $name) {
                $sections[] = Section::create([
                    'section_name' => $name,
                    'grade_level' => $validatedData['grade_level'],
                    'strand' => $validatedData['strand'], // Include strand when creating sections
                ]);
            }
            
    
            return response()->json($sections, 201);
    }
    public function updateSection(Request $request, $gradeLevel, $strand)
        {
            $validatedData = $request->validate([
                'section_name' => 'required|array',
                'section_name.*' => 'required|string|max:255',
                'grade_level' => 'required|integer|max:12',
                'strand' => 'required|string|max:255', 
            ]);

            $existingSections = Section::where('grade_level', $gradeLevel)
                                    ->where('strand', $strand)
                                    ->get();

            foreach ($validatedData['section_name'] as $index => $name) {
                if (isset($existingSections[$index])) {
                    $existingSections[$index]->update([
                        'section_name' => $name,
                    ]);
                } else {
                    Section::create([
                        'section_name' => $name,
                        'grade_level' => $validatedData['grade_level'],
                        'strand' => $validatedData['strand'],
                    ]);
                }
            }
            return response()->json(['message' => 'Sections updated successfully.', 'sections' => $validatedData['section_name']], 200);
    }
    public function removeSection(Request $request, $id)
        {
            $section = Section::find($id);
        
            if (!$section) {
                return response()->json(['message' => 'section not found.'], 404);
            }
            $section->delete();
            return response()->json(['message' => 'section deleted successfully.'], 200);
    }
    public function destroySection($gradeLevel, $strand)
        {
            $deletedRows = Section::where('grade_level', $gradeLevel)
                                  ->where('strand', $strand)
                                  ->delete();
        
            if ($deletedRows) {
                return response()->json(['message' => 'Sections deleted successfully.'], 200);
            } else {
                return response()->json(['message' => 'No sections found for this grade level and strand.'], 404);
            }
    }


    // Subject
    public function getindexSubject()
    {
        $subjects = Subject::all();
        $organizedSubjects = [];
        
        foreach ($subjects as $subject) {
            $key = $subject->grade_level . '-' . $subject->strand;
            
            // Initialize the array for a new key if it doesn't exist
            if (!isset($organizedSubjects[$key])) {
                $organizedSubjects[$key] = [
                    'level' => $subject->grade_level,
                    'strand' => $subject->strand,
                    'subject_name' => []  // Change this to subject_name
                ];
            }
    
            $subjectEntry = [
                'name' => ucfirst($subject->subject_name),
                'id' => $subject->subject_id  // Assuming the subject model has an id property
            ];
            
            // Check for duplicates by name
            $subjectNames = array_column($organizedSubjects[$key]['subject_name'], 'name');
            if (!in_array($subjectEntry['name'], $subjectNames)) {
                $organizedSubjects[$key]['subject_name'][] = $subjectEntry;
            }
        }
    
        // Sort the $organizedSubjects array by grade level (ascending order)
        uasort($organizedSubjects, function($a, $b) {
            return $a['level'] <=> $b['level']; // Compare grade levels in ascending order
        });
        
        return array_values($organizedSubjects);
    }
    public function storeSubject(Request $request)
    {
        $validatedData = $request->validate([
            'subject_name' => 'required|array',
            'subject_name.*' => 'required|string|max:255',
            'grade_level' => 'required|integer|max:12',
            'strand' => 'required|string|max:255',
        ]);

        $subjects = [];
        
        foreach ($validatedData['subject_name'] as $name) {
            $subjects[] = Subject::create([
                'subject_name' => $name,
                'grade_level' => $validatedData['grade_level'],
                'strand' => $validatedData['strand'],
            ]);
        }

        return response()->json($subjects, 201);
    }
    public function updateSubject(Request $request, $gradeLevel, $strand)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'subject_name' => 'required|array',
            'subject_name.*' => 'required|string|max:255',
            'grade_level' => 'required|integer|max:12',
            'strand' => 'required|string|max:255',
        ]);

        // Log the validated data
        Log::info('Updating subjects for Grade Level: ' . $gradeLevel . ', Strand: ' . $strand, $validatedData);

        // Retrieve existing subjects
        $existingSubjects = Subject::where('grade_level', $gradeLevel)
                                ->where('strand', $strand)
                                ->get();

        // Log existing subjects
        Log::info('Existing subjects retrieved:', $existingSubjects->toArray());

        // Update or create subjects based on incoming data
        foreach ($validatedData['subject_name'] as $index => $name) {
            if (isset($existingSubjects[$index])) {
                // Update existing subject
                $existingSubjects[$index]->update([
                    'subject_name' => $name,
                    // Do not update grade_level and strand if they shouldn't change
                ]);
                Log::info('Updated subject:', ['id' => $existingSubjects[$index]->subject_id, 'name' => $name]);
            } else {
                // Create new subject if it doesn't exist
                $newSubject = Subject::create([
                    'subject_name' => $name,
                    'grade_level' => $validatedData['grade_level'],
                    'strand' => $validatedData['strand'],
                ]);
                Log::info('Created new subject:', ['id' => $newSubject->subject_id, 'name' => $name]);
            }
        }

        // Delete subjects that are no longer present in the incoming data
        // foreach ($existingSubjects as $existingSubject) {
        //     if (!in_array($existingSubject->subject_name, $validatedData['subject_name'])) {
        //         $existingSubject->delete(); 
        //         Log::info('Deleted subject:', ['id' => $existingSubject->subject_id, 'name' => $existingSubject->subject_name]);
        //     }
        // }

        return response()->json(['message' => 'Subjects updated successfully.', 'subjects' => $validatedData], 200);
    }
    public function removesubject(Request $request, $id)
{
    $subject = Subject::find($id);

    if (!$subject) {
        return response()->json(['message' => 'Subject not found.'], 404);
    }
    $subject->delete();
    return response()->json(['message' => 'Subject deleted successfully.'], 200);
    }
    public function destroySubject($gradeLevel, $strand)
{
    $deletedRows = Subject::where('grade_level', $gradeLevel)
                          ->where('strand', $strand)
                          ->delete();

    if ($deletedRows) {
        return response()->json(['message' => 'Subjects deleted successfully.'], 200);
    } else {
        return response()->json(['message' => 'No subjects found for this grade level and strand.'], 404);
    }
    }



    // teacher/admins
    public function getAdminsteacher()
    {
        $admins = Admin::all();
        $sortedAdmins = $admins->sortBy(function ($admin) {
            return strtolower($admin->lname);
        })->values();
        return response()->json($sortedAdmins);
    }
    public function updateAdminsteacher(Request $request, Admin $admin)
    {
        // Log the incoming request data
        Log::info('Updating admin record', [
            'admin_id' => $admin->id,
            'request_data' => $request->all(),
        ]);
    
        // Validate the incoming request data
        $validatedData = $request->validate([
            'fname' => 'sometimes|required|string|max:255',
            'mname' => 'sometimes|required|string|max:12',
            'lname' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
        ]);
    
        // Log the validated data
        Log::info('Validated data for admin update', [
            'admin_id' => $admin->id,
            'validated_data' => $validatedData,
        ]);
    
        // Update the admin instance with validated data
        $admin->update($validatedData);
    
        // Log successful update
        Log::info('Admin record updated successfully', [
            'admin_id' => $admin->id,
            'updated_data' => $admin,
        ]);
    
        // Return a JSON response with the updated admin data
        return response()->json($admin, 200);
    }
    public function destroyAdminsteacher($admin)
    {
        try {
            $admin = Admin::find($admin);

            if ($admin) {
                $admin->delete();
                return response()->json(['message' => 'Deleted successfully!'], 200);
            }

            return response()->json(['message' => 'teacher not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting teacher: ' . $e->getMessage()], 500);
        }
    }



    // account
    public function updatePass(Request $request)
{
    // Validate incoming request
    $request->validate([
        'admin_id' => 'required|integer|exists:admins,admin_id',
        'oldPassword' => 'nullable|string', // Make oldPassword optional
        'newPassword' => 'nullable|string|min:8|confirmed', // Allow newPassword to be optional
        'fname' => 'required|string|max:255',
        'mname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:admins,email,' . $request->admin_id . ',admin_id', // Check uniqueness for email
        'address' => 'required|string|max:255',
    ]);

    // Retrieve user
    $user = Admin::find($request->admin_id);

    // If old password is provided, check it
    if ($request->oldPassword && !Hash::check($request->oldPassword, $user->password)) {
        return response()->json(['message' => 'Wrong password'], 401);
    }

    // Update user details
    if ($request->newPassword) {
        $user->password = Hash::make($request->newPassword); // Update password if provided
    }
    
    $user->fname = $request->fname;
    $user->mname = $request->mname;
    $user->lname = $request->lname;
    $user->email = $request->email;
    $user->address = $request->address;

    $user->save(); // Save all changes

    return response()->json(['message' => 'User details updated successfully']);
    }
    public function uploadImage(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'admin_id' => 'required|exists:admins,admin_id'
    ]);

    try {
        $admin = Admin::findOrFail($request->input('admin_id'));
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('assets/adminPic');

        // Ensure the directory exists
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Delete the old image if exists
        if ($admin->admin_pic && file_exists($path = $destinationPath . '/' . $admin->admin_pic)) {
            unlink($path);
        }

        // Move the new image and update the admin profile
        $image->move($destinationPath, $imageName);
        $admin->update(['admin_pic' => $imageName]);

        return response()->json([
            'message' => 'Image uploaded successfully.',
            'image_url' => url('assets/adminPic/' . $imageName)
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Image upload failed.'], 500);
    }
    }
    


    // message
    public function getMessages(Request $request){
        $uid = $request->input('uid');

        $latestMessages = DB::table('messages')
            ->select('message_sender', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('message_sender');
    
        $msg = DB::table('messages')
            ->leftJoin('students', function ($join) {
                $join->on('messages.message_sender', '=', 'students.LRN');
            })
            ->leftJoin('parent_guardians', function ($join) {
                $join->on('messages.message_sender', '=', 'parent_guardians.guardian_id');
            })
            ->joinSub($latestMessages, 'latest_messages', function ($join) {
                $join->on('messages.message_sender', '=', 'latest_messages.message_sender')
                    ->on('messages.created_at', '=', 'latest_messages.max_created_at');
            })
            ->whereNotIn('messages.message_sender', function ($query) {
                $query->select('admin_id')->from('admins');
            })
            ->where('messages.message_reciever', '=', $uid)
            ->select('messages.*', 
                DB::raw('CASE 
                    WHEN messages.message_sender IN (SELECT LRN FROM students) THEN CONCAT(students.fname, " ",LEFT(students.mname, 1), ". ", students.lname)
                    WHEN messages.message_sender IN (SELECT guardian_id FROM parent_guardians) THEN CONCAT(parent_guardians.fname, " ",LEFT(parent_guardians.mname, 1), ". ", parent_guardians.lname)
                END as sender_name'))
            ->orderBy('messages.created_at', 'desc')
            ->get();
        
        return $msg;
    }
    public function getConvo(Request $request, $sid){
        $uid = $request->input('uid');

        $convo = DB::table('messages')
            ->leftJoin('students', function ($join) {
                $join->on('messages.message_sender', '=', 'students.LRN');
            })
            ->leftJoin('admins', function ($join) {
                $join->on('messages.message_sender', '=', 'admins.admin_id');
            })
            ->leftJoin('parent_guardians', function ($join) {
                $join->on('messages.message_sender', '=', 'parent_guardians.guardian_id');
            })
            ->where(function ($query) use ($uid) {
                $query->where('messages.message_sender', $uid) // Sent messages
                      ->orWhere('messages.message_reciever', $uid); // Received replies
            })     
            ->where(function ($query) use ($sid) {
                $query->where('messages.message_sender', $sid) // Sent messages
                      ->orWhere('messages.message_reciever', $sid); // Received replies
            })        
            ->select('messages.*', 
                DB::raw('CASE 
                    WHEN messages.message_sender IN (SELECT LRN FROM students) THEN CONCAT(students.fname, " ",LEFT(students.mname, 1), ". ", students.lname)
                    WHEN messages.message_sender IN (SELECT guardian_id FROM parent_guardians) THEN CONCAT(parent_guardians.fname, " ",LEFT(parent_guardians.mname, 1), ". ", parent_guardians.lname)
                END as sender_name'),
                DB::raw('CASE 
                    WHEN messages.message_sender IN (SELECT admin_id FROM admins) THEN CONCAT(admins.fname, " ",LEFT(admins.mname, 1), ". ", admins.lname)
                END as me'))
            ->get();
            if ($convo->isEmpty()) {
                return response()->json(['message' => 'No messages found'], 404);
            }

        return $convo;
    }
    public function sendMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'message_sender' => 'required',
            'message_reciever' => 'required',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $message = Message::create([
            'message_sender' => $request->input('message_sender'), // Ensure the key matches your database column
            'message_reciever' => $request->input('message_reciever'), // Ensure the key matches your database column
            'message' => $request->input('message'), // Ensure the key matches your database column
            'message_date' => now(),
        ]);

        return response()->json($message, 201);
    }
    public function getrecepeints(Request $request)
    {
     $students = DB::table('students')
     ->select(DB::raw('LRN AS receiver_id, CONCAT(fname, " ", lname) AS receiver_name'));
    $guardians = DB::table('parent_guardians')
        ->select(DB::raw('guardian_id AS receiver_id, CONCAT(fname, " ", lname) AS receiver_name'));
    $admins = DB::table('admins')
        ->select(DB::raw('admin_id AS receiver_id, CONCAT(fname, " ", lname) AS receiver_name'));
    $recipients = $students->unionAll($guardians)->unionAll($admins)->get();
    return response()->json($recipients);
    }
    public function composenewmessage(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'message_date' => 'required|date',
            'message_sender' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInStudents = DB::table('students')->where('LRN', $value)->exists();
                    $existsInGuardians = DB::table('parent_guardians')->where('guardian_id', $value)->exists();
                    $existsInAdmins = DB::table('admins')->where('admin_id', $value)->exists();
    
                    if (!$existsInStudents && !$existsInGuardians && !$existsInAdmins) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
            'message_reciever' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInStudents = DB::table('students')->where('LRN', $value)->exists();
                    $existsInGuardians = DB::table('parent_guardians')->where('guardian_id', $value)->exists();
                    $existsInAdmins = DB::table('admins')->where('admin_id', $value)->exists();
    
                    if (!$existsInStudents && !$existsInGuardians && !$existsInAdmins) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
        ]);
    
        try {
            // Create a new message
            $message = new Message();
            $message->message_sender = $validated['message_sender'];
            $message->message_reciever = $validated['message_reciever'];
            $message->message = $validated['message'];
            $message->message_date = $validated['message_date'];
            $message->save();
    
            // Log a success message
            Log::info('Message successfully composed', [
                'message_id' => $message->message_id,
                'sender' => $validated['message_sender'],
                'receiver' => $validated['message_reciever'],
                'message_content' => $validated['message'],
                'message_date' => $validated['message_date'],
            ]);
    
            // Return the updated list of messages
            return $this->getMessages($request);  // Call getMessages method to return updated conversation
        } catch (\Exception $e) {
            // Log any error that occurs
            Log::error('Error sending message: ' . $e->getMessage());
    
            // Return an error response
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }



    // announcements
    public function getAnnouncements()
    {
        $announcements = Announcement::all();
        return $announcements;
    }
    public function postAnnouncements(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'announcement' => 'required|string|max:5000',
            'admin_id' => 'required|exists:admins,admin_id',
            'class_id' => 'required|exists:classes,class_id',
        ]);

        $announcement = Announcement::create($validatedData);
        return response()->json($announcement, 201);
    }
    public function showtoupdate(Announcement $announcement)
    {
        return response()->json($announcement);
    }
    public function updateAnnouncements(Request $request, Announcement $announcement)
{
    try {
        $formField = $request->validate([
            'title' => 'required|max:255',
            'announcement' => 'required',
        ]);

        $announcement->update($formField);
        return response()->json(['message' => 'Announcement updated successfully!', 'data' => $announcement], 200);
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error updating announcement: ', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to update announcement', 'error' => $e->getMessage()], 500);
    }
    }
    public function destroyAnnouncements($ancmnt_id)
    {
        try {
            $announcement = Announcement::find($ancmnt_id);

            if ($announcement) {
                $announcement->delete();
                return response()->json(['message' => 'Deleted successfully!'], 200);
            }

            return response()->json(['message' => 'Announcement not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting announcement: ' . $e->getMessage()], 500);
        }
    }



    // parent/guardian
    public function showStudent()
    {
        $student = Student::orderBy('lname', 'asc')->get();
        return $student;
    }
    public function getParent() {
        $parents = DB::table('parent_guardians')
            ->select('guardian_id', 'LRN', 'fname', 'lname', 'mname', 'relationship', 'contact_no', 'email')
            ->get()
            ->groupBy('email');
    
        Log::info('Fetched parent data:', ['parents' => $parents]); // Log the fetched data
    
        $formattedParents = collect($parents)->map(function ($group) {
            $lrns = $group->pluck('LRN')->toArray();
            Log::info('LRNs for group:', ['lrns' => $lrns]); // Log LRNs for each group
    
            $students = DB::table('students')->whereIn('LRN', $lrns)->get();
            return [
                'fname' => $group[0]->fname,
                'lname' => $group[0]->lname,
                'mname' => $group[0]->mname,
                'relationship' => $group[0]->relationship,
                'contact_no' => $group[0]->contact_no,
                'email' => $group[0]->email,
                'LRNs' => $lrns, // Ensure this is not empty
                'students' => $students // Ensure this contains valid data
            ];
        })->values();
    
        // Sort parents by last name (lname)
        $sortedParents = $formattedParents->sortBy('lname')->values();
    
        Log::info('Sorted parents data:', ['sortedParents' => $sortedParents]); // Log sorted data
    
        return response()->json($sortedParents);
    }
    public function storeParent(Request $request)
{
    $validatedData = $request->validate([
        'LRN' => 'required|array',
        'LRN.*' => 'exists:students,LRN',
        'fname' => 'required|string|max:255',
        'mname' => 'required|string|max:12',
        'lname' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'relationship' => 'required|string|max:255',
        'contact_no' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:parent_guardians,email',
        'password' => 'required|string|min:8|max:255'
    ]);

    $existingGuardian = ParentGuardian::where('fname', $validatedData['fname'])
                                       ->where('lname', $validatedData['lname'])
                                       ->where('contact_no', $validatedData['contact_no'])
                                       ->first();

    if ($existingGuardian) {
        return response()->json(['message' => 'Guardian already exists.'], 409);
    }

    // Create new ParentGuardian record
    $parents = [];
    foreach ($validatedData['LRN'] as $l) {
        $parentData = array_merge($validatedData, ['LRN' => $l]);
        $parents[] = ParentGuardian::create($parentData);
    }

    return response()->json($parents, 201);
    }
    public function updateParent(Request $request, $email)
     {
         $validatedData = $request->validate([
             'LRN' => 'required|array',
             'LRN.*' => 'exists:students,LRN', 
         ]);
     
         $parentGuardians = ParentGuardian::where('email', $email)->get();
     
         if ($parentGuardians->isEmpty()) {
             return response()->json(['message' => 'No Parent/Guardian found with that email.'], 404);
         }
    
         foreach ($parentGuardians as $guardian) {
             foreach ($validatedData['LRN'] as $l) {
                 if (!ParentGuardian::where('email', $guardian->email)->where('LRN', $l)->exists()) {
                     ParentGuardian::create([
                         'LRN' => $l,
                         'fname' => $guardian->fname,
                         'mname' => $guardian->mname,
                         'lname' => $guardian->lname,
                         'address' => $guardian->address,
                         'relationship' => $guardian->relationship,
                         'contact_no' => $guardian->contact_no,
                         'email' => $guardian->email,
                         'password' => $guardian->password
                     ]);
                 }
             }
         }
     
         return response()->json(['message' => 'LRN(s) added successfully.'], 200);
    }
    public function removeParentStudent(Request $request, $email) {
        $validatedData = $request->validate([
            'LRN' => 'required|exists:parent_guardians,LRN', 
        ]);
    
        $deleted = ParentGuardian::where('LRN', $validatedData['LRN'])->delete();
        Log::info('LRN deletion attempt', ['LRN' => $validatedData['LRN'], 'deleted' => $deleted]);
    
        return response()->json(['deletedCount' => $deleted]);
    }
    public function destroyParent($email)
{
    $parentGuardians = ParentGuardian::where('email', $email)->get();

    if ($parentGuardians->isEmpty()) {
        return response()->json(['message' => 'No Parent/Guardian found with that email.'], 404);
    }

    try {
        foreach ($parentGuardians as $guardian) {
            $guardian->delete();
        }

        return response()->json(['message' => 'All Parent/Guardians with that email deleted successfully.'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error deleting Parent/Guardians: ' . $e->getMessage()], 500);
    }
    }



    

}
