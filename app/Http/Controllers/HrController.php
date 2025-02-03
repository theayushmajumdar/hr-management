<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HrController extends Controller
{
    // Check-In Action for HR
    public function performCheckIn(Request $request)
    {
        $user = User::find($request->input('user_id'));

        // Check if already checked in today
        $existingRecord = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if ($existingRecord && $existingRecord->check_in_time) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in today.'
            ]);
        }

        // Create or update attendance record
        $attendanceRecord = AttendanceRecord::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => Carbon::today()->toDateString()
            ],
            [
                'check_in_time' => Carbon::now()->toTimeString(),
                'check_in_latitude' => $request->input('check_in_latitude'),
                'check_in_longitude' => $request->input('check_in_longitude')
            ]
        );

        return response()->json(['success' => true]);
    }

    // Check-Out Action for HR
    public function performCheckOut(Request $request)
    {
        $user = User::find($request->input('user_id'));

        // Find today's attendance record
        $attendanceRecord = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        // Check if checked in
        if (!$attendanceRecord || !$attendanceRecord->check_in_time) {
            return response()->json([
                'success' => false,
                'message' => 'Must check in first.'
            ]);
        }

        // Check if already checked out
        if ($attendanceRecord->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked out today.'
            ]);
        }

        // Update check-out details
        $attendanceRecord->update([
            'check_out_time' => Carbon::now()->toTimeString(),
            'check_out_latitude' => $request->input('check_out_latitude'),
            'check_out_longitude' => $request->input('check_out_longitude')
        ]);

        return response()->json(['success' => true]);
    }

    // Attendance History for HR (View for all employees)
    public function attendanceHistory()
    {
        $attendanceRecords = AttendanceRecord::orderBy('date', 'desc')->get();

        return view('hr.attendance-history', [
            'attendanceRecords' => $attendanceRecords
        ]);
    }

    // Check-In Page for HR
    public function checkInPage()
    {
        $employees = User::all(); // Get all employees for HR
        return view('hr.check-in', compact('employees'));
    }

    // Check-Out Page for HR
    public function checkOutPage()
    {
        $employees = User::all(); // Get all employees for HR
        return view('hr.check-out', compact('employees'));
    }
}
