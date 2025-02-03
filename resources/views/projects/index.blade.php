@extends('layouts.app')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar" class="bg-dark text-white" style="min-width: 250px; min-height: 100vh;">
        <div class="sidebar-header p-3">
            <h4><a href="/welcome" style="text-decoration: none">Project Manager</a></h4>
        </div>

        <ul class="list-unstyled p-3">
            <li class="nav-item mb-3">
                <a href="{{ route('projects.index') }}" class="nav-link text-white hover-opacity">
                    <i class="fas fa-project-diagram me-2"></i>Projects
                </a>
            </li>
            <li class="nav-item dropdown mb-3">
                <a href="#" class="nav-link dropdown-toggle text-white hover-opacity" id="attendanceDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-clock me-2"></i>Attendance
                </a>
                <ul class="dropdown-menu bg-dark" aria-labelledby="attendanceDropdown">
                    <li><a href="{{ route('pm.check-in') }}" class="dropdown-item text-white hover-opacity">Check In</a></li>
                    <li><a href="{{ route('pm.check-out') }}" class="dropdown-item text-white hover-opacity">Check Out</a></li>
                    <li><a href="{{ route('pm.attendance.history') }}" class="dropdown-item text-white hover-opacity">View Attendance History</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div class="content flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark">Projects Overview</h3>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-success rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                        data-bs-toggle="modal" 
                        data-bs-target="#createProjectModal" 
                        style="width: 48px; height: 48px;">
                    <i class="fas fa-plus"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger d-flex align-items-center shadow-sm">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-4">
            @foreach($projects as $project)
            <div class="col-md-6 col-xl-6">
                <div class="card h-100 shadow-hover border-0 rounded-3 overflow-hidden">
                    <div class="position-relative">
                        <div class="image-container">
                            <img src="{{ Storage::url($project->image) }}" 
                                 class="card-img-top project-image" 
                                 alt="{{ $project->name }}">
                        </div>
                        <div class="position-absolute top-0 end-0 p-3">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle shadow-sm opacity-75 hover-opacity-100" 
                                        type="button" 
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <button class="dropdown-item edit-project" data-id="{{ $project->id }}">
                                            <i class="fas fa-edit me-2 text-primary"></i> Edit Project
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge status-badge" 
                                  data-status="{{ $project->status }}">
                                {{ $project->status }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">{{ $project->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($project->description, 100) }}</p>
                        
                        <div class="mb-4">
                            <label class="form-label small text-muted mb-2">Update Status</label>
                            <select class="form-select form-select-sm status-select" 
                                    data-project-id="{{ $project->id }}">
                                @foreach(['Active', 'In Progress', 'On Hold', 'Completed', 'In Review'] as $status)
                                    <option value="{{ $status }}" 
                                            {{ $project->status === $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="small text-muted mb-2">Timeline</label>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ $project->start_date->format('M d, Y') }}
                                </span>
                                <i class="fas fa-arrow-right text-muted small"></i>
                                <span class="badge bg-light text-dark">
                                    <i class="far fa-calendar-check me-1"></i>
                                    {{ $project->end_date->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="small text-muted mb-2">Team Members</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($project->users as $user)
                                <span class="badge bg-info bg-opacity-10 text-info user-badge" 
                                      data-bs-toggle="tooltip" 
                                      data-bs-placement="top" 
                                      data-bs-custom-class="user-tooltip"
                                      title="{{ $user->full_name }}">
                                    <i class="fas fa-user-circle me-1"></i>
                                    
                                    {{ ($user->full_name) }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@include('projects.create-modal')
@include('projects.partials.edit-modal')
@endsection

@push('styles')
<style>
:root {
    --transition-speed: 0.3s;
}

.hover-opacity {
    transition: opacity var(--transition-speed) ease;
}

.hover-opacity:hover {
    opacity: 0.8;
}

.hover-opacity-100:hover {
    opacity: 1 !important;
}

.shadow-hover {
    transition: all var(--transition-speed) ease;
}

.shadow-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.15) !important;
}

.image-container {
    height: 220px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.project-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform var(--transition-speed) ease;
}

.card:hover .project-image {
    transform: scale(1.05);
}

.user-badge {
    cursor: pointer;
    transition: all var(--transition-speed) ease;
    padding: 0.5rem 0.75rem;
}

.user-badge:hover {
    background-color: rgba(13, 202, 240, 0.2) !important;
    transform: translateY(-2px);
}

.status-badge {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    border-radius: 20px;
}

.status-badge[data-status="Active"] {
    background-color: #28a745;
    color: white;
}

.status-badge[data-status="In Progress"] {
    background-color: #007bff;
    color: white;
}

.status-badge[data-status="On Hold"] {
    background-color: #ffc107;
    color: #000;
}

.status-badge[data-status="Completed"] {
    background-color: #6f42c1;
    color: white;
}

.status-badge[data-status="In Review"] {
    background-color: #17a2b8;
    color: white;
}

.status-select {
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
    padding: 0.4rem 0.75rem;
    font-size: 0.875rem;
    transition: all var(--transition-speed) ease;
}

.status-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.user-tooltip {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    background-color: rgba(0, 0, 0, 0.9);
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for edit modal
    $('#edit-team-members-select').select2({
        theme: 'bootstrap-5',
        placeholder: "Select team members",
        allowClear: true,
        width: '100%',
        ajax: {
            url: '/get-employees',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    // Add click handler for edit button in the index page
    $(document).on('click', '.edit-project', function() {
        const projectId = $(this).data('id');
        populateEditModal(projectId);
    });

    // Function to populate edit modal
    window.populateEditModal = function(projectId) {
        // Show loading state
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/projects/${projectId}/edit`,
            type: 'GET',
            success: function(response) {
                const project = response.project;
                
                // Populate form fields
                $('#edit_project_id').val(project.id);
                $('#edit_name').val(project.name);
                $('#edit_description').val(project.description);
                $('#edit_start_date').val(project.start_date);
                $('#edit_end_date').val(project.end_date);
                $('#current_project_image').attr('src', project.image_url);

                // Populate Select2 with existing team members
                if (project.team_members) {
                    const select = $('#edit-team-members-select');
                    select.empty(); // Clear existing options
                    project.team_members.forEach(member => {
                        const option = new Option(member.name, member.id, true, true);
                        select.append(option);
                    });
                    select.trigger('change');
                }

                // Close loading indicator and show modal
                Swal.close();
                $('#editProjectModal').modal('show');
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load project details'
                });
            }
        });
    };
    // Initialize tooltips with custom options
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
        delay: {
            show: 200,
            hide: 100
        }
    }));

    // Handle status change with loading state
    $('.status-select').change(function() {
        const select = $(this);
        const projectId = select.data('project-id');
        const newStatus = select.val();
        const originalStatus = select.find('option:selected').text();
        
        select.prop('disabled', true);
        
        $.ajax({
            url: `/projects/${projectId}/update-status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                toastr.success('Project status updated successfully');
                
                // Update the status badge
                const badge = select.closest('.card').find('.status-badge');
                badge.attr('data-status', newStatus);
                window.location.reload();
                badge.text(newStatus);
            },
            error: function() {
                toastr.error('Failed to update project status');
                // Revert selection on error
                select.val(originalStatus);
            },
            complete: function() {
                select.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush