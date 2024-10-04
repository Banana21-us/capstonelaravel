<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::all();
        return $sections;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'sectionname' => 'required|string|max:255',
        ]);

        $section = Section::create($validatedData);
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
    public function update(UpdateSectionRequest $request, Section $section)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        //
    }
}
