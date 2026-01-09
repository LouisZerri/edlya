document.addEventListener('DOMContentLoaded', function() {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.querySelectorAll('[id^="latitude-"]').forEach(function(input) {
                input.value = lat;
            });
            
            document.querySelectorAll('[id^="longitude-"]').forEach(function(input) {
                input.value = lng;
            });
        }, function(error) {
            console.log('Géolocalisation non disponible:', error.message);
        });
    }
});