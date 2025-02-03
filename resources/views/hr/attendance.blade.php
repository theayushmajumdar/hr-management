@extends('layouts.app')

@push('styles')
<style>
    .status-present { color: green; }
    .status-absent { color: red; }
    .status-late { color: orange; }
</style>
@endpush

@section('content')
<div class="wrapper d-flex">
    <!-- Sidebar -->
    <nav id="sidebar" class="bg-dark text-white" style="min-width: 250px; min-height: 100vh;">
        <div class="sidebar-header p-3">
            <h4><a href="/welcome" style="text-decoration: none">HR Dashboard</a></h4>
        </div>

        <ul class="list-unstyled p-3">
            <li>
                <a href="/welcome" class="text-white text-decoration-none d-block p-2">
                    Employee List
                </a>
            </li>
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
            <h2>Daily Attendance</h2>
            <div class="d-flex">
                <input type="date" id="attendanceDate" class="form-control me-2" value="{{ date('Y-m-d') }}">
                <button class="btn btn-success me-2" onclick="fetchAttendance()">Search</button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Check-In Time</th>
                                <th>Check-Out Time</th>
                                <th>Check-In Location</th>
                                <th>Check-Out Location</th>
                                <th>Status</th>
                                <th>Hours Worked</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <!-- Attendance records dynamically fetched here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fetch attendance for the default date when the page loads
    fetchAttendance();
});

function fetchAttendance() {
    const date = document.getElementById('attendanceDate').value;

    fetch(`/hr/attendance/fetch?date=${date}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        const tableBody = document.getElementById('attendanceTableBody');
        tableBody.innerHTML = '';

        if (data.length === 0) {
            tableBody.insertAdjacentHTML('beforeend', `<tr><td colspan="8" class="text-center">No records found for the selected date.</td></tr>`);
            return;
        }

        data.forEach(record => {
            const row = `
                <tr>
                    <td>${record.employee_id}</td>
                    <td>${record.full_name}</td>
                    <td>${record.check_in_time || 'N/A'}</td>
                    <td>${record.check_out_time || 'Not Checked Out'}</td>
                    <td>${record.check_in_latitude || 'N/A'}, ${record.check_in_longitude || 'N/A'}</td>
                    <td>${record.check_out_latitude || 'N/A'}, ${record.check_out_longitude || 'N/A'}</td>
                    <td><span class="${record.status_class}">${record.attendance_status}</span></td>
                    <td>${record.hours_worked || 'N/A'}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to fetch attendance records');
    });
}
</script>
@endpush
