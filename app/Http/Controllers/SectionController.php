<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{

    public function index()
    {
        $sections = Section::all(); 
        $organizedSections = [];

        foreach ($sections as $section) {
            $key = $section->grade_level . '-' . $section->strand;

            if (!isset($organizedSections[$key])) {
                $organizedSections[$key] = [
                    'level' => $section->grade_level,
                    'strand' => $section->strand,
                    'sections' => []
                ];
            }
            $organizedSections[$key]['sections'][] = ucfirst($section->section_name);
        }

        return array_values($organizedSections);
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
    public function update(Request $request, $gradeLevel, $strand)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'section_name' => 'required|array',
            'section_name.*' => 'required|string|max:255',
            'grade_level' => 'required|integer|max:12',
            'strand' => 'required|string|max:255', 
        ]);

        // Retrieve existing sections
        $existingSections = Section::where('grade_level', $gradeLevel)
                                ->where('strand', $strand)
                                ->get();

        // Update or create sections based on incoming data
        foreach ($validatedData['section_name'] as $index => $name) {
            if (isset($existingSections[$index])) {
                // Update existing section
                $existingSections[$index]->update([
                    'section_name' => $name,
                    // Do not update grade_level and strand if they shouldn't change
                ]);
            } else {
                // Create new section if it doesn't exist
                Section::create([
                    'section_name' => $name,
                    'grade_level' => $validatedData['grade_level'],
                    'strand' => $validatedData['strand'],
                ]);
            }
        }

        // Delete sections that are no longer present in the incoming data
        foreach ($existingSections as $existingSection) {
            if (!in_array($existingSection->section_name, $validatedData['section_name'])) {
                $existingSection->delete();
            }
        }

        return response()->json(['message' => 'Sections updated successfully.', 'sections' => $validatedData['section_name']], 200);
    }



    public function destroy($gradeLevel, $strand)
    {
        // Delete sections associated with the specified grade level and strand
        $deletedRows = Section::where('grade_level', $gradeLevel)
                              ->where('strand', $strand)
                              ->delete();
    
        if ($deletedRows) {
            return response()->json(['message' => 'Sections deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'No sections found for this grade level and strand.'], 404);
        }
    }
}