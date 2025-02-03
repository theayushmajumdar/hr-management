@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create New User</div>
                <div class="card-body">
                    <form id="createUserForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Full Name</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="HR">HR</option>
                                    <option value="HR">Project Manager</option>
                                    <option value="Employee">Employee</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Designation</label>
                                <input type="text" name="designation" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Branch</label>
                                <input type="text" name="branch" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Country</label>
                                <input type="text" name="country" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Create User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            type: 'POST',
            url: '{{ route('create.user') }}',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('User created successfully');
                    window.location.href = '{{ route('welcome') }}';
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON.message || 'User Creation failed';
                alert(errorMessage);
            }
        });
    });
});
</script>
@endpush