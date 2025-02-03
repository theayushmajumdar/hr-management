function getLocation(formId) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const form = document.getElementById(formId);
                
                // Create hidden input fields for coordinates
                const latInput = document.createElement('input');
                latInput.type = 'hidden';
                latInput.name = 'latitude';
                latInput.value = position.coords.latitude;
                
                const longInput = document.createElement('input');
                longInput.type = 'hidden';
                longInput.name = 'longitude';
                longInput.value = position.coords.longitude;
                
                // Add to form
                form.appendChild(latInput);
                form.appendChild(longInput);
                
                // Submit the form
                form.submit();
            },
            (error) => {
                alert('Error getting location: ' + error.message);
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}