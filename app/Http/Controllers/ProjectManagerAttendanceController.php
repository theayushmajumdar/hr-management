<?php

namespace App\Http\Controllers;


use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProjectManagerAttendanceController extends Controller
{
    public function checkInPage()
    {
        return view('project-manager.check-in');
    }

    public function performCheckIn(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'Project Manager') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $existingRecord = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if ($existingRecord && $existingRecord->check_in_time) {
            return response()->json(['success' => false, 'message' => 'Already checked in today.']);
        }

        AttendanceRecord::updateOrCreate(
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

    public function checkOutPage()
    {
        return view('project-manager.check-out');
    }

    public function performCheckOut(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'Project Manager') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $attendanceRecord = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$attendanceRecord || !$attendanceRecord->check_in_time) {
            return response()->json(['success' => false, 'message' => 'Must check in first.']);
        }

        if ($attendanceRecord->check_out_time) {
            return response()->json(['success' => false, 'message' => 'Already checked out today.']);
        }

        $attendanceRecord->update([
            'check_out_time' => Carbon::now()->toTimeString(),
            'check_out_latitude' => $request->input('check_out_latitude'),
            'check_out_longitude' => $request->input('check_out_longitude')
        ]);

        return response()->json(['success' => true]);
    }

    public function attendanceHistory()
    {
        $user = auth()->user();

        if ($user->role !== 'Project Manager') {
            abort(403, 'Unauthorized access.');
        }

        $attendanceRecords = AttendanceRecord::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('project-manager.attendance-history', [
            'attendanceRecords' => $attendanceRecords
        ]);
    }
}
