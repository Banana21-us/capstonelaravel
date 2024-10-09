<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grades = DB::table('sections')
        ->select('grade_level', 'section_name')
        ->get()
        ->groupBy('grade_level');

    // Format the response to include sections for each grade level
    $formattedGrades = collect($grades)->map(function ($group) {
        return [
            'level' => $group[0]->grade_level,
            'sections' => $group->pluck('section_name')->toArray() // Get all section names for this grade level
        ];
    })->values();

    return response()->json($formattedGrades);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'section_name' => 'required|array',
            'section_name.*' => 'required|string|max:255',
            'grade_level' => 'required|integer|max:12',
        ]);
        
        $section = [];
        
        foreach ($validatedData['section_name'] as $name) {
            $section[] = Section::create([
                'section_name' => $name,
                'grade_level' => $validatedData['grade_level'],
            ]);
        }

        return response()->json($section, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $gradeLevel)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'section_name' => 'required|array', // Expecting an array of section names
            'section_name.*' => 'required|string|max:255', // Each name must be a string
            'grade_level' => 'required|integer|max:12',
        ]);
    
        // Find sections associated with the specified grade level
        $sections = Section::where('grade_level', $gradeLevel)->get();
    
        // Check if sections exist
        if ($sections->isEmpty()) {
            return response()->json(['message' => 'No sections found for this grade level.'], 404);
        }
    
        // Clear existing sections for this grade level (optional)
        Section::where('grade_level', $gradeLevel)->delete();
    
        // Create new sections with validated data
        foreach ($validatedData['section_name'] as $name) {
            Section::create([
                'section_name' => $name,
                'grade_level' => $validatedData['grade_level'],
            ]);
        }
    
        return response()->json(['message' => 'Sections updated successfully.', 'sections' => $validatedData['section_name']], 200);
    }

    public function destroy($gradeLevel)
    {
        // Delete sections associated with the specified grade level
        $deletedRows = Section::where('grade_level', $gradeLevel)->delete();

        if ($deletedRows) {
            return response()->json(['message' => 'Sections deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'No sections found for this grade level.'], 404);
        }
    }
}
