<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TriSakay | Commuter</title>
    <?php
    include '../dependencies/dependencies.php';
    ?>
    <link rel="stylesheet" href="../css/mylocation.css">
    <!-- Include Leaflet CSS and JavaScript -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for AJAX -->
</head>
<style>
    p {
        color: white;
        text-align: center;
        margin-top: 2.5vh;
    }
</style>

<body>

    <?php
    include('../php/navbar_commuter.php');
    ?>

    <div id="map" style="width: 100%; height: 60vh;"></div>
    <div class="address">
        <p>Locating your current address...</p>
    </div>
    <div class="mb-2">
        <button type="submit" class="btn btn-default custom-btn" onclick="redirectToCommuter()">
            <i class="fa-solid fa-rotate-left fa-lg" style="color: #00000;"></i> Back
        </button>
    </div>
    <script src="../js/button.js"></script>

    <script>
        var map = L.map('map', {
            zoomControl: false
        }).setView([0, 0], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([0, 0]).addTo(map).bindPopup('You are here').openPopup(); // Open the popup on load

        function updateLocation(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            marker.setLatLng([latitude, longitude]).update();
            map.setView([latitude, longitude], 15);

            // Reverse geocode the coordinates to get the address
            $.ajax({
                url: 'https://nominatim.openstreetmap.org/reverse',
                method: 'GET',
                dataType: 'json',
                data: {
                    format: 'json',
                    lat: latitude,
                    lon: longitude,
                    zoom: 18,
                },
                success: function (data) {
                    var address = data.display_name;
                    // Split the address into words
                    var addressWords = address.split(',');
                    // Remove the last 3 words
                    addressWords.splice(-3);
                    // Join the remaining words back into a string
                    var shortenedAddress = addressWords.join(',');

                    // Update the p element with the shortened address
                    $('.address p').text(shortenedAddress);
                },
                error: function (error) {
                    console.error('Error getting address: ' + error);
                }
            });
        }

        function handleError(error) {
            // console.error('Error getting user location: ' + error.message);
        }

        // Watch user's position
        var watchId = navigator.geolocation.watchPosition(updateLocation, handleError);

        // To stop watching the position, you can call navigator.geolocation.clearWatch(watchId);
    </script>

</body>

</html>