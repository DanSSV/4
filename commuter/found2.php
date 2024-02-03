<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Test #1</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    
    
    <?php
    include '../dependencies/dependencies.php';
    ?>
    <link rel="stylesheet" href="../css/found2.css">
</head>

<body>
    <?php
    include('../php/navbar_commuter.php');
    ?>
    <div id="map" style="width: 100%; height: 60vh"></div>
    <h5 id="dateUpdated"></h5>
    <script>
        var map = L.map("map", {
            zoomControl: false,
            doubleClickZoom: false,
        }).setView([0, 0], 16);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        const greenMarkerIcon = L.icon({
            iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        // Initial marker, which will be updated with real data
        var marker = L.marker([0, 0], { icon: greenMarkerIcon }).addTo(map);

        // Function to fetch data from the server and update the map
        function fetchDataAndUpdateMap() {
            $.ajax({
                url: 'found2_back.php', // Your PHP file that fetches data from the database
                type: 'POST',
                data: { 'liveID': 5 }, // Your hardcoded liveID
                dataType: 'json',
                success: function (response) {
                    // Assuming the response has the structure { LiveLat: '', LiveLng: '', DateUpdated: ''}
                    if (response.LiveLat && response.LiveLng) {
                        var newLatLng = new L.LatLng(response.LiveLat, response.LiveLng);
                        marker.setLatLng(newLatLng); // Update marker position
                        // map.setView(newLatLng); // Optionally pan the map to the new marker location
                        marker.bindPopup("Tricycle").openPopup(); // Add popup to the marker
                    }
                    if (response.DateUpdated) {
                        document.getElementById('dateUpdated').textContent = "Last Updated: " + response.DateUpdated; // Update the date display
                    }
                },
                error: function (xhr, status, error) {
                    console.error("An error occurred: " + error);
                }
            });
        }

        var marker1 = L.marker([0, 0]).addTo(map).bindPopup('You are here').openPopup();

        function updateLocation(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            marker1.setLatLng([latitude, longitude]).update();
           
        }
        function handleError(error) {
            // console.error('Error getting user location: ' + error.message);
        }

        // Watch user's position
        var watchId = navigator.geolocation.watchPosition(updateLocation, handleError);
        // Update the map every second
        setInterval(fetchDataAndUpdateMap, 1000);

    </script>
</body>

</html>