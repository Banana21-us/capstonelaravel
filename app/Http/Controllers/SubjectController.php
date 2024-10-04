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
        //
        $subjects = Subject::all();
        return $subjects;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subject_name' => 'required|string|max:255',
            'grade_level' => 'required|integer|max:12',
            'strand' => 'string|max:255'
        ]);

        $subject = Subject::create($validatedData);
        return response()->json($subject, 201);
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
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        //
    }
}
