<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createProjectForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="name" class="form-control" required>
                        <div class="invalid-feedback">Please enter a project name</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                        <div class="invalid-feedback">Please enter a project description</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <div class="invalid-feedback">Please select a project image</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                            <div class="invalid-feedback">Please select a start date</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                            <div class="invalid-feedback">Please select an end date</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign Team Members</label>
                        <select name="users[]" class="form-select" id="team-members-select" multiple required>
                        </select>
                        <div class="invalid-feedback">Please select at least one team member</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitCreateProject">
                    <i class="fas fa-save me-2"></i>Create Project
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#team-members-select').select2({
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

    // Form submission
    $('#submitCreateProject').click(function(e) {
        e.preventDefault();

        // Validate form
        const form = $('#createProjectForm')[0];
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields.'
            });
            return;
        }

        const formData = new FormData($('#createProjectForm')[0]);

        // Log form data for debugging
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            type: 'POST',
            url: '/projects',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#submitCreateProject')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Creating...');
            },
            success: function(response) {
                if (response.success) {
                    $('#createProjectModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                }
            },
            error: function(xhr) {
                console.error('Error response:', xhr);
                let errorMessage = 'An error occurred while creating the project.';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            },
            complete: function() {
                $('#submitCreateProject')
                    .prop('disabled', false)
                    .html('<i class="fas fa-save me-2"></i>Create Project');
            }
        });
    });

    // Reset form when modal is closed
    $('#createProjectModal').on('hidden.bs.modal', function() {
        $('#createProjectForm')[0].reset();
        $('#team-members-select').val(null).trigger('change');
        $('#createProjectForm').removeClass('was-validated');
    });
});
</script>
@endpush