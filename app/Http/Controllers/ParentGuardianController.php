<?php

namespace App\Http\Controllers;

use App\Models\ParentGuardian;
use App\Models\Student;
use App\Http\Requests\StoreParentGuardianRequest;
use App\Http\Requests\UpdateParentGuardianRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
class ParentGuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    

     public function index()
{
    // Retrieve all parent guardians and group them by email
    $parents = DB::table('parent_guardians')
        ->select('guardian_id', 'LRN', 'fname', 'lname', 'relationship', 'contact_no', 'email')
        ->get()
        ->groupBy('email');

    // Format the response to include LRNs and associated students
    $formattedParents = collect($parents)->map(function ($group) {
        // Get all LRNs for this guardian
        $lrns = $group->pluck('LRN')->toArray();

        // Fetch associated students based on LRNs
        $students = DB::table('students')->whereIn('LRN', $lrns)->get();

        return [
            'fname' => $group[0]->fname,
            'lname' => $group[0]->lname,
            'relationship' => $group[0]->relationship,
            'contact_no' => $group[0]->contact_no,
            'email' => $group[0]->email,
            'LRNs' => $lrns,
            'students' => $students // Include the fetched students
        ];
    })->values();

    return response()->json($formattedParents);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validate incoming request data
    $validatedData = $request->validate([
        'LRN' => 'required|array',
        'LRN.*' => 'exists:students,LRN',
        'fname' => 'required|string|max:255',
        'mname' => 'required|string|max:12',
        'lname' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'relationship' => 'required|string|max:255',
        'contact_no' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:parent_guardians,email', // Ensure email is unique
        'password' => 'required|string|min:8|max:255'
    ]);

    // Check if guardian already exists based on other criteria
    $existingGuardian = ParentGuardian::where('fname', $validatedData['fname'])
                                       ->where('lname', $validatedData['lname'])
                                       ->where('contact_no', $validatedData['contact_no'])
                                       ->first();

    if ($existingGuardian) {
        return response()->json(['message' => 'Guardian already exists.'], 409);
    }

    // Create new ParentGuardian record
    $parents = [];
    foreach ($validatedData['LRN'] as $l) {
        $parentData = array_merge($validatedData, ['LRN' => $l]);
        $parents[] = ParentGuardian::create($parentData);
    }

    return response()->json($parents, 201);
}

    /**
     * Display the specified resource.
     */
    public function show(ParentGuardian $parentGuardian)
    {
        return response()->json($parentGuardian);
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, $email)
     {
         // Validate incoming request data
         $validatedData = $request->validate([
             'LRN' => 'required|array',
             'LRN.*' => 'exists:students,LRN', // Ensure each LRN exists in students table
         ]);
     
         // Find the guardian by email
         $parentGuardians = ParentGuardian::where('email', $email)->get();
     
         // Check if any records exist for that email
         if ($parentGuardians->isEmpty()) {
             return response()->json(['message' => 'No Parent/Guardian found with that email.'], 404);
         }
     
         // Loop through each guardian and add new LRN(s)
         foreach ($parentGuardians as $guardian) {
             foreach ($validatedData['LRN'] as $l) {
                 // Check if this LRN already exists for this guardian
                 if (!ParentGuardian::where('email', $guardian->email)->where('LRN', $l)->exists()) {
                     // Create a new record for this LRN under the same guardian
                     ParentGuardian::create([
                         'LRN' => $l,
                         'fname' => $guardian->fname,
                         'mname' => $guardian->mname,
                         'lname' => $guardian->lname,
                         'address' => $guardian->address,
                         'relationship' => $guardian->relationship,
                         'contact_no' => $guardian->contact_no,
                         'email' => $guardian->email,
                         'password' => $guardian->password // or hash it if needed
                     ]);
                 }
             }
         }
     
         return response()->json(['message' => 'LRN(s) added successfully.'], 200);
     }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($email)
{
    // Find all ParentGuardians by email
    $parentGuardians = ParentGuardian::where('email', $email)->get();

    // Check if any records exist
    if ($parentGuardians->isEmpty()) {
        return response()->json(['message' => 'No Parent/Guardian found with that email.'], 404);
    }

    try {
        // Delete all matching records
        foreach ($parentGuardians as $guardian) {
            $guardian->delete();
        }

        return response()->json(['message' => 'All Parent/Guardians with that email deleted successfully.'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error deleting Parent/Guardians: ' . $e->getMessage()], 500);
    }
}
}
