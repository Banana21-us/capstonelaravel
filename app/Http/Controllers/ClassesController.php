<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Subject;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Requests\StoreClassesRequest;
use App\Http\Requests\UpdateClassesRequest;
use Illuminate\Support\Facades\Log; 
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
class ClassesController extends Controller
{

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

    public function index()
    {
    //
    }

     public function store(Request $request)
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

    

    /**
     * Display the specified resource.
     */
    public function show(Classes $classes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   

        public function update(Request $request, $id)
        {  
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
     

     

     


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($class_id)
{
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

}
