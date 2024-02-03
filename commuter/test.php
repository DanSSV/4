<!DOCTYPE html>
<html>

<head>
  <title>Map of Baliwag, Bulacan</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>

<body>
  <div id="map" style="width: 600px; height: 400px;"></div>
  <h1 id="pickupInfo">Pickup Point: </h1>
  <h1 id="dropOffInfo">Drop-off Point: </h1>
  <button id="pickupConfirmButton" disabled>Confirm Pickup Point</button>
  <button id="dropOffConfirmButton" disabled>Confirm Drop-off Point</button>
  <script>
    var map = L.map('map', {
      doubleClickZoom: false
    }).setView([14.9547, 120.8960], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var baliwagBorder = {
      "type": "Feature",
      "properties": {},
      "geometry": {
        "type": "Polygon",
        "coordinates": [
          [
            [120.884636, 14.975004],
            [120.886553, 14.968013],
            [120.895216, 14.964246],
            [120.902219, 14.965920],
            [120.908055, 14.959083],
            [120.913277, 14.962588],
            [120.917792, 14.959260],
            [120.923443, 14.963070],
            [120.921748, 14.966362],
            [120.926912, 14.969162],
            [120.921655, 14.973515],
            [120.916575, 14.971384],
            [120.913404, 14.974052],
            [120.908298, 14.973532],
            [120.905630, 14.976379],
            [120.894470, 14.975419],
            [120.888803, 14.979179],
            [120.884636, 14.975004]
          ]
        ]
      }
    };

    

    var baliwagShape = L.geoJSON(baliwagBorder, {
      style: {
        color: 'green',
        weight: 0.1
      }
    }).addTo(map);

    var pickupMarker;
    var dropOffMarker;
    var pickupInfo = document.getElementById('pickupInfo');
    var dropOffInfo = document.getElementById('dropOffInfo');
    var pickupConfirmButton = document.getElementById('pickupConfirmButton');
    var dropOffConfirmButton = document.getElementById('dropOffConfirmButton');

    map.on('dblclick', function (e) {
      if (!pickupMarker) {
        // Create a pickup point marker
        if (isMarkerInsidePolygon(e.latlng, baliwagBorder.geometry.coordinates[0])) {
          pickupMarker = L.marker(e.latlng, { draggable: true }).addTo(map);
          pickupMarker.bindPopup("Pickup Point");
          pickupConfirmButton.disabled = false;
        } else {
          alert('You can only select a pickup point within the specified route.');
        }
      } else if (!pickupMarker.isConfirmed) {
        // Confirm the pickup point
        pickupMarker.dragging.disable();
        pickupMarker.isConfirmed = true;
        pickupConfirmButton.disabled = true;

        // Enable drop-off marker
        dropOffConfirmButton.disabled = false;
      } else if (!dropOffMarker && pickupMarker.isConfirmed) {
        // Create a drop-off point marker after confirming pickup
        if (isMarkerInsidePolygon(e.latlng, baliwagBorder.geometry.coordinates[0])) {
          dropOffMarker = L.marker(e.latlng, { draggable: true }).addTo(map);
          dropOffMarker.bindPopup("Drop-off Point");
          dropOffConfirmButton.disabled = false;
        } else {
          alert('You can only select a drop-off point within the specified route.');
        }
      } else if (!dropOffMarker.isConfirmed && dropOffMarker) {
        // Confirm the drop-off point
        dropOffMarker.dragging.disable();
        dropOffMarker.isConfirmed = true;
        dropOffConfirmButton.disabled = true;
      }
    });

    pickupConfirmButton.addEventListener('click', function () {
      if (pickupMarker) {
        pickupMarker.dragging.disable();
        pickupMarker.isConfirmed = true;
        pickupConfirmButton.disabled = true;
        dropOffConfirmButton.disabled = false;
      }
    });

    dropOffConfirmButton.addEventListener('click', function () {
      if (dropOffMarker) {
        dropOffMarker.dragging.disable();
        dropOffMarker.isConfirmed = true;
        dropOffConfirmButton.disabled = true;
      }
    });

    function isMarkerInsidePolygon(latlng, polygonCoordinates) {
      var x = latlng.lng, y = latlng.lat;

      var inside = false;
      for (var i = 0, j = polygonCoordinates.length - 1; i < polygonCoordinates.length; j = i++) {
        var xi = polygonCoordinates[i][0], yi = polygonCoordinates[i][1];
        var xj = polygonCoordinates[j][0], yj = polygonCoordinates[j][1];

        var intersect = ((yi > y) != (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) inside = !inside;
      }

      return inside;
    }
  </script>
</body>

</html>