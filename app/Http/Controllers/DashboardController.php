<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller 
{
    public function index()
    {
        // If user is HR, fetch employees
        if (Auth::user()->role === 'HR') {
            $employees = User::where('role', 'Employee')->get();
            return view('welcome', compact('employees'));
        }
        
        // For regular employees
        $user = Auth::user();
        return view('welcome', compact('user'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:Active,Inactive'
        ]);
        
        $user->update(['status' => $request->status]);
        return response()->json(['success' => true]);
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