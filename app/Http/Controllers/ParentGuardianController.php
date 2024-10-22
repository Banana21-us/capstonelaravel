<?php

namespace App\Http\Controllers;

use App\Models\ParentGuardian;
use App\Models\Student;
use App\Http\Requests\StoreParentGuardianRequest;
use App\Http\Requests\UpdateParentGuardianRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ParentGuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    

     public function index() {
        $parents = DB::table('parent_guardians')
            ->select('guardian_id', 'LRN', 'fname', 'lname', 'relationship', 'contact_no', 'email')
            ->get()
            ->groupBy('email');
    
        Log::info('Fetched parent data:', ['parents' => $parents]); // Log the fetched data
    
        $formattedParents = collect($parents)->map(function ($group) {
            $lrns = $group->pluck('LRN')->toArray();
            Log::info('LRNs for group:', ['lrns' => $lrns]); // Log LRNs for each group
    
            $students = DB::table('students')->whereIn('LRN', $lrns)->get();
            return [
                'fname' => $group[0]->fname,
                'lname' => $group[0]->lname,
                'relationship' => $group[0]->relationship,
                'contact_no' => $group[0]->contact_no,
                'email' => $group[0]->email,
                'LRNs' => $lrns, // Ensure this is not empty
                'students' => $students // Ensure this contains valid data
            ];
        })->values();
    
        Log::info('Formatted parents data:', ['formattedParents' => $formattedParents]); // Log formatted data
    
        return response()->json($formattedParents);
    }
     

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'LRN' => 'required|array',
        'LRN.*' => 'exists:students,LRN',
        'fname' => 'required|string|max:255',
        'mname' => 'required|string|max:12',
        'lname' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'relationship' => 'required|string|max:255',
        'contact_no' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:parent_guardians,email',
        'password' => 'required|string|min:8|max:255'
    ]);

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
         $validatedData = $request->validate([
             'LRN' => 'required|array',
             'LRN.*' => 'exists:students,LRN', 
         ]);
     
         $parentGuardians = ParentGuardian::where('email', $email)->get();
     
         if ($parentGuardians->isEmpty()) {
             return response()->json(['message' => 'No Parent/Guardian found with that email.'], 404);
         }
    
         foreach ($parentGuardians as $guardian) {
             foreach ($validatedData['LRN'] as $l) {
                 if (!ParentGuardian::where('email', $guardian->email)->where('LRN', $l)->exists()) {
                     ParentGuardian::create([
                         'LRN' => $l,
                         'fname' => $guardian->fname,
                         'mname' => $guardian->mname,
                         'lname' => $guardian->lname,
                         'address' => $guardian->address,
                         'relationship' => $guardian->relationship,
                         'contact_no' => $guardian->contact_no,
                         'email' => $guardian->email,
                         'password' => $guardian->password
                     ]);
                 }
             }
         }
     
         return response()->json(['message' => 'LRN(s) added successfully.'], 200);
     }
     
     public function remove(Request $request, $email) {
        // Validate LRN from query parameters
        $validatedData = $request->validate([
            'LRN' => 'required|exists:parent_guardians,LRN', // Change to expect a single value
        ]);
    
        // Delete the specified LRN
        $deleted = ParentGuardian::where('LRN', $validatedData['LRN'])->delete();
        Log::info('LRN deletion attempt', ['LRN' => $validatedData['LRN'], 'deleted' => $deleted]);
    
        return response()->json(['deletedCount' => $deleted]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($email)
{
    $parentGuardians = ParentGuardian::where('email', $email)->get();

    if ($parentGuardians->isEmpty()) {
        return response()->json(['message' => 'No Parent/Guardian found with that email.'], 404);
    }

    try {
        foreach ($parentGuardians as $guardian) {
            $guardian->delete();
        }

        return response()->json(['message' => 'All Parent/Guardians with that email deleted successfully.'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error deleting Parent/Guardians: ' . $e->getMessage()], 500);
    }
}
}

