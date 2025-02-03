@extends('layouts.app')

@section('content')
<div class="container-fluid bg-light py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="row g-0">
                    <!-- Left Side - Background Image/Graphic -->
                    <div class="col-md-5 bg-primary d-none d-md-flex align-items-center justify-content-center p-5">
                        <div class="text-center text-white">
                            <h2 class="display-4 mb-3">Welcome</h2>
                            <p class="lead">Create your account and join our team</p>
                            <i class="fas fa-user-plus fa-4x mt-4"></i>
                        </div>
                    </div>

                    <!-- Right Side - Registration Form -->
                    <div class="col-md-7 bg-white">
                        <div class="p-5">
                            <h2 class="text-center text-primary mb-4">Employee Registration</h2>
                            
                            <!-- AJAX Form for Dynamic Submission -->
                            <form id="registrationForm" method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" name="full_name" class="form-control" placeholder="Enter full name" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Phone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" name="phone" class="form-control" placeholder="Enter phone number" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Role</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                            <select name="role" class="form-select" required>
                                                <option value="HR">HR</option>
                                                <option value="Project Manager">Project Manager</option>
                                                <option value="Employee">Employee</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Designation</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                            <input type="text" name="designation" class="form-control" placeholder="Enter designation" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Branch</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            <input type="text" name="branch" class="form-control" placeholder="Enter branch" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Country</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                            <input type="text" name="country" class="form-control" placeholder="Enter country" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 mt-3 mb-2">
                                    <i class="fas fa-user-plus me-2"></i>Register
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registration Successful</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <p class="lead">User account has been created successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            type: 'POST',
            url: '{{ route('register') }}',
            data: $(this).serialize(),
            success: function(response) {
                
                $('#successModal').modal('show');
                
                // Optional: Reset form
                $('#registrationForm')[0].reset();
            },
            error: function(xhr) {
                // Handle errors (show validation messages, etc.)
                alert('Registration failed. Please check your details.');
            }
        });
    });
});


// 
$(document).ready(function() {
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            type: 'POST',
            url: '{{ route('register') }}',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Close create user modal
                    $('#createUserModal').modal('hide');
                    
                    // Show success modal
                    $('#successModal').modal('show');
                    
                    // Optional: Reload page to refresh employee list
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    // Handle specific error messages
                    Swal.fire({
                        icon: 'error',
                        title: 'User Creation Failed',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                // More detailed error handling
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
</script>
@endpush