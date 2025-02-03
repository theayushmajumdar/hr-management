<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
{
    $validatedData = $request->validate([
        'full_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'required|in:HR,Employee',
        'branch' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'designation' => 'required|string'
    ]);

    try {
        // Generate Employee ID (Format: EMP followed by 6 digits)
        $lastUser = User::latest()->first();
        $nextId = $lastUser ? (intval(substr($lastUser->employee_id, 3)) + 1) : 1;
        $employeeId = 'EMP' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        // Create new user
        $user = User::create([
            'employee_id' => $employeeId,
            'full_name' => $validatedData['full_name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'branch' => $validatedData['branch'],
            'country' => $validatedData['country'],
            'designation' => $validatedData['designation'],
            'status' => 'Active' 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating user: ' . $e->getMessage()
        ], 500);
    }
}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            // Update user's location and login timestamp
            $user->latitude = $request->input('latitude');
            $user->longitude = $request->input('longitude');
            $user->last_login_at = now();
            $user->save();

            // Existing session logic
            $request->session()->regenerate();
            Session::put('user_id', Auth::id());
            Session::put('user_name', Auth::user()->full_name);
            Session::put('user_email', Auth::user()->email);
            Session::put('user_role', Auth::user()->role);

            return redirect()->route('welcome')->with('success');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Session::forget(['user_id', 'user_name', 'user_email', 'user_role']);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }

    public function showCheckIn()
    {
        return view('employee.check-in');
    }

    public function checkIn(Request $request)
{
    $request->validate([
        'check_in_latitude' => 'nullable|numeric',
        'check_in_longitude' => 'nullable|numeric'
    ]);

    $user = Auth::user();
    
    $user->check_in_time = Carbon::now();
    $user->check_in_latitude = $request->input('check_in_latitude');
    $user->check_in_longitude = $request->input('check_in_longitude');
    $user->save();

    return response()->json(['success' => true, 'message' => 'Check-in successful']);
}

    public function showCheckOut()
    {
        return view('employee.check-out');
    }

    public function checkOut(Request $request)
{
    $request->validate([
        'check_out_latitude' => 'nullable|numeric',
        'check_out_longitude' => 'nullable|numeric'
    ]);

    $user = Auth::user();
    
    $user->check_out_time = Carbon::now();
    $user->check_out_latitude = $request->input('check_out_latitude');
    $user->check_out_longitude = $request->input('check_out_longitude');
    $user->save();

    return response()->json(['success' => true, 'message' => 'Check-out successful']);
}

public function performCheckIn(Request $request)
{
    $validatedData = $request->validate([
        'check_in_latitude' => 'required|numeric',
        'check_in_longitude' => 'required|numeric',
        'check_in_time' => 'required|date'
    ]);

    try {
        // Update the authenticated user's record
        auth()->user()->update([
            'check_in_latitude' => $validatedData['check_in_latitude'],
            'check_in_longitude' => $validatedData['check_in_longitude'],
            'check_in_time' => $validatedData['check_in_time']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Check-in failed: ' . $e->getMessage()
        ], 500);
    }
}
public function createUser(Request $request)
{
    $validatedData = $request->validate([
        'full_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'required|in:HR,Project Manager,Employee',
        'branch' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'designation' => 'required|string'
    ]);

    try {
        // Generate Employee ID (Format: EMP followed by 6 digits)
        $lastUser = User::latest()->first();
        $nextId = $lastUser ? (intval(substr($lastUser->employee_id, 3)) + 1) : 1;
        $employeeId = 'EMP' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        // Create new user
        $user = User::create([
            'employee_id' => $employeeId,
            'full_name' => $validatedData['full_name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'branch' => $validatedData['branch'],
            'country' => $validatedData['country'],
            'designation' => $validatedData['designation'],
            'status' => 'Active' 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating user: ' . $e->getMessage()
        ], 500);
    }
}
}
