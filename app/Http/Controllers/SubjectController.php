<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 


class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
    
        return array_values($organizedSubjects);
    }

    public function store(Request $request)
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

    public function show(Subject $subject)
    {
        //
    }

//     public function update(Request $request, $gradeLevel, $strand)
// {
//     // Validate incoming request data
//     $validatedData = $request->validate([
//         'subject_name' => 'required|array',
//         'subject_name.*' => 'required|string|max:255',
//         'grade_level' => 'required|integer|max:12',
//         'strand' => 'required|string|max:255',
//     ]);

//     // Retrieve existing subjects
//     $existingSubjects = Subject::where('grade_level', $gradeLevel)
//                                ->where('strand', $strand)
//                                ->get();

//     // Create an array of existing subject names for comparison
//     $existingSubjectNames = $existingSubjects->pluck('subject_name')->toArray();

//     // Update or create subjects based on incoming data
//     foreach ($validatedData['subject_name'] as $index => $name) {
//         if (isset($existingSubjects[$index])) {
//             // Update existing subject
//             $existingSubjects[$index]->update([
//                 'subject_name' => $name,
//                 // Do not update grade_level and strand if they shouldn't change
//             ]);
//         } else {
//             // Create new subject if it doesn't exist
//             Subject::create([
//                 'subject_name' => $name,
//                 'grade_level' => $validatedData['grade_level'],
//                 'strand' => $validatedData['strand'],
//             ]);
//         }
//     }

//     // Delete subjects that are no longer present in the incoming data
//     // foreach ($existingSubjects as $index => $existingSubject) {
//     //     if (!in_array($existingSubject->subject_name, $validatedData['subject_name'])) {
//     //         $existingSubject->delete(); 
//     //         unset($existingSubjects[$index]);
//     //     }
//     // }

//     return response()->json(['message' => 'Subjects updated successfully.', 'subjects' => $validatedData], 200);
// }   

public function update(Request $request, $gradeLevel, $strand)
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
    
    /**
     * Remove the specified resource from storage.
     */

     public function removesubject(Request $request, $id)
{
    $subject = Subject::find($id);

    if (!$subject) {
        return response()->json(['message' => 'Subject not found.'], 404);
    }
    $subject->delete();
    return response()->json(['message' => 'Subject deleted successfully.'], 200);
}


    public function destroy($gradeLevel, $strand)
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
}
