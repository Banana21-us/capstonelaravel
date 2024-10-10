<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use Illuminate\Http\Request;


class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all subjects from the database
        $subjects = Subject::all();
    
        // Initialize an array to hold the organized data
        $organizedSubjects = [];
    
        // Loop through each subject
        foreach ($subjects as $subject) {
            // Check if the grade level already exists in the organized array
            if (!isset($organizedSubjects[$subject->grade_level])) {
                // Initialize a new entry for this grade level with user-defined strand
                $organizedSubjects[$subject->grade_level] = [
                    'level' => $subject->grade_level,
                    'strand' => $subject->strand,  // Assuming strand is a property of Subject
                    'subject_name' => []
                ];
            }
    
            // Add the subject name to the corresponding grade level
            $organizedSubjects[$subject->grade_level]['subject_name'][] = strtolower($subject->subject_name);
        }
    
        // Return the organized subjects as an array
        return array_values($organizedSubjects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subject_name' => 'required|array',
            'subject_name.*' => 'required|string|max:255',
            'grade_level' => 'required|integer|max:12',
            'strand' => 'nullable|string|max:255',
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

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $gradeLevel)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'subject_name' => 'required|array',
            'subject_name.*' => 'required|string|max:255',
            'grade_level' => 'required|integer|max:12',
            'strand' => 'required|string|max:255', 
        ]);

        // Fetch existing subjects for the specified grade level
        $subjects = Subject::where('grade_level', $gradeLevel)->get();

        // Check if any subjects were found for the given grade level
        if ($subjects->isEmpty()) {
            return response()->json(['message' => 'No subjects found for this grade level.'], 404);
        }

        // Delete existing subjects for the specified grade level
        Subject::where('grade_level', $gradeLevel)->delete();

        // Create new subjects based on the validated data
        foreach ($validatedData['subject_name'] as $name) {
            Subject::create([
                'subject_name' => $name,
                'grade_level' => $validatedData['grade_level'],
                'strand' => $validatedData['strand'], // Include strand when creating new subjects
            ]);
        }

        return response()->json(['message' => 'Subjects updated successfully.', 'subjects' => $validatedData['subject_name']], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($gradeLevel)
    {
        $deletedRows = Subject::where('grade_level', $gradeLevel)->delete();

        if ($deletedRows) {
            return response()->json(['message' => 'Sections deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'No sections found for this grade level.'], 404);
        }
    }
}
