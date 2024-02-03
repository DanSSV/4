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
    <style>
        .custom-btn {
            display: none;
        }
    </style>

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

    <div class="address">
        <p class="pickup"><i class="fa-solid fa-location-crosshairs" style="color: #ffffff;"></i> Loading...</p>
        <!-- <p class="drop-off">Drop-off point: Please double-click on the map to add your drop-off point.</p> -->
        <!-- <p id="nearest-toda">Nearest TODA: Loading...</p>
        <p id="nearest-latlng">Nearest Coords: Loading...</p> -->
    </div>
    <div class="address2">
        <p class="drop-off"><i class="fa-solid fa-location-dot" style="color: #fcfcfc;"></i> Please double-click on the
            map to add your drop-off point.</p>
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
    <!-- <div class="huh">
        <p id="1"></p>
        <p id="2"></p>
        <p id="3"></p>
        <p id="4"></p>
        <p id="5"></p>
        <p id="6"></p>
        <p id="7"></p>
        <p id="8"></p>
    </div> -->

    <!-- Baliuag coords
    14.954252043265168, 120.90080869853092 -->

    <?php
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

    $borderQuery = "SELECT border FROM baliuag LIMIT 1"; // Adjust your query accordingly
    $borderResult = mysqli_query($conn, $borderQuery);

    if ($borderResult) {
        $borderData = mysqli_fetch_assoc($borderResult);
        $border = $borderData['border'];
    } else {
        // Handle the database error appropriately
        echo "Error fetching border data: " . mysqli_error($conn);
        exit();
    }
    ?>

    <?php

    // Include database connection
    include('../db/dbconn.php');

    // Fetch specific columns (BaseFare, PerKM, NightDiff, FarePerPassenger) from the farematrix table
    $fareColumnsQuery = "SELECT BaseFare, PerKM, NightDiff, FarePerPassenger FROM farematrix WHERE Status = 'active'";
    $fareColumnsResult = mysqli_query($conn, $fareColumnsQuery);

    $fareData = [];

    if ($fareColumnsResult) {
        $fareData = mysqli_fetch_assoc($fareColumnsResult);
    } else {
        // Handle the database error appropriately
        echo "Error fetching fare data: " . mysqli_error($conn);
        exit();
    }

    // Rest of your PHP code remains unchanged
    ?>

    <script src="../js/search.js"></script>
    <script>
        const map = L.map('map', {
            zoomControl: false,
            doubleClickZoom: false
        });

        let pickupPoint = null;
        let dropoffPoint = null;
        let watchId;
        let routeLayer = null;
        let nearestLatLng = null;
        let distance = null;
        let fare = null;
        let convenienceFee = null;
        let grand = null;

        const fareData = <?php echo json_encode($fareData); ?>;

        // Define constants using fetched fare data
        const BaseFare = fareData.BaseFare;
        const PerKM = fareData.PerKM;
        const NightDiff = fareData.NightDiff;
        const FarePerPassenger = fareData.FarePerPassenger;

        let passengerCount = 1;

        document.getElementById("passenger-dropdown").addEventListener("click", function (e) {
            if (e.target && e.target.nodeName == "A") {
                passengerCount = e.target.getAttribute("data-value");
                document.getElementById("passenger-display").innerText = passengerCount;

            }
        });

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

        function checkInsidePolygon(lat, lng) {
            const polygonData = <?php echo json_encode(json_decode($border, true)); ?>;
            const polygon = L.polygon(polygonData.latlngs);

            const point = L.latLng(lat, lng);
            const isInside = polygon.getBounds().contains(point);


            if (!isInside) {
                const alertContent = "It seems you're currently outside of Baliuag, where Trisakay's services are currently unavailable. To book a ride, please head back to Baliuag or check back later for updates on our expanded coverage area.\n\nIn the meantime, you can check out other modes of transportation available in your location.\n\nThank you for your understanding!";
                alert(alertContent);

                window.location.href = '../commuter/commuter.php';
            }
        }

        const updateMap = (position) => {
            const { latitude, longitude } = position.coords;

            if (!pickupPoint) {
                map.setView([latitude, longitude], 15);

                pickupPoint = L.marker([latitude, longitude], { icon: greenMarkerIcon }).addTo(map);
                pickupPoint.bindPopup('You are here').openPopup();
                checkInsidePolygon(pickupPoint.getLatLng().lat, pickupPoint.getLatLng().lng);
            } else {
                pickupPoint.setLatLng([latitude, longitude]);
            }

            // Call the function to find the nearest TODA using pickup point coordinates
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
                    addressWords.splice(-5);
                    // Join the remaining words back into a string
                    var shortenedAddress = addressWords.join(',');

                    // Update the p element with the shortened address
                    $('.pickup').text(shortenedAddress).prepend('<i class="fa-solid fa-location-crosshairs" style="color: #ffffff;"></i> ');
                },
                error: function (error) {
                    console.error('Error getting address: ' + error);
                }
            });

            findNearestTODA(pickupPoint.getLatLng().lat, pickupPoint.getLatLng().lng);

        };

        function isPointInsidePolygon(point, polygon) {
            const { lat, lng } = point;
            const polygonVertices = polygon.getLatLngs()[0];
            let intersectCount = 0;

            for (let i = 0; i < polygonVertices.length - 1; i++) {
                const vertex1 = polygonVertices[i];
                const vertex2 = polygonVertices[i + 1];

                if (
                    ((vertex1.lat <= lat && lat < vertex2.lat) || (vertex2.lat <= lat && lat < vertex1.lat)) &&
                    (lng < (vertex1.lng - vertex2.lng) * (lat - vertex2.lat) / (vertex1.lat - vertex2.lat) + vertex2.lng)
                ) {
                    intersectCount++;
                }
            }

            return intersectCount % 2 === 1;
        }

        function calculateShortestPath(pickupLatLng, dropoffLatLng) {
            const osrmEndpoint = 'https://router.project-osrm.org/route/v1/driving/';
            const coordinates = `${pickupLatLng.lng},${pickupLatLng.lat};${dropoffLatLng.lng},${dropoffLatLng.lat}`;

            axios.get(`${osrmEndpoint}${coordinates}`)
                .then(response => {
                    // Extract the route geometry from the response
                    const route = response.data.routes[0];
                    const routeGeometry = route.geometry;

                    // Display the route on the map
                    const decodedRoute = L.Polyline.fromEncoded(routeGeometry, { color: 'blue' }).addTo(map);
                    map.fitBounds(decodedRoute.getBounds());
                })
                .catch(error => {
                    console.error('Error fetching route:', error);
                    // Handle errors appropriately (e.g., display an error message to the user)
                });
        }
let pickupCoord = null;
let dropoffCoord = null;
let timeMinutes2 = null;
        const onMapDoubleClick = (event) => {
            const { lat, lng } = event.latlng;
            const polygonData = <?php echo json_encode(json_decode($border, true)); ?>;
            const polygon = L.polygon(polygonData.latlngs);

            // Check if the drop-off point is outside the Baliuag border using raycasting
            const isInsideBoundary = isPointInsidePolygon({ lat, lng }, polygon);
            if (!isInsideBoundary) {
                const alertContent = "Sorry, you can't add a drop-off point outside of Baliuag. To proceed, please select a drop-off location within Baliuag.";
                alert(alertContent);
                return; // Prevent setting the drop-off point outside the border
            }

            if (dropoffPoint) {
                map.removeLayer(dropoffPoint);
            }
            dropoffPoint = L.marker([lat, lng], { icon: redMarkerIcon }).addTo(map).bindPopup('Drop-off point').openPopup();

            // Remove the previous route layer, if exists
            if (routeLayer) {
                map.removeLayer(routeLayer);
            }

            document.getElementById("confirm-booking-btn").style.display = "block";
             pickupCoord = `${pickupPoint.getLatLng().lng},${pickupPoint.getLatLng().lat}`;
             dropoffCoord = `${dropoffPoint.getLatLng().lng},${dropoffPoint.getLatLng().lat}`;
            const url = `https://router.project-osrm.org/route/v1/driving/${pickupCoord};${dropoffCoord}?overview=full&geometries=geojson`;

            const pickupLatLng = pickupPoint.getLatLng();
            const dropoffLatLng = dropoffPoint.getLatLng();
            distance = pickupLatLng.distanceTo(dropoffLatLng) / 1000; // Convert meters to kilometers

            const distanceText = `Distance: ${distance.toFixed(2)} km`;


            const url2 = `https://router.project-osrm.org/route/v1/driving/${pickupCoord};${nearestLatLng}?overview=full&geometries=geojson`;
            const distance2 = pickupLatLng.distanceTo(nearestLatLng) / 1000;
            const distanceText2 = `TODA Distance: ${distance2.toFixed(2)} km`;

            const averageSpeedKPH = 10;
            const timeHours = distance / averageSpeedKPH;
            const timeMinutes = Math.round(timeHours * 60);
            const timeHours2 = distance2 / averageSpeedKPH;
             timeMinutes2 = Math.round(timeHours2 * 60);

            const currentTime = new Date();
            const isNightTime = currentTime.getHours() >= 23 || currentTime.getHours() < 4;

            const baseFare = 30;
            const perKM = 5;
            const nightDiff = 3;
            const farePerPassenger = 5;
            const fee = 20;


            if (isNightTime) {
                fare = Math.round((distance - 2) * (perKM + nightDiff));
            } else {
                fare = Math.round((distance - 2) * perKM);
            }

            if (distance <= 2) {
                fare = baseFare + ((passengerCount > 1 ? (passengerCount - 1) * farePerPassenger : 0));
            } else {
                fare = Math.round(baseFare + (distance - 2) * perKM);
                fare += (passengerCount > 2 ? (passengerCount - 1) * farePerPassenger : 0);
            }


            const convenienceFeePerKM = isNightTime ? fee + nightDiff : fee;
            convenienceFee = Math.round((distance2) * convenienceFeePerKM);
            grand = fare + convenienceFee;

            dropoffPoint.bindPopup(`<b>Drop-off</b><br><br>${distanceText}<br>Fare: ₱${grand}<br>ETA: ${timeMinutes} minutes`).openPopup();

        //     document.getElementById("1").textContent = `Pickup: ${pickupCoord}`;
        // document.getElementById("2").textContent = `Dropoff: ${dropoffCoord}`;
        // document.getElementById("3").textContent = `Nearest TODA: ${nearestTODA}`;
        // document.getElementById("4").textContent = `Fare: ${fare}`;
        // document.getElementById("5").textContent = `Convenience: ${convenienceFee}`;
        // document.getElementById("6").textContent = `Count: ${passengerCount}`;
        // document.getElementById("7").textContent = `Driver Distance: ${timeMinutes2}`;
        // document.getElementById("8").textContent = `Route Distance: ${distance}`;

            axios.get(url)
                .then(response => {
                    const route = response.data.routes[0].geometry.coordinates;

                    const geojsonRoute = {
                        type: "Feature",
                        properties: {},
                        geometry: {
                            type: "LineString",
                            coordinates: route
                        }
                    };

                    routeLayer = L.geoJSON(geojsonRoute, {
                        style: {

                            weight: 3
                        }
                    }).addTo(map);

                    // Fit the map view to the bounds of the route
                    const boundsWithPadding = routeLayer.getBounds().pad(0.1); // 20% padding

                    map.fitBounds(boundsWithPadding);
                })
                .catch(error => {
                    console.error('Error fetching route:', error);
                    // Handle errors appropriately (e.g., display an error message to the user)
                });

            $.ajax({
                url: 'https://nominatim.openstreetmap.org/reverse',
                method: 'GET',
                dataType: 'json',
                data: {
                    format: 'json',
                    lat: dropoffPoint.getLatLng().lat,
                    lon: dropoffPoint.getLatLng().lng,
                    zoom: 18,
                },
                success: function (data) {
                    var address = data.display_name;
                    // Split the address into words
                    var addressWords = address.split(',');
                    // Remove the last 3 words
                    addressWords.splice(-5);
                    // Join the remaining words back into a string
                    var shortenedAddress = addressWords.join(',');

                    // Update the p element with the shortened address
                    $('.drop-off').text(shortenedAddress).prepend('<i class="fa-solid fa-location-dot" style="color: #fcfcfc;"></i> ');
                },
                error: function (error) {
                    console.error('Error getting address: ' + error);
                }
            });
        };


        const options = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        };
 let nearestTODA = null;
        function findNearestTODA(userLat, userLng) {
            const todalocations = <?php echo $todalocationData; ?>;
           
            let minDistance = Infinity;


            todalocations.forEach((location) => {
                const { lat, lng } = location.terminal.latlng;
                const distance = L.latLng(userLat, userLng).distanceTo([lat, lng]);

                if (distance < minDistance) {
                    nearestTODA = location.toda;
                    minDistance = distance;
                    nearestLatLng = { lat, lng };
                }
            });

            // Update the "Nearest TODA" h5 element
            // document.getElementById("nearest-toda").textContent = `Nearest TODA: ${nearestTODA}`;

            // // Display the latitude and longitude of the nearest TODA
            // if (nearestLatLng) {
            //     document.getElementById("nearest-latlng").textContent = `Latitude: ${nearestLatLng.lat}, Longitude: ${nearestLatLng.lng}`;
            // }
        }


        const todalocations = <?php echo $todalocationData; ?>;
        const markersLayer = L.layerGroup().addTo(map);

        function displayMarkers() {
            todalocations.forEach((location) => {
                const { lat, lng } = location.terminal.latlng;
                const marker = L.marker([lat, lng]).addTo(markersLayer);
                marker.bindPopup(`${location.toda} Terminal`);
            });
        }

        // displayMarkers();

        watchId = navigator.geolocation.watchPosition(updateMap, null, options);
        map.on('dblclick', onMapDoubleClick);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        function displayBaliuagBorder() {
            const polygonData = <?php echo json_encode(json_decode($border, true)); ?>;
            const borderCoordinates = polygonData.latlngs;

            const border = L.polyline(borderCoordinates, {
                color: 'red',
                weight: 1,
                dashArray: '10, 5', // Define the dash pattern for the line (5 pixels dash, 8 pixels gap)
                opacity: 1, // Set opacity for faint appearance
                lineCap: 'round',
            }).addTo(map);
        }



        
        // // Use the defined constants in your JavaScript code
        // console.log('Base Fare:', BaseFare);
        // console.log('Per KM:', PerKM);
        // console.log('Night Diff:', NightDiff);
        // console.log('Fare Per Passenger:', FarePerPassenger);

        // document.getElementById("1").textContent = `Pickup: ${pickupCoord}`;
        // document.getElementById("2").textContent = `Dropoff: ${dropoffCoord}`;
        // document.getElementById("3").textContent = `Nearest TODA: ${nearestTODA}`;
        // document.getElementById("4").textContent = `Fare: ${fare}`;
        // document.getElementById("5").textContent = `Convenience: ${convenienceFee}`;
        // document.getElementById("6").textContent = `Count: ${passengerCount}`;
        // document.getElementById("7").textContent = `Driver Distance: ${nearestTODA}`;
        // document.getElementById("8").textContent = `Route Distance: ${distance}`;

        // Call the function to display the Baliuag border
        displayBaliuagBorder();

        document.getElementById("confirm-booking-btn").addEventListener("click", function () {
    // Ensure that both pickuppoint and dropoffpoint exist
    if (pickupCoord && dropoffCoord) {
        // Use the confirm function to ask for confirmation
        const isConfirmed = confirm("Are you sure you want to confirm your booking?");
        
        if (isConfirmed) {
            const form = document.getElementById("booking-form");

                // Create hidden input fields and set their values
                const pickuppointInput = document.createElement("input");
                pickuppointInput.type = "hidden";
                pickuppointInput.name = "pickuppoint";
                pickuppointInput.value = `${pickupCoord}`;

                const dropoffpointInput = document.createElement("input");
                dropoffpointInput.type = "hidden";
                dropoffpointInput.name = "dropoffpoint";
                dropoffpointInput.value = `${dropoffCoord}`;

                const todaInput = document.createElement("input");
                todaInput.type = "hidden";
                todaInput.name = "dropoffTodaName";
                todaInput.value = `${nearestTODA}`;

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
                driverETAInput.value = `${timeMinutes2}`;

                const distanceKilometersInput = document.createElement("input");
                distanceKilometersInput.type = "hidden";
                distanceKilometersInput.name = "distanceKilometers";
                distanceKilometersInput.value = `${distance}`;


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


    </script>



</body>

</html>