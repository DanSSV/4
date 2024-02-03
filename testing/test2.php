<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TriSakay | Commuter</title>
    <?php
    $imagePath = "../img/Logo_Nobg.png";
    ?>
    <link rel="icon" href="<?php echo $imagePath; ?>" type="image/png" />
    <?php include '../dependencies/dependencies.php'; ?>
    <link rel="stylesheet" href="../css/booking.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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
    <div class="locations">
        <p id="pickup-address">Locating your current address...</p>
        <p id="dropoff-address">Add a drop-off point by double-clicking.</p>
    </div>
    <div class="dropdown-center">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Number of passenger(s): <span id="passenger-display">1</span>
        </button>4
        <ul class="dropdown-menu" id="passenger-dropdown">
            <li><a class="dropdown-item" href="#" data-value="1">1</a></li>
            <li><a class="dropdown-item" href="#" data-value="2">2</a></li>
            <li><a class="dropdown-item" href="#" data-value="3">3</a></li>
            <li><a class="dropdown-item" href="#" data-value="4">4</a></li>
        </ul>
    </div>
    <div class="mb-2">
        <form action="confirm_booking.php" method="post" id="booking-form">
            <div class="mb-2">
                <button type="submit" class="btn btn-default custom-btn" id="confirm-booking-btn">
                    Confirm Booking
                </button>
            </div>
        </form>

    </div>
    <?php include '../db/dbconn.php';

    $routeQuery = "SELECT toda, borders FROM route WHERE status = 'active'";
    $routeResult = mysqli_query($conn, $routeQuery);
    $todaQuery = "SELECT toda, terminal FROM todalocation";
    $todaResult = mysqli_query($conn, $todaQuery);

    $routeData = [];
    $todaLocations = [];

    while ($row = mysqli_fetch_assoc($routeResult)) {
        $routeData[] = [
            'toda' => $row['toda'],
            'borders' => json_decode($row['borders'], true)
        ];
    }

    while ($tl = mysqli_fetch_assoc($todaResult)) {
        $todaLocations[] = [
            'toda' => $tl['toda'],
            'terminal' => json_decode($tl['terminal'], true)
        ];
    }

    $jsonData = json_encode($routeData);
    $todalocationData = json_encode($todaLocations);
    ?>

    <script src="search.js"></script>
    <script>
        var map = L.map('map', {
            zoomControl: false,
            doubleClickZoom: false
        }).setView([14.954283534502583, 120.90080909502916], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const dbPolygons = <?php echo $jsonData; ?>;
        const todalocations = <?php echo $todalocationData; ?>;
        const polygonsLayer = L.layerGroup().addTo(map);
        const markersLayer = L.layerGroup().addTo(map);

        function displayPolygons() {
            dbPolygons.forEach((polygonData) => {
                const latlngs = polygonData.borders.latlngs[0].map(coord => [coord.lat, coord.lng]);
                const polygon = L.polygon(latlngs, {
                    color: 'transparent',
                    fillColor: 'green',
                    fillOpacity: 0.3,
                    weight: 0
                }).addTo(polygonsLayer);
                polygon.toda = polygonData.toda;

                const matchingToda = todalocations.find(tl => tl.toda === polygonData.toda);

                if (matchingToda) {
                    const marker = L.marker([matchingToda.terminal.latlng.lat, matchingToda.terminal.latlng.lng]).addTo(markersLayer);
                    marker.bindPopup(`${polygonData.toda} Terminal`)
                }
            });
        }

        function isPointInPolygon(point, polygon) {
            const polyPoints = polygon.getLatLngs()[0];
            const x = point.lat, y = point.lng;
            let inside = false;

            for (let i = 0, j = polyPoints.length - 1; i < polyPoints.length; j = i++) {
                const xi = polyPoints[i].lat, yi = polyPoints[i].lng;
                const xj = polyPoints[j].lat, yj = polyPoints[j].lng;
                const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                if (intersect) inside = !inside;
            }
            return inside;
        }

        const greenMarkerIcon = L.icon({
            iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        let pickuppoint;
        const pickupAddress = document.getElementById("pickup-address");

        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const { latitude: userLat, longitude: userLng } = position.coords;
                pickuppoint = [userLat, userLng];
                const userMarker = L.marker([userLat, userLng], { icon: greenMarkerIcon }).addTo(map);

                axios.get("https://nominatim.openstreetmap.org/reverse?lat=" + userLat + "&lon=" + userLng + "&format=json")
                    .then(function (response) {
                        var address = response.data.display_name;
                        pickupAddress.textContent = "Pickup to: " + address;
                    })
                    .catch(function (error) {
                        // Handle errors here
                    });
                userMarker.bindPopup('You are here').openPopup();
            });
        }


        const redMarkerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        let dropoffPoint;
        let dropoffToda;
        let routeLayer;
        let dropoffTodaName = null;
        var fare; // Define global variables
        var convenienceFee;
        var distanceKilometers;
        var driverETA;
        const dropoffAddress = document.getElementById("dropoff-address");

        function addDropoffPoint(e) {
            let isInPolygon = false;


            polygonsLayer.eachLayer(function (layer) {
                if (isPointInPolygon(e.latlng, layer)) {
                    isInPolygon = true;
                    dropoffTodaName = layer.toda;
                    return false;
                }
            });

            if (isInPolygon) {
                if (dropoffPoint) {
                    map.removeLayer(dropoffPoint);
                }
                dropoffPoint = L.marker(e.latlng, { icon: redMarkerIcon }).addTo(map);
                axios.get("https://nominatim.openstreetmap.org/reverse?lat=" + e.latlng.lat + "&lon=" + e.latlng.lng + "&format=json")
            .then(function (response) {
                var address = response.data.display_name;
                dropoffAddress.textContent = "Drop-off to: " + address;
            })
            .catch(function (error) {
                // Handle errors here
            });
                dropoffPoint.bindPopup("Loading...").openPopup();
                calculateDistance(dropoffTodaName, e.latlng);
            } else {
                alert("Please click inside an available drop-off location.");
            }
        }

        function calculateDistance(dropoffTodaName, dropoffLatLng, isDropoff) {
            if (!pickuppoint || !dropoffTodaName) {
                return;
            }

            const pickupCoord = `${pickuppoint[1]},${pickuppoint[0]}`;
            const dropoffCoord = `${dropoffLatLng.lng},${dropoffLatLng.lat}`;
            const url = `https://router.project-osrm.org/route/v1/driving/${pickupCoord};${dropoffCoord}?overview=full&geometries=geojson`;

            axios.get(url)
                .then(response => {
                    const route = response.data.routes[0].geometry.coordinates;
                    const distanceMeters = response.data.routes[0].distance;
                    distanceKilometers = distanceMeters / 1000;

                    const matchingToda = todalocations.find(tl => tl.toda === dropoffTodaName);
                    if (matchingToda) {
                        const terminalLatLng = L.latLng(matchingToda.terminal.latlng.lat, matchingToda.terminal.latlng.lng);
                        const pickupLatLng = L.latLng(pickuppoint[0], pickuppoint[1]);
                        const terminalDistanceMeters = terminalLatLng.distanceTo(pickupLatLng);
                        const terminalDistanceKilometers = terminalDistanceMeters / 1000;

                        const currentTime = new Date();
                        const isNightTime = currentTime.getHours() >= 23 || currentTime.getHours() < 4;

                        const baseFare = 30;
                        const perKM = 10;
                        const nightDiff = 3;
                        const farePerPassenger = 5;
                        const fee = 20;


                        if (isNightTime) {
                            fare = Math.round((distanceKilometers - 2) * (perKM + nightDiff));
                        } else {
                            fare = Math.round((distanceKilometers - 2) * perKM);
                        }

                        if (distanceKilometers <= 2) {
                            fare = baseFare + ((passengerCount > 1 ? (passengerCount - 1) * farePerPassenger : 0));
                        } else {
                            fare = Math.round(baseFare + (distanceKilometers - 2) * perKM);
                            fare += (passengerCount > 2 ? (passengerCount - 1) * farePerPassenger : 0);
                        }


                        const convenienceFeePerKM = isNightTime ? (isDropoff ? (fee + nightDiff) : fee) : fee;
                        convenienceFee = Math.round((terminalDistanceKilometers) * convenienceFeePerKM);
                        driverETA = calculateETA(terminalDistanceMeters);

                        dropoffPoint.bindPopup(`<b>Drop-off Point</b> <br>Distance (km): ${distanceKilometers.toFixed(2)} <br>Fare: ₱${fare} <br>Convenience Fee: ₱${convenienceFee} <br>ETA: ${calculateETA(distanceMeters)} minutes <br>Driver ETA : ${calculateETA(terminalDistanceMeters)} minutes`).openPopup();
                    }

                    if (routeLayer) {
                        map.removeLayer(routeLayer);
                    }

                    const geojsonRoute = {
                        type: "Feature",
                        properties: {},
                        geometry: {
                            type: "LineString",
                            coordinates: route
                        }
                    };

                    routeLayer = L.geoJSON(geojsonRoute, {}).addTo(map);
                })
                .catch(error => {
                    console.error("Error calculating distance:", error);
                });
        }

        var passengerCount = 1;

        document.getElementById("passenger-dropdown").addEventListener("click", function (e) {
            if (e.target && e.target.nodeName == "A") {
                passengerCount = e.target.getAttribute("data-value");
                document.getElementById("passenger-display").innerText = passengerCount;

                if (dropoffPoint) {
                    recalculateFare();
                }
            }
        });

        function recalculateFare() {
            calculateDistance(dropoffTodaName, dropoffPoint.getLatLng());
        }

        function calculateETA(distanceMeters) {
            const averageSpeedKPH = 10;
            const timeHours = distanceMeters / 1000 / averageSpeedKPH;
            const timeMinutes = Math.round(timeHours * 60);
            return timeMinutes;
        }


        document.getElementById("confirm-booking-btn").addEventListener("click", function () {
    // Ensure that both pickuppoint and dropoffpoint exist
    if (pickuppoint && dropoffPoint) {
        // Use the confirm function to ask for confirmation
        const isConfirmed = confirm("Are you sure you want to confirm your booking?");
        
        if (isConfirmed) {
            const form = document.getElementById("booking-form");

                // Create hidden input fields and set their values
                const pickuppointInput = document.createElement("input");
                pickuppointInput.type = "hidden";
                pickuppointInput.name = "pickuppoint";
                pickuppointInput.value = `${pickuppoint}`;

                const dropoffpointInput = document.createElement("input");
                dropoffpointInput.type = "hidden";
                dropoffpointInput.name = "dropoffpoint";
                dropoffpointInput.value = `${dropoffPoint.getLatLng().lat},${dropoffPoint.getLatLng().lng}`;

                const todaInput = document.createElement("input");
                todaInput.type = "hidden";
                todaInput.name = "dropoffTodaName";
                todaInput.value = `${dropoffTodaName}`;

                const fareInput = document.createElement("input");
                fareInput.type = "hidden";
                fareInput.name = "fare";
                fareInput.value = `${fare}`;

                const convenienceFeeInput = document.createElement("input");
                convenienceFeeInput.type = "hidden";
                convenienceFeeInput.name = "convenienceFee";
                convenienceFeeInput.value = `${convenienceFee}`;

                const passengerCountInput = document.createElement("input");
                passengerCountInput.type = "hidden";
                passengerCountInput.name = "passengerCount";
                passengerCountInput.value = `${passengerCount}`;

                const driverETAInput = document.createElement("input");
                driverETAInput.type = "hidden";
                driverETAInput.name = "driverETA";
                driverETAInput.value = `${driverETA}`;

                const distanceKilometersInput = document.createElement("input");
                distanceKilometersInput.type = "hidden";
                distanceKilometersInput.name = "distanceKilometers";
                distanceKilometersInput.value = `${distanceKilometers}`;


                // Append the hidden input fields to the form
                form.appendChild(pickuppointInput);
                form.appendChild(dropoffpointInput);
                form.appendChild(todaInput);
                form.appendChild(fareInput);
                form.appendChild(convenienceFeeInput);
                form.appendChild(passengerCountInput);
                form.appendChild(driverETAInput);
                form.appendChild(distanceKilometersInput);

                // Submit the form
                form.submit();
        } else {
            // User clicked "Cancel" in the confirmation dialog
            alert("Booking not confirmed.");
        }
    } else {
        alert("Please select both pickup and dropoff points.");
    }
});



        displayPolygons();
        map.on('dblclick', addDropoffPoint);
    </script>
</body>

</html>