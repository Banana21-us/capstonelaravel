<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $student = Student::orderBy('lname', 'asc')->get();
        return $student;
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'LRN' => 'required|digits_between:10,14',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'mname' => 'required|string|max:12',
            'suffix' => 'nullable|string|max:255',
            'bdate' => 'required|string|max:255',
            'bplace' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);
        $validatedData['suffix'] = $validatedData['suffix'] ?? '';

        $student = Student::create($validatedData);
        return response()->json($student, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($lrn)
{
    $student = Student::where('LRN', $lrn)
                ->orderBy('lname', 'asc') // Order by last name in ascending order
                ->first();

    if (!$student) {
        return response()->json(['message' => 'Student not found.'], 404);
    }

    return response()->json($student, 200);
}


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}
