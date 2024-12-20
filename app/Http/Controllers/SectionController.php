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
        return $a['level'] <=> $b['level']; // Compare grade levels in ascending order
    });
    
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
        // foreach ($existingSections as $existingSection) {
        //     if (!in_array($existingSection->section_name, $validatedData['section_name'])) {
        //         $existingSection->delete();
        //     }
        // }

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