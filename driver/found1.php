<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Driver | Accepted</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        .navbar {
            background-color: #033957 !important;
        }
    </style>
    <?php
    include '../dependencies/dependencies.php';
    ?>
    <link rel="stylesheet" href="../css/found1.css">
</head>

<body>
    <?php
    include('../php/navbar_commuter.php');
    ?>
    <div id="map" style="width: 100%; height: 60vh"></div>
    <h5 id="coordinates">Latitude: N/A, Longitude: N/A</h5>
    <div class="address">
        <h5>Locating your current address...</h5>
    </div>
    <h5 id="nearest-toda">Nearest TODA: Loading...</h5>
    <h5>Your IP address is: <span id="ip-address"></span></h5>
    <?php
    require '../db/dbconn.php';

    $todaQuery = "SELECT toda, terminal FROM todalocation";
    $todaResult = mysqli_query($conn, $todaQuery);

    $todaLocations = [];

    while ($tl = mysqli_fetch_assoc($todaResult)) {
        $todaLocations[] = [
            'toda' => $tl['toda'],
            'terminal' => json_decode($tl['terminal'], true)
        ];
    }

    $todalocationData = json_encode($todaLocations);
    ?>


    <script src="../js/ip.js"></script>
    <script>
        var map = L.map("map", {
            zoomControl: false,
            doubleClickZoom: false,
        }).setView([0, 0], 15);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        var pickupPoint;

        function updatePickupPoint(lat, lng, accuracy) {
            var signalStrength = accuracy;
            var signalStrengthCategory, textColor;

            if (signalStrength <= 40) {
                signalStrengthCategory = "Good";
                textColor = "green";
            } else if (signalStrength <= 80) {
                signalStrengthCategory = "Fair";
                textColor = "yellow";
            } else {
                signalStrengthCategory = "Bad";
                textColor = "red";
            }

            var coordinatesElement = document.getElementById("coordinates");
            coordinatesElement.textContent = `Latitude: ${lat}, Longitude: ${lng}`;

            if (pickupPoint) {
                pickupPoint.setLatLng([lat, lng]);
                pickupPoint
                    .getPopup()
                    .setContent(
                        `Signal Strength: <span style="color: ${textColor};">${signalStrengthCategory}</span><br>You are here!`
                    );
            } else {
                pickupPoint = L.marker([lat, lng]).addTo(map);
                pickupPoint
                    .bindPopup(
                        `Signal Strength: <span style="color: ${textColor};">${signalStrengthCategory}</span><br>You are here!`
                    )
                    .openPopup();
            }

            map.setView([lat, lng], 15);
        }

        function updateLocation(position) {
            var { latitude, longitude, accuracy } = position.coords;
            updatePickupPoint(latitude, longitude, accuracy);

            // Save to database
            $.ajax({
                url: 'found1_back.php', // Update with the correct path to your PHP script
                type: 'POST',
                data: {
                    latitude: latitude,
                    longitude: longitude
                },
                success: function (response) {
                    var resp = JSON.parse(response);
                    if (resp.status === 'success') {
                        console.log('Coordinates saved to database.');
                    } else {
                        console.error('Failed to save coordinates:', resp.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                }
            });

            $.ajax({
                url: "https://nominatim.openstreetmap.org/reverse",
                method: "GET",
                dataType: "json",
                data: {
                    format: "json",
                    lat: latitude,
                    lon: longitude,
                    zoom: 18,
                },
                success: function (data) {
                    var address = data.display_name.split(",").slice(0, -3).join(",");
                    $(".address h5").text(address);
                },
                error: function (error) {
                    console.error("Error getting address: " + error.statusText);
                },
            });
        }

        function handleError(error) {
            console.error("Error:", error.message);
        }

        var options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0,
        };

        var watchId = navigator.geolocation.watchPosition(
            updateLocation,
            handleError,
            options
        );


    </script>
</body>

</html>