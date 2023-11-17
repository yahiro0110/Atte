<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    // TODO: あとで$idを実装する
    public function show()
    {
        $employee = Employee::find(1);
        return view('home', ['employee' => $employee]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        //
    }

    public function punch($employeeId)
    {
        $result = Employee::with('attendance')->find($employeeId);

        if ($result && $result->attendance) {
            switch ($result->attendance->work_status) {
                case '1':
                    $workStatus = Attendance::WORK_STATUSES['clockIn'];
                    break;

                case '2':
                    $workStatus = Attendance::WORK_STATUSES['onBreak'];
                    break;

                case '3':
                    $workStatus = Attendance::WORK_STATUSES['offBreak'];
                    break;

                case '4':
                    $workStatus = Attendance::WORK_STATUSES['clockOut'];
                    break;

                case '5':
                    $workStatus = Attendance::WORK_STATUSES['noClockOut'];
                    break;

                case '6':
                    $workStatus = Attendance::WORK_STATUSES['noWork'];
                    break;

                default:
                    $workStatus = 0;
                    break;
            }
        } else {
            $workStatus = null;
        }

        session()->flash('work_status', $workStatus);

        return view('punch', ['result' => $result]);
    }
}
