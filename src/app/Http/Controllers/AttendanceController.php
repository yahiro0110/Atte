<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index($id, Request $request)
    {
        $setYear = $request->has('year') ? $request->input('year') : now()->year;
        $setMonth = $request->has('month') ? $request->input('month') : now()->month;
        $results = Attendance::with('breaktimes')
            ->where('employee_id', $id)
            ->whereYear('date', $setYear)
            ->whereMonth('date', $setMonth)
            ->get();

        $breakTimes = [];
        $workedTimes = [];

        foreach ($results as $attendance) {
            $breakTime = $attendance->calculateTotalBreakTimes($attendance->id, $attendance->breaktimes);
            $workedTime = $attendance->calculateTotalWorkTime($attendance->start_time, $attendance->end_time, $breakTime);
            $breakTimes[$attendance->id] = $breakTime;
            $workedTimes[$attendance->id] = $workedTime;
        }

        return view('index', compact('results', 'breakTimes', 'workedTimes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Unused variable $request removed
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        // Unused variable $attendance removed
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        // Unused variable $attendance removed
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        // Unused variables $request and $attendance removed
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        // Unused variable $attendance removed
        //
    }
}
