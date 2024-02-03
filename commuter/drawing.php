<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Mamatay na</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .search {
            top: 10px;
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        #search-input {
            flex-grow: 1;
            padding: 8px;
            border: none;
            font-size: 16px;
        }

        #search-button {
            padding: 8px 16px;
            border: none;
            background-color: #3b8875;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="search">
        <input id="search-input" type="text" placeholder="Where are you heading to?">
        <button id="search-button"><i class="fa-solid fa-magnifying-glass-location fa-lg" style="color: #ffffff;"></i>
            Search</button>
    </div>
    <div id="map" style="width: 600px; height: 400px;"></div>
    <div id="coordinates"></div>

    <div class="input-field">
            <input type="text" class="input" id="email" name="email" required autocomplete="off" />
            <label for="Toda"><i class="fa-solid fa-envelope fa-lg" style="color: #ffffff;"></i> Toda</label>
          </div>
          <h1 hidden>Creator = Session named AdminID Select from admin where Firstname</h1>
          <!-- Admin Session ID get Admin Info -->
          <h1 hidden>Date & Time NOW()</h1>
          <!-- MYSQL statement NOW -->
          <h1 hidden>IpAdress</h1>
          <!-- PHP Command get IP -->
          <div class="container d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-default custom-btn">Save Border</button>
          </div>
          <div class="input-field">
            <input type="text" class="input" id="email" name="email" required autocomplete="off" />
            <label for="Toda"><i class="fa-solid fa-envelope fa-lg" style="color: #ffffff;"></i> Toda</label>
          </div>
          <div class="container d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-default custom-btn">Save Marker</button>
          </div>



    <script>
        var map = L.map('map').setView([14.95420936376014, 120.90083184274572], 15);
        var coordinatesArray = [];

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Initialize the draw control and add it to the map
        var drawControl = new L.Control.Draw({
            draw: {
                marker: true,
                circle: true,
                rectangle: true,
                polygon: true,
            },
            edit: {
                featureGroup: new L.FeatureGroup(),
            },
        });

        map.addControl(drawControl);

        // Handle drawing events
        map.on(L.Draw.Event.CREATED, function (event) {
            var layer = event.layer;
            map.addLayer(layer);

            if (layer instanceof L.Marker || layer instanceof L.CircleMarker) {
                // Handle markers
                var latlng = layer.getLatLng();
                var coordinates = { type: "marker", latlng: latlng };
                coordinatesArray.push(coordinates);
            } else if (layer instanceof L.Circle) {
                // Handle circles
                var latlng = layer.getLatLng();
                var radius = layer.getRadius();
                var coordinates = { type: "circle", latlng: latlng, radius: radius };
                coordinatesArray.push(coordinates);
            } else if (layer instanceof L.Rectangle) {
                // Handle rectangles
                var bounds = layer.getBounds();
                var coordinates = { type: "rectangle", bounds: bounds };
                coordinatesArray.push(coordinates);
            } else if (layer instanceof L.Polygon) {
                // Handle polygons
                var latlngs = layer.getLatLngs();
                var coordinates = { type: "polygon", latlngs: latlngs };
                coordinatesArray.push(coordinates);
            }

            updateCoordinates();
        });

        // Function to update the displayed coordinates
        function updateCoordinates() {
            var coordinatesDiv = document.getElementById('coordinates');
            coordinatesDiv.innerHTML = '<h2>Coordinates:</h2>';
            coordinatesArray.forEach(function (coordinates, index) {
                var coordinatesInfo = JSON.stringify(coordinates);
                coordinatesDiv.innerHTML += '<p>' + coordinatesInfo + '</p>';
            });
        }

        // Handle edit events
        map.on('editable:editing', function (event) {
            var layers = event.layers;
            layers.eachLayer(function (layer) {
                // Handle edited layers here
                // You can update the coordinatesArray when a shape is edited
            });
        });

        const searchInput = document.getElementById("search-input");
        const searchButton = document.getElementById("search-button");

        searchInput.addEventListener("keyup", function (event) {
            if (event.keyCode === 13) {
                searchButton.click();
            }
        });

        searchButton.addEventListener("click", function () {
            var searchValue = searchInput.value;
            axios.get("https://nominatim.openstreetmap.org/search?q=" + searchValue + "&format=json&limit=1")
                .then(function (response) {
                    var result = response.data[0];
                    map.setView([result.lat, result.lon], 16);
                })
                .catch(function (error) {
                    console.log(error);
                });
        });
    </script>

</body>

</html>