@extends('layouts.app')


@push('styles')
<style>
    .hover-lift {
        transition: transform 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-10px);
    }
    .border-primary { border: 2px solid #007bff; }
    .border-warning { border: 2px solid #ffc107; }
    .border-success { border: 2px solid #28a745; }

    .stats-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
@php
    $employees = $employees ?? collect([]);
    $activeProjects = $activeProjects ?? 0;
    $completedProjects = $completedProjects ?? 0;
    $teamMembers = $teamMembers ?? 0;
@endphp

@section('content')
    @if (Session::get('user_role') === 'HR')
        <div class="wrapper d-flex">
            <!-- Sidebar -->
            <nav id="sidebar" class="bg-dark text-white" style="min-width: 250px; min-height: 100vh;">
                <div class="sidebar-header p-3">
                    <h4><a href="/welcome" style="text-decoration: none">HR Dashboard</a></h4>
                </div>

                <ul class="list-unstyled p-3">
                    <li class="active">
                        <a href="#" class="text-white text-decoration-none d-block p-2 bg-primary rounded">
                            Employee List
                        </a>
                    </li>
                    
                    <!-- New Attendance Dropdown -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle text-white" id="attendanceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Attendance
                        </a>
                        <ul class="dropdown-menu bg-dark" aria-labelledby="attendanceDropdown">
                            <li><a href="{{ route('hr.check-in') }}" class="dropdown-item text-white">Check In</a></li>
                            <li><a href="{{ route('hr.check-out') }}" class="dropdown-item text-white">Check Out</a></li>
                            <li><a href="{{ route('hr.attendance.view') }}" class="dropdown-item text-white">View Attendance</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <!-- Page Content -->
            <div class="content flex-grow-1 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Employee Management</h2>
                    <div class="d-flex align-items-center">
                        <!-- Add New User Button -->
                        <button type="button" class="btn btn-success rounded-circle me-2"
                            style="width: 50px; height: auto;" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="fas fa-plus"></i>
                        </button>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Employee List Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Designation</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $employee)
                                        <tr>
                                            <td>{{ $employee->employee_id }}</td>
                                            <td>{{ $employee->full_name }}</td>
                                            <td>{{ $employee->email }}</td>
                                            <td>{{ $employee->designation }}</td>
                                            <td>{{ $employee->branch }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $employee->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $employee->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm status-select"
                                                    data-user-id="{{ $employee->id }}" onchange="updateStatus(this)">
                                                    <option value="Active"
                                                        {{ $employee->status === 'Active' ? 'selected' : '' }}>Active
                                                    </option>
                                                    <option value="Inactive"
                                                        {{ $employee->status === 'Inactive' ? 'selected' : '' }}>Inactive
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createUserForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-control" required>
                                <option value="HR">HR</option>
                                <option value="Project Manager">Project Manager</option>
                                <option value="Employee">Employee</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Branch</label>
                            <input type="text" name="branch" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="submitCreateUser">Create User</button>
            </div>
        </div>
    </div>
</div>

        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">User Created Successfully</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <p class="lead">New user has been added to the system.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @elseif (Session::get('user_role') === 'Employee')
        <div class="wrapper d-flex">
            <!-- Sidebar -->
            <nav id="sidebar" class="bg-dark text-white" style="min-width: 250px; min-height: 100vh;">
                <div class="sidebar-header p-3">
                    <h4><a href="/welcome" style="text-decoration: none">Employee Dashboard</a></h4>
                </div>

                <ul class="list-unstyled p-3">
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle text-white" id="attendanceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Attendance
                        </a>
                        <ul class="dropdown-menu bg-dark" aria-labelledby="attendanceDropdown">
                            <li><a href="{{ route('employee.check-in') }}" class="dropdown-item text-white">Check In</a></li>
                            <li><a href="{{ route('employee.check-out') }}" class="dropdown-item text-white">Check Out</a></li>
                            <li><a href="{{ route('employee.attendance-history') }}" class="dropdown-item text-white">Attendance History</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <!-- Page Content -->
            <div class="content flex-grow-1 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Welcome {{ Session::get('user_name') }}</h3>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </div>

                <!-- Dashboard Overview -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card text-center p-3 border-primary">
                            <h5>Leave Taken</h5>
                            <h3 class="text-primary">5 Days</h3>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center p-3 border-warning">
                            <h5>Leave Applied</h5>
                            <h3 class="text-warning">6 Days</h3>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center p-3 border-success">
                            <h5>Leave Balance</h5>
                            <h3 class="text-success">12 Days</h3>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('employee.check-in') }}" class="text-decoration-none">
                            <div class="card bg-primary text-white p-4 text-center hover-lift">
                                <h3>Check In</h3>
                                <p>Start your work day</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('employee.check-out') }}" class="text-decoration-none">
                            <div class="card bg-danger text-white p-4 text-center hover-lift">
                                <h3>Check Out</h3>
                                <p>End your work day</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @elseif (Session::get('user_role') === 'Project Manager')
        <div class="wrapper d-flex">
            <!-- Sidebar -->
            <nav id="sidebar" class="bg-dark text-white" style="min-width: 250px; min-height: 100vh;">
                <div class="sidebar-header p-3">
                    <h4><a href="/welcome" style="text-decoration: none">Project Manager</a></h4>
                </div>

                <ul class="list-unstyled p-3">
                    <li class="nav-item mb-3">
                        <a href="{{ route('projects.index') }}" class="nav-link text-white">
                            <i class="fas fa-project-diagram me-2"></i>Projects
                        </a>
                    </li>
                    <li class="nav-item dropdown mb-3">
                        <a href="#" class="nav-link dropdown-toggle text-white" id="attendanceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-clock me-2"></i>Attendance
                        </a>
                        <ul class="dropdown-menu bg-dark" aria-labelledby="attendanceDropdown">
                            <li><a href="{{ route('pm.check-in') }}" class="dropdown-item text-white">Check In</a></li>
                            <li><a href="{{ route('pm.check-out') }}" class="dropdown-item text-white">Check Out</a></li>
                            <li><a href="{{ route('pm.attendance.history') }}" class="dropdown-item text-white">View Attendance History</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <!-- Page Content -->
            <div class="content flex-grow-1 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Welcome, {{ Session::get('user_name') }}</h3>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card stats-card border-primary hover-lift">
                            <div class="card-body text-center">
                                <i class="fas fa-tasks fa-2x text-primary mb-2"></i>
                                <h5 class="card-title">Active Projects</h5>
                                <h3 class="text-primary">{{ $activeProjects ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card stats-card border-success hover-lift">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h5 class="card-title">Completed Projects</h5>
                                <h3 class="text-success">{{ $completedProjects ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card stats-card border-info hover-lift">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x text-info mb-2"></i>
                                <h5 class="card-title">Team Members</h5>
                                <h3 class="text-info">{{ $teamMembers ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card hover-lift h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Create New Project</h5>
                                <p class="card-text">Start a new project and assign team members</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                    <i class="fas fa-plus me-2"></i>New Project
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card hover-lift h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-list fa-3x text-success mb-3"></i>
                                <h5 class="card-title">View All Projects</h5>
                                <p class="card-text">Manage and monitor your existing projects</p>
                                <a href="{{ route('projects.index') }}" class="btn btn-success">
                                    <i class="fas fa-eye me-2"></i>View Projects
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        @include('projects.create-modal')
        @endif
@endsection


@push('scripts')

    <script>
        
        $(document).ready(function() {
    $('#submitCreateUser').on('click', function(e) {
        e.preventDefault();
        
        // Add form validation
        let form = $('#createUserForm')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: '{{ route('create.user') }}',
            data: $('#createUserForm').serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#createUserModal').modal('hide');
                    $('#successModal').modal('show');
                    
                    // Reload page to refresh employee list
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'User Creation failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });
});


        $(document).ready(function() {
    $('#submitCreateUser').on('click', function(e) {
        e.preventDefault();
        $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
        $.ajax({
            type: 'POST',
            url: '{{ route('create.user') }}',
            data: $('#createUserForm').serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#createUserModal').modal('hide');
                    
                    // Show success modal or alert
                    $('#successModal').modal('show');
                    
                    // Reload page to refresh employee list
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'User Creation failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            }
        });
    });
});

        function updateStatus(selectElement) {
            const userId = selectElement.dataset.userId;
            const status = selectElement.value;

            fetch(`/employee/${userId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const badge = selectElement.closest('tr').querySelector('.badge');
                        badge.textContent = status;
                        badge.className = `badge ${status === 'Active' ? 'bg-success' : 'bg-secondary'}`;
                    }
                })
                .catch(error => console.error('Error:', error));
        }


       
    </script>
@endpush
