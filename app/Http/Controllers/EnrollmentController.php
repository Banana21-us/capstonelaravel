<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Count the number of enrollments grouped by grade_level and strand
        $enrollmentCounts = DB::table('enrollments')
            ->select('grade_level', 'strand', DB::raw('count(*) as total'))
            ->groupBy('grade_level', 'strand')
            ->orderBy('grade_level')
            ->get();

        // Calculate total counts
        $totalEnrollments = DB::table('enrollments')->count();
        $juniorHighTotal = DB::table('enrollments')->whereIn('grade_level', ['7', '8', '9', '10'])->count();
        $seniorHighTotal = DB::table('enrollments')->whereIn('grade_level', ['11', '12'])->count();

        return response()->json([
            'enrollmentCounts' => $enrollmentCounts,
            'totalEnrollments' => $totalEnrollments,
            'juniorHighTotal' => $juniorHighTotal,
            'seniorHighTotal' => $seniorHighTotal,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        //
    }
}
