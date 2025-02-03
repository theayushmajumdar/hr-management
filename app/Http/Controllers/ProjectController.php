<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
        {
    $projects = Project::with(['users' => function($query) {
        $query->select('users.id', 'users.full_name'); 
    }])->get();
    
    return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        // Enable query logging for debugging
        DB::enableQueryLog();
        
        try {
            
            Log::info('Project creation request:', [
                'data' => $request->all(),
                'files' => $request->hasFile('image') ? 'Image present' : 'No image'
            ]);

            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'users' => 'required|array|min:1',
                'users.*' => 'exists:users,id'
            ]);

            // Start database transaction
            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('projects', 'public');
                Log::info('Image stored at: ' . $imagePath);
            } else {
                throw new \Exception('Image file is required');
            }

            // Create project
            $project = Project::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'image' => $imagePath,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => 'Active'
            ]);

            Log::info('Project created:', ['project_id' => $project->id]);

            // Attach users
            $project->users()->attach($validated['users']);
            
            Log::info('Users attached to project:', [
                'project_id' => $project->id,
                'user_ids' => $validated['users']
            ]);

            // Commit transaction
            DB::commit();

            // Log queries for debugging
            Log::info('Queries executed:', ['queries' => DB::getQueryLog()]);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error:', [
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating project:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the project: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEmployees() 
    {
        try {
            
            if (!class_exists('App\Models\User')) {
                throw new \Exception('User model not found');
            }

            
            $query = User::query();
            
            
            Log::info('Employee query:', ['sql' => $query->where('role', 'Employee')->toSql()]);
            
            $employees = $query->where('role', 'Employee')
                ->select('id', 'full_name', 'email')
                ->get();

            // Log the number of employees found
            Log::info('Employees found:', ['count' => $employees->count()]);

            // Transform the data
            $formattedEmployees = $employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'text' => $employee->full_name . ' (' . $employee->email . ')'
                ];
            })->values()->all();

            return response()->json($formattedEmployees);

        } catch (\Exception $e) {
            Log::error('Error in getEmployees:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to fetch employees',
                'debug_message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Project $project)
    {
        try {
            $projectData = [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'start_date' => $project->start_date->format('Y-m-d'),
                'end_date' => $project->end_date->format('Y-m-d'),
                'image_url' => Storage::url($project->image),
                'team_members' => $project->users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->full_name
                    ];
                })
            ];
    
            return response()->json([
                'success' => true,
                'project' => $projectData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load project details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Project $project)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'users' => 'required|array',
                'users.*' => 'exists:users,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Update basic project information
            $project->name = $request->name;
            $project->description = $request->description;
            $project->start_date = $request->start_date;
            $project->end_date = $request->end_date;

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                // Delete old image if it exists
                if ($project->image) {
                    Storage::delete($project->image);
                }
                // Store new image
                $project->image = $request->file('image')->store('projects', 'public');
            }

            $project->save();

            // Update team members
            if ($request->has('users')) {
                $project->users()->sync($request->users);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateStatus(Request $request, Project $project)
{
    $request->validate([
        'status' => 'required|in:Active,Completed,On Hold,In Review,In Progress'
    ]);

    $project->update(['status' => $request->status]);

    return response()->json(['success' => true]);
}
}