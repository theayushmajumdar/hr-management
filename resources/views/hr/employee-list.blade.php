@extends('layouts.app')

@section('content')
<div class="wrapper d-flex">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0">HR Dashboard</h4>
        </div>

        <ul class="list-unstyled components">
            <li>
                <a href="#" class="active d-flex align-items-center">
                    <i class="fas fa-users me-2"></i>
                    Employee List
                </a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Employee Management</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Employee List</li>
                        </ol>
                    </nav>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-4"></i>Logout
                    </button>
                </form>
            </div>

            <!-- Employee List Card -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Employees</h5>
                        <div class="input-group" style="width: 300px;">
                            <input type="text" class="form-control" placeholder="Search employee...">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Designation</th>
                                    <th>Branch</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $employee->employee_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                {{ strtoupper(substr($employee->full_name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $employee->full_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->designation }}</td>
                                    <td>{{ $employee->branch }}</td>
                                    <td class="text-center">
                                        <button 
                                            class="btn status-toggle {{ $employee->status === 'Active' ? 'btn-success' : 'btn-outline-secondary' }}"
                                            data-user-id="{{ $employee->id }}"
                                            data-status="{{ $employee->status }}"
                                            onclick="toggleStatus(this)">
                                            <i class="fas fa-power-off me-2"></i>
                                            <span class="status-text">{{ $employee->status }}</span>
                                        </button>
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
</div>

@push('scripts')
<script>
function toggleStatus(button) {
    const userId = button.dataset.userId;
    const currentStatus = button.dataset.status;
    const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';

    // Add loading state
    button.disabled = true;
    const originalContent = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

    fetch(`/employee/${userId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button state
            button.dataset.status = newStatus;
            const statusText = button.querySelector('.status-text');
            statusText.textContent = newStatus;
            
            if (newStatus === 'Active') {
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
            } else {
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }

            // Show success notification
            showNotification('Status updated successfully!', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update status!', 'error');
    })
    .finally(() => {
        // Remove loading state
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endpush

<!-- Internal CSS -->
@section('styles')
<style>
    
    .custom-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        font-size: 14px;
        background-color: #f9f9f9;
        color: #ddd;
    }

    .custom-table th, .custom-table td {
        padding: 12px 15px;
        text-align: left;
    }

    .custom-table th {
        background-color: #007bff;
        color: white;
        text-transform: uppercase;
    }

    .custom-table tbody tr {
        border-bottom: 1px solid #ddd;
    }

    .custom-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .custom-table tbody tr:nth-child(even) {
        background-color: #f7f7f7;
    }

    .status-toggle {
        display: flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 25px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .status-toggle:hover {
        background-color: #f1f1f1;
    }
</style>
@endsection

@endsection
