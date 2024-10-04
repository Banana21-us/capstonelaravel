<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Admin::all();
        return $admin;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fname' => 'required|string|max:255',
            'mname' => 'required|string|max:12',
            'lname' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);

        $admin = Admin::create($validatedData);
        return response()->json($admin, 201);
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
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($admin)
    {
        try {
            $admin = Admin::find($admin);

            if ($admin) {
                $admin->delete();
                return response()->json(['message' => 'Deleted successfully!'], 200);
            }

            return response()->json(['message' => 'teacher not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting teacher: ' . $e->getMessage()], 500);
        }
    }
}
