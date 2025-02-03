<div class="modal fade" id="editProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProjectForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="project_id" id="edit_project_id">
                    <div class="mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                        <div class="invalid-feedback">Please enter a project name</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                        <div class="invalid-feedback">Please enter a project description</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project Image</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img id="current_project_image" src="" alt="Current Project Image" style="max-width: 100px; height: auto;">
                            <span class="text-muted">Current Image</span>
                        </div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                            <div class="invalid-feedback">Please select a start date</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                            <div class="invalid-feedback">Please select an end date</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign Team Members</label>
                        <select name="users[]" class="form-select" id="edit-team-members-select" multiple required>
                        </select>
                        <div class="invalid-feedback">Please select at least one team member</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitEditProject">
                    <i class="fas fa-save me-2"></i>Update Project
                </button>
            </div>
        </div>
    </div>
</div>

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

    // Function to populate edit modal
    window.populateEditModal = function(projectId) {
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
                    project.team_members.forEach(member => {
                        const option = new Option(member.name, member.id, true, true);
                        select.append(option);
                    });
                    select.trigger('change');
                }

                $('#editProjectModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load project details'
                });
            }
        });
    };

    // Handle form submission
    $('#submitEditProject').click(function(e) {
        e.preventDefault();

        // Validate form
        const form = $('#editProjectForm')[0];
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields.'
            });
            return;
        }

        const formData = new FormData($('#editProjectForm')[0]);
        const projectId = $('#edit_project_id').val();

        $.ajax({
            type: 'POST',
            url: `/projects/${projectId}`,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#submitEditProject')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');
            },
            success: function(response) {
                if (response.success) {
                    $('#editProjectModal').modal('hide');
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
                let errorMessage = 'An error occurred while updating the project.';
                
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
                $('#submitEditProject')
                    .prop('disabled', false)
                    .html('<i class="fas fa-save me-2"></i>Update Project');
            }
        });
    });

    // Reset form when modal is closed
    $('#editProjectModal').on('hidden.bs.modal', function() {
        $('#editProjectForm')[0].reset();
        $('#edit-team-members-select').val(null).trigger('change');
        $('#editProjectForm').removeClass('was-validated');
        $('#current_project_image').attr('src', '');
    });
});
</script>
@endpush