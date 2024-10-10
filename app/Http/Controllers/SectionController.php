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
            ->select('grade_level', 'section_name', 'strand') // Include strand in the selection
            ->get()
            ->groupBy('grade_level');

        // Format the response to include sections for each grade level
        $formattedGrades = collect($grades)->map(function ($group) {
            return [
                'level' => $group[0]->grade_level, // Extracting grade level
                'strand' => $group[0]->strand, // Extracting strand from the first item
                'sections' => $group->map(function ($item) {
                    return $item->section_name; // Only return section names
                })->values()->toArray() // Convert to array
            ];
        })->values()->toArray(); // Convert outer collection to array

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

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        return response()->json($section);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $gradeLevel)
    {
        $validatedData = $request->validate([
            'section_name' => 'required|array',
            'section_name.*' => 'required|string|max:255',
            'grade_level' => 'required|integer|max:12',
            'strand' => 'required|string|max:255', 
        ]);
        $sections = Section::where('grade_level', $gradeLevel)->get();

        if ($sections->isEmpty()) {
            return response()->json(['message' => 'No sections found for this grade level.'], 404);
        }

        Section::where('grade_level', $gradeLevel)->delete();

        foreach ($validatedData['section_name'] as $name) {
            Section::create([
                'section_name' => $name,
                'grade_level' => $validatedData['grade_level'],
                'strand' => $validatedData['strand'], // Include strand when creating new sections
            ]);
        }

        return response()->json(['message' => 'Sections updated successfully.', 'sections' => $validatedData['section_name']], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
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