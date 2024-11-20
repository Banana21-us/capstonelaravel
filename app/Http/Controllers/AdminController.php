<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // $admins = Admin::all();
    // $sortedAdmins = $admins->sortBy(function ($admin) {
    //     return strtolower($admin->lname);
    // })->values();
    // return response()->json($sortedAdmins);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $validatedData = $request->validate([
        //     'fname' => 'required|string|max:255',
        //     'mname' => 'required|string|max:12',
        //     'lname' => 'required|string|max:255',
        //     'role' => 'required|string|max:255',
        //     'address' => 'required|string|max:255',
        //     'email' => 'required|email|max:255',
        //     'password' => 'required|string|min:8|max:255'
        // ]);

        // $admin = Admin::create($validatedData);
        // return response()->json($admin, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Admin $admin)
    {
        // Log the incoming request data
        // Log::info('Updating admin record', [
        //     'admin_id' => $admin->id,
        //     'request_data' => $request->all(),
        // ]);
    
        // // Validate the incoming request data
        // $validatedData = $request->validate([
        //     'fname' => 'sometimes|required|string|max:255',
        //     'mname' => 'sometimes|required|string|max:12',
        //     'lname' => 'sometimes|required|string|max:255',
        //     'address' => 'sometimes|required|string|max:255',
        //     'email' => 'sometimes|required|email|max:255',
        // ]);
    
        // // Log the validated data
        // Log::info('Validated data for admin update', [
        //     'admin_id' => $admin->id,
        //     'validated_data' => $validatedData,
        // ]);
    
        // // Update the admin instance with validated data
        // $admin->update($validatedData);
    
        // // Log successful update
        // Log::info('Admin record updated successfully', [
        //     'admin_id' => $admin->id,
        //     'updated_data' => $admin,
        // ]);
    
        // // Return a JSON response with the updated admin data
        // return response()->json($admin, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($admin)
    {
        // try {
        //     $admin = Admin::find($admin);

        //     if ($admin) {
        //         $admin->delete();
        //         return response()->json(['message' => 'Deleted successfully!'], 200);
        //     }

        //     return response()->json(['message' => 'teacher not found.'], 404);
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'Error deleting teacher: ' . $e->getMessage()], 500);
        // }
    }
}
