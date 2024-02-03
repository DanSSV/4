<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script>
        window.addEventListener('beforeunload', function (e) {
            // Cancel the event to prompt the user with a confirmation dialog
            e.preventDefault();
            // Chrome requires the returnValue to be set
            e.returnValue = '';

            // Perform any additional actions or display a message
            // This message might not be shown in some modern browsers
            return 'Are you sure you want to leave this page? Your booking will not be saved.';
        });
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking | TriSakay</title>
    <?php
    $imagePath = "../img/Logo_Nobg.png";
    ?>
    <link rel="icon" href="<?php echo $imagePath; ?>" type="image/png" />
    <?php include '../dependencies/dependencies.php'; ?>
    <link rel="stylesheet" href="../css/booking.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js" integrity="sha512-FW2A4pYfHjQKc2ATccIPeCaQpgSQE1pMrEsZqfHNohWKqooGsMYCo3WOJ9ZtZRzikxtMAJft+Kz0Lybli0cbxQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <?php
    include('../php/navbar_commuter.php');
    ?>
    <div class="search">
        <input id="search-input" type="text" placeholder="Where are you heading to?">
        <button id="search-button"><i class="fa-solid fa-magnifying-glass-location fa-lg" style="color: #ffffff;"></i>
            Search</button>
    </div>
    <div id="map" style="width: 100%; height: 50vh;"></div>

    <script>
        const map = L.map('map', {
            zoomControl: false,
            doubleClickZoom: false
        });

        let pickupPoint = null;
        let dropoffPoint = null;
        let watchId;
        let nearestRoadMarker = null; // Store the reference to the nearest road marker

        const greenMarkerIcon = L.icon({
            iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        const redMarkerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        function addMarkerToNearestRoad(latlng) {
            // Use OSRM service to snap marker to nearest road
            axios.get(`https://router.project-osrm.org/nearest/v1/driving/${latlng.lng},${latlng.lat}`)
                .then(response => {
                    const nearestPoint = response.data.waypoints[0].location;
                    const newNearestRoadMarker = L.marker([nearestPoint[1], nearestPoint[0]], { icon: redMarkerIcon }).addTo(map);
                    newNearestRoadMarker.bindPopup('Nearest Road').openPopup();

                    // Remove the previous nearest road marker
                    if (nearestRoadMarker) {
                        map.removeLayer(nearestRoadMarker);
                    }

                    // Set the reference to the new nearest road marker
                    nearestRoadMarker = newNearestRoadMarker;
                })
                .catch(error => {
                    console.error('Error fetching nearest road:', error);
                });
        }

        const onMapDoubleClick = (event) => {
            const { latlng } = event;

            if (dropoffPoint) {
                map.removeLayer(dropoffPoint);
            }
            dropoffPoint = L.marker(latlng, { icon: redMarkerIcon }).addTo(map).bindPopup('Drop-off point').openPopup();

            // Add marker to the nearest road
            addMarkerToNearestRoad(latlng);
        };

        const options = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        };

        const updateMap = (position) => {
            const { latitude, longitude } = position.coords;

            if (!pickupPoint) {
                map.setView([latitude, longitude], 15);

                pickupPoint = L.marker([latitude, longitude], { icon: greenMarkerIcon }).addTo(map);
                pickupPoint.bindPopup('You are here').openPopup();
            } else {
                pickupPoint.setLatLng([latitude, longitude]);
            }
        };

        watchId = navigator.geolocation.watchPosition(updateMap, null, options);
        map.on('dblclick', onMapDoubleClick);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    </script>
</body>

</html>