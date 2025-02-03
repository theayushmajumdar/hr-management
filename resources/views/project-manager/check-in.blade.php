@extends('layouts.app')

@section('content')
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
            <h3>{{ Session::get('user_name') }}'s Check In</h3>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>
        
        <!-- Check-In Content -->
        <div class="card">
            <div class="card-body">
                <form id="checkInForm" method="POST" action="{{ route('pm.check-in.store') }}">
                    @csrf
                    <input type="hidden" id="checkInLatitude" name="check_in_latitude">
                    <input type="hidden" id="checkInLongitude" name="check_in_longitude">
                    <input type="hidden" id="checkInAccuracy" name="check_in_accuracy">
                    <input type="hidden" id="checkInTime" name="check_in_time">
                    
                    <div id="locationStatus" class="alert alert-info" style="display: none;">
                        Fetching location... Please wait.
                    </div>
                    
                    <div id="locationError" class="alert alert-danger" style="display: none;">
                        Location access is required. Please enable location services.
                    </div>
                    
                    <h6 class="mb-3">Click the button to save your check-in details:</h6>
                    <button type="submit" id="checkInButton" class="btn btn-success btn-lg w-10" disabled>
                        <span id="checkInSpinner" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true" style="display: none;"></span>
                        Check In
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInForm = document.getElementById('checkInForm');
    const checkInLatitude = document.getElementById('checkInLatitude');
    const checkInLongitude = document.getElementById('checkInLongitude');
    const checkInAccuracy = document.getElementById('checkInAccuracy');
    const checkInTime = document.getElementById('checkInTime');
    const checkInButton = document.getElementById('checkInButton');
    const locationStatus = document.getElementById('locationStatus');
    const locationError = document.getElementById('locationError');
    const checkInSpinner = document.getElementById('checkInSpinner');

    // Location options for more accurate positioning
    const locationOptions = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    };

    // Function to handle successful location retrieval
    function handleLocationSuccess(position) {
        const currentTime = new Date().toISOString();
        
        checkInLatitude.value = position.coords.latitude;
        checkInLongitude.value = position.coords.longitude;
        checkInAccuracy.value = position.coords.accuracy;
        checkInTime.value = currentTime;

        locationStatus.style.display = 'none';
        checkInButton.disabled = false;
        
        locationStatus.textContent = `Location found. Accuracy: ${position.coords.accuracy.toFixed(2)} meters`;
        locationStatus.style.display = 'block';
    }

    // Function to handle location error
    function handleLocationError(error) {
        locationStatus.style.display = 'none';
        locationError.style.display = 'block';
        
        switch(error.code) {
            case error.PERMISSION_DENIED:
                locationError.textContent = "Location access denied. Please enable location permissions.";
                break;
            case error.POSITION_UNAVAILABLE:
                locationError.textContent = "Location information unavailable.";
                break;
            case error.TIMEOUT:
                locationError.textContent = "Location request timed out.";
                break;
            default:
                locationError.textContent = "An unknown error occurred while fetching location.";
        }
        
        checkInButton.disabled = true;
        console.error("Geolocation error:", error);
    }

    // Immediately attempt to get location when page loads
    if ("geolocation" in navigator) {
        locationStatus.style.display = 'block';
        
        navigator.geolocation.getCurrentPosition(
            handleLocationSuccess, 
            handleLocationError, 
            locationOptions
        );
    } else {
        locationError.textContent = "Geolocation is not supported by this browser.";
        locationError.style.display = 'block';
    }

    // Form submission handling remains the same as in your original script
    checkInForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        checkInButton.disabled = true;
        checkInSpinner.style.display = 'inline-block';
        
        if (!checkInLatitude.value || !checkInLongitude.value) {
            locationError.textContent = 'Please allow location access to check in.';
            locationError.style.display = 'block';
            checkInButton.disabled = false;
            checkInSpinner.style.display = 'none';
            return;
        }
        
        const formData = new FormData(checkInForm);
        
        fetch('{{ route('employee.perform-check-in') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Check-in Successful!',
                    text: 'You have been checked in.',
                    timer: 2000,
                    timerProgressBar: true,
                    didClose: () => {
                        window.location.href = '{{ route('dashboard') }}';
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Check-in Failed',
                    text: data.message || 'Please try again.'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Check-in failed. Please try again.'
            });
        })
        .finally(() => {
            checkInButton.disabled = false;
            checkInSpinner.style.display = 'none';
        });
    });
});
</script>
@endpush