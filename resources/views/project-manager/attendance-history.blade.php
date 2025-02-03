@extends('layouts.app')

@section('content')
<div class="wrapper d-flex">
    <!-- Sidebar remains the same -->
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
            <h3>{{ Session::get('user_name') }}'s Attendance History</h3>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>

        <div class="card">
            <div class="card-body">
                @if($attendanceRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped" id="attendanceHistoryTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Check In Time</th>
                                    <th>Check In Location</th>
                                    <th>Check Out Time</th>
                                    <th>Check Out Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendanceRecords as $record)
                                    <tr>
                                        <td>{{ $record->date }}</td>
                                        <td>
                                            {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i A') : 'Not checked in' }}
                                        </td>
                                        <td>
                                            @if($record->check_in_latitude && $record->check_in_longitude)
                                                <span class="map-tooltip" 
                                                      data-latitude="{{ $record->check_in_latitude }}" 
                                                      data-longitude="{{ $record->check_in_longitude }}">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    ({{ $record->check_in_latitude }}, {{ $record->check_in_longitude }})
                                                </span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            {{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('H:i A') : 'Not checked out' }}
                                        </td>
                                        <td>
                                            @if($record->check_out_latitude && $record->check_out_longitude)
                                                <span class="map-tooltip" 
                                                      data-latitude="{{ $record->check_out_latitude }}" 
                                                      data-longitude="{{ $record->check_out_longitude }}">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    ({{ $record->check_out_latitude }}, {{ $record->check_out_longitude }})
                                                </span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">No attendance records found.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tooltip for Map Address -->
<div id="locationTooltip" class="tooltip-map" style="display:none; position:absolute; z-index:1000; padding:10px; background:white; border:1px solid #ccc; max-width:300px;"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            if (window.google && window.google.maps) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places';
            script.async = true;
            script.defer = true;
            
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Failed to load Google Maps API'));
            
            document.head.appendChild(script);
        });
    }

    function initializeMapTooltips() {
        const tooltipElements = document.querySelectorAll('.map-tooltip');
        const tooltipContainer = document.getElementById('locationTooltip');

        tooltipElements.forEach(element => {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                
                const latitude = parseFloat(this.dataset.latitude);
                const longitude = parseFloat(this.dataset.longitude);

                if (google && google.maps && google.maps.Geocoder) {
                    const geocoder = new google.maps.Geocoder();
                    const latlng = { lat: latitude, lng: longitude };

                    geocoder.geocode({ location: latlng }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            tooltipContainer.textContent = results[0].formatted_address;
                            tooltipContainer.style.display = 'block';
                            
                            tooltipContainer.style.top = `${e.pageY + 10}px`;
                            tooltipContainer.style.left = `${e.pageX + 10}px`;
                        } else {
                            tooltipContainer.textContent = 'Address not found';
                            tooltipContainer.style.display = 'block';
                        }
                    });
                } else {
                    tooltipContainer.textContent = 'Google Maps API not fully loaded';
                    tooltipContainer.style.display = 'block';
                }
            });
        });

        document.addEventListener('click', function(e) {
            const tooltipContainer = document.getElementById('locationTooltip');
            const isTooltipClick = e.target.closest('.map-tooltip, #locationTooltip');
            
            if (!isTooltipClick) {
                tooltipContainer.style.display = 'none';
            }
        });
    }

    loadGoogleMapsAPI()
        .then(initializeMapTooltips)
        .catch(error => {
            console.error('Error loading Google Maps API:', error);
            const tooltipContainer = document.getElementById('locationTooltip');
            tooltipContainer.textContent = 'Failed to load Google Maps';
            tooltipContainer.style.display = 'block';
        });
});
</script>
@endpush
