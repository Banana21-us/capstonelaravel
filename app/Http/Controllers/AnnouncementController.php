<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $announcements = Announcement::all();
        // return $announcements;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $validatedData = $request->validate([
        //     'title' => 'required|string|max:255',
        //     'announcement' => 'required|string|max:5000',
        //     'admin_id' => 'required|exists:admins,admin_id',
        //     'class_id' => 'required|exists:classes,class_id',
        // ]);

        // $announcement = Announcement::create($validatedData);
        // return response()->json($announcement, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        // return response()->json($announcement);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        // $formField = $request->validate([
        //     'title' => 'required|max:255',
        //     'announcement' => 'required'
        // ]);

        // $announcement->update($formField);
        // return $announcement;
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ancmnt_id)
    {
        // try {
        //     $announcement = Announcement::find($ancmnt_id);

        //     if ($announcement) {
        //         $announcement->delete();
        //         return response()->json(['message' => 'Deleted successfully!'], 200);
        //     }

        //     return response()->json(['message' => 'Announcement not found.'], 404);
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'Error deleting announcement: ' . $e->getMessage()], 500);
        // }
    }
}
