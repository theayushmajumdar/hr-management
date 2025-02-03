<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HRAttendanceController extends Controller
{
    public function viewAttendance()
    {
        if (Auth::user()->role !== 'HR') {
            return redirect('welcome')->with('error', 'Unauthorized access');
        }

        $today = Carbon::today();
        $attendanceRecords = $this->getAttendanceRecords($today);
        return view('hr.attendance', compact('attendanceRecords'));
    }

    public function fetchAttendance(Request $request)
{
    if (Auth::user()->role !== 'HR') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $date = $request->input('date');

    if (!$date) {
        return response()->json(['error' => 'Invalid date'], 400);
    }

    // Fetch records where the `date` column matches the searched date
    $attendanceRecords = DB::table('attendance_records')
        ->join('users', 'attendance_records.user_id', '=', 'users.id')
        ->where('attendance_records.date', '=', $date) // Filter by exact date
        ->select(
            'attendance_records.*',
            'users.employee_id',
            'users.full_name'
        )
        ->get()
        ->map(function ($record) {
            return $this->addAttendanceMetadata($record);
        });

    return response()->json($attendanceRecords);
}


    private function getAttendanceRecords($date)
{
    return AttendanceRecord::query()
        ->join('users', 'attendance_records.user_id', '=', 'users.id')
        ->whereDate('attendance_records.check_in_time', $date)
        ->select(
            'attendance_records.*',
            'users.employee_id',
            'users.full_name'
        )
        ->get()
        ->map(function($record) {
            return $this->addAttendanceMetadata($record);
        });
}


    private function addAttendanceMetadata($record)
    {
        $record->status_class = $this->getStatusClass($record);
        $record->attendance_status = $this->getAttendanceStatus($record);
        $record->hours_worked = $this->calculateHoursWorked($record);
        return $record;
    }

    private function getStatusClass($record)
    {
        if (!$record->check_in_time) return 'status-absent';
        
        $checkInTime = Carbon::parse($record->check_in_time);
        $startOfWorkDay = Carbon::parse('09:00:00');
        
        return $checkInTime->gt($startOfWorkDay) ? 'status-late' : 'status-present';
    }

    private function getAttendanceStatus($record)
    {
        if (!$record->check_in_time) return 'Absent';
        
        $checkInTime = Carbon::parse($record->check_in_time);
        $startOfWorkDay = Carbon::parse('09:00:00');
        
        return $checkInTime->gt($startOfWorkDay) ? 'Late' : 'Present';
    }

    private function calculateHoursWorked($record)
    {
        if (!$record->check_in_time || !$record->check_out_time) return null;

        $checkIn = Carbon::parse($record->check_in_time);
        $checkOut = Carbon::parse($record->check_out_time);
        
        return $checkIn->diffInHours($checkOut);
    }
}
