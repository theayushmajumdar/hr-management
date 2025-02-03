<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HRAttendanceController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectManagerAttendanceController;

// Redirect root to register page
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/welcome', function () { return view('welcome'); })->name('welcome')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/welcome', [DashboardController::class, 'index'])->name('welcome');
    Route::post('/employee/{user}/status', [DashboardController::class, 'updateStatus'])->name('employee.status');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/employee/check-in', [AuthController::class, 'showCheckIn'])->name('employee.check-in');
    Route::post('/employee/check-in', [AuthController::class, 'checkIn'])->name('employee.check-in.submit');
    
    Route::get('/employee/check-out', [AuthController::class, 'showCheckOut'])->name('employee.check-out');
    Route::post('/employee/check-out', [AuthController::class, 'checkOut'])->name('employee.check-out.submit');
});

Route::post('/employee/perform-check-in', [EmployeeController::class, 'performCheckIn'])->name('employee.perform-check-in');
Route::post('/employee/perform-check-out', [EmployeeController::class, 'performCheckOut'])->name('employee.perform-check-out');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


Route::post('/create-user', [AuthController::class, 'createUser'])->name('create.user');
Route::post('/employee/{user}/status', [DashboardController::class, 'updateStatus'])->name('employee.update-status');

Route::get('/create-user', [EmployeeController::class, 'createUser'])->name('auth.create-user');

Route::get('/employee/attendance-history', [EmployeeController::class, 'attendanceHistory'])
    ->name('employee.attendance-history');

Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
Route::get('/employee/attendance-history', [EmployeeController::class, 'attendanceHistory'])->name('employee.attendance-history');

Route::middleware(['auth'])->group(function () {
    Route::get('/employee/check-in', [EmployeeController::class, 'checkInPage'])->name('employee.check-in');
    Route::get('/employee/check-out', [EmployeeController::class, 'checkOutPage'])->name('employee.check-out');
    Route::post('/employee/perform-check-in', [EmployeeController::class, 'performCheckIn'])->name('employee.perform-check-in');
    Route::post('/employee/perform-check-out', [EmployeeController::class, 'performCheckOut'])->name('employee.perform-check-out');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/hr/attendance', [HRAttendanceController::class, 'viewAttendance'])
        ->name('hr.attendance.view');
    Route::get('/hr/attendance/fetch', [HRAttendanceController::class, 'fetchAttendance'])
        ->name('hr.attendance.fetch');

});

Route::prefix('hr')->middleware('auth')->group(function () {
    // Check-In and Check-Out Routes for HR
    Route::get('/check-in', [HrController::class, 'checkInPage'])->name('hr.check-in');
    Route::get('/check-out', [HrController::class, 'checkOutPage'])->name('hr.check-out');
    Route::post('/perform-check-in', [HrController::class, 'performCheckIn'])->name('hr.perform-check-in');
    Route::post('/perform-check-out', [HrController::class, 'performCheckOut'])->name('hr.perform-check-out');

    // Attendance History
    Route::get('/attendance-history', [HrController::class, 'attendanceHistory'])->name('hr.attendance.history');
});


Route::middleware(['auth'])->group(function () {
    
    Route::get('/get-employees', [ProjectController::class, 'getEmployees'])
        ->name('get.employees')
        ->middleware('web');  
   
});


Route::middleware(['auth'])->group(function () {
  
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/get-employees', [ProjectController::class, 'getEmployees'])->name('get.employees');
    Route::post('/projects/{project}/update-status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
});



Route::middleware(['auth'])->prefix('pm')->group(function () {
    Route::get('/check-in', [ProjectManagerAttendanceController::class, 'checkInPage'])->name('pm.check-in');
    Route::post('/check-in', [ProjectManagerAttendanceController::class, 'performCheckIn'])->name('pm.check-in.store');
    Route::get('/check-out', [ProjectManagerAttendanceController::class, 'checkOutPage'])->name('pm.check-out');
    Route::post('/perform-check-out', [ProjectManagerAttendanceController::class, 'performCheckOut'])->name('pm.perform-check-out');
    Route::get('/attendance-history', [ProjectManagerAttendanceController::class, 'attendanceHistory'])->name('pm.attendance.history');
});

