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
        if (!isset($organizedSubjects[$key])) {
            $organizedSubjects[$key] = [
                'level' => $subject->grade_level,
                'strand' => $subject->strand,
                'subjects' => []  
            ];
        }
        $organizedSubjects[$key]['subjects'][] = ucfirst($subject->subject_name);
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

    public function update(Request $request, $gradeLevel, $strand)
{
    // Validate incoming request data
    $validatedData = $request->validate([
        'subject_name' => 'required|array',
        'subject_name.*' => 'required|string|max:255',
        'grade_level' => 'required|integer|max:12',
        'strand' => 'required|string|max:255',
    ]);

    // Retrieve existing subjects
    $existingSubjects = Subject::where('grade_level', $gradeLevel)
                               ->where('strand', $strand)
                               ->get();

    // Create an array of existing subject names for comparison
    $existingSubjectNames = $existingSubjects->pluck('subject_name')->toArray();

    // Update or create subjects based on incoming data
    foreach ($validatedData['subject_name'] as $index => $name) {
        if (isset($existingSubjects[$index])) {
            // Update existing subject
            $existingSubjects[$index]->update([
                'subject_name' => $name,
                // Do not update grade_level and strand if they shouldn't change
            ]);
        } else {
            // Create new subject if it doesn't exist
            Subject::create([
                'subject_name' => $name,
                'grade_level' => $validatedData['grade_level'],
                'strand' => $validatedData['strand'],
            ]);
        }
    }

    // Delete subjects that are no longer present in the incoming data
    foreach ($existingSubjects as $index => $existingSubject) {
        if (!in_array($existingSubject->subject_name, $validatedData['subject_name'])) {
            $existingSubject->delete(); 
            unset($existingSubjects[$index]);
        }
    }
    
    

    return response()->json(['message' => 'Subjects updated successfully.', 'subjects' => $validatedData['subject_name']], 200);
}   
    
    
    /**
     * Remove the specified resource from storage.
     */
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
