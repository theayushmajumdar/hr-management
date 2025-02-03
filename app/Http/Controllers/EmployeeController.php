<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function performCheckIn(Request $request)
    {
        $user = Auth::user();

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

    public function performCheckOut(Request $request)
    {
        $user = Auth::user();

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

    public function checkInPage() 
{
    return view('employee.check-in');
}

public function checkOutPage() 
{
    return view('employee.check-out');
}

public function attendanceHistory() 
{
    $user = auth()->user();
    
    $attendanceRecords = AttendanceRecord::where('user_id', $user->id)
        ->orderBy('date', 'desc')
        ->get();

    return view('employee.attendance-history', [
        'user' => $user,
        'attendanceRecords' => $attendanceRecords,
        'leaveStats' => [
            'taken' => 5,
            'applied' => 2,
            'balance' => 10
        ]
    ]);
}

}