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
        $organizedSubjects[$key]['subjects'][] = strtolower($subject->subject_name);
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
    $validatedData = $request->validate([
        'subject_name' => 'required|array',
        'subject_name.*' => 'required|string|max:255',
        'grade_level' => 'required|integer|max:12',
        'strand' => 'required|string|max:255',
    ]);

    // Check if subjects exist
    $subjects = Subject::where('grade_level', $gradeLevel)->where('strand', $strand)->get();

    // Delete existing subjects
    Subject::where('grade_level', $gradeLevel)->where('strand', $strand)->delete();

    // Create new subjects
    foreach ($validatedData['subject_name'] as $name) {
        Subject::create([
            'subject_name' => $name,
            'grade_level' => $validatedData['grade_level'],
            'strand' => $validatedData['strand'],
        ]);
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
