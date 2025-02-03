@extends('layouts.app')

@section('content')
<div class="wrapper d-flex">
    <!-- Sidebar remains the same -->
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
            <h3>Check Out</h3>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>
        
        <!-- Check-Out Content -->
        <div class="card">
            <div class="card-body">
                <form id="checkOutForm" method="POST" action="{{ route('employee.perform-check-out') }}">
                    @csrf
                    <input type="hidden" id="checkOutLatitude" name="check_out_latitude">
                    <input type="hidden" id="checkOutLongitude" name="check_out_longitude">
                    <input type="hidden" id="checkOutAccuracy" name="check_out_accuracy">
                    
                    <div id="locationStatus" class="alert alert-info" style="display: none;">
                        Fetching location... Please wait.
                    </div>
                    
                    <div id="locationError" class="alert alert-danger" style="display: none;">
                        Location access is required. Please enable location services.
                    </div>
                    
                    <h6 class="mb-3">Please click the Button to save your check-out details:</h6>
                    <button type="submit" id="checkOutButton" class="btn btn-danger btn-lg w-10" disabled>
                        <span id="checkOutSpinner" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true" style="display: none;"></span>
                        Check Out
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
    // Vanilla JS implementation with fallback for jQuery
    const checkOutForm = document.getElementById('checkOutForm');
    const checkOutLatitude = document.getElementById('checkOutLatitude');
    const checkOutLongitude = document.getElementById('checkOutLongitude');
    const checkOutAccuracy = document.getElementById('checkOutAccuracy');
    const checkOutButton = document.getElementById('checkOutButton');
    const locationStatus = document.getElementById('locationStatus');
    const locationError = document.getElementById('locationError');
    const checkOutSpinner = document.getElementById('checkOutSpinner');

    // Location options for more accurate positioning
    const locationOptions = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    };

    // Function to handle successful location retrieval
    function handleLocationSuccess(position) {
        checkOutLatitude.value = position.coords.latitude;
        checkOutLongitude.value = position.coords.longitude;
        checkOutAccuracy.value = position.coords.accuracy;

        locationStatus.style.display = 'none';
        checkOutButton.disabled = false;
        
        // Optional: Display location accuracy
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
        
        checkOutButton.disabled = true;
        console.error("Geolocation error:", error);
    }

    // Check geolocation support
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

    // Form submission handling
    checkOutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Button loading state
        checkOutButton.disabled = true;
        checkOutSpinner.style.display = 'inline-block';
        
        // Validate location
        if (!checkOutLatitude.value || !checkOutLongitude.value) {
            locationError.textContent = 'Please allow location access to check out.';
            locationError.style.display = 'block';
            checkOutButton.disabled = false;
            checkOutSpinner.style.display = 'none';
            return;
        }
        
        // Create FormData object
        const formData = new FormData(checkOutForm);
        
        // Fetch API for AJAX request
        fetch('{{ route('employee.perform-check-out') }}', {
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
                    title: 'Check-out Successful!',
                    text: 'You have been checked out.',
                    timer: 2000,
                    timerProgressBar: true,
                    didClose: () => {
                        window.location.href = '{{ route('dashboard') }}';
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Check-out Failed',
                    text: data.message || 'Please try again.'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Check-out failed. Please try again.'
            });
        })
        .finally(() => {
            checkOutButton.disabled = false;
            checkOutSpinner.style.display = 'none';
        });
    });
});
</script>
@endpush