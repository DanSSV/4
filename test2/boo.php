<?php
// Include database connection
include '../db/dbconn.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the sent data from the AJAX request
    $nearestTODA = $_POST['nearestTODA'];
    $pickupCoord = $_POST['pickupCoord'];
    $dropoffCoord = $_POST['dropoffCoord'];
    $fare = $_POST['fare'];
    $convenienceFee = $_POST['convenienceFee'];
    $passengerCount = $_POST['passengerCount'];
    $timeMinutes2 = $_POST['timeMinutes2'];
    $distance = $_POST['distance'];

    session_start(); // Start the session if not already started
    $commuterid = $_SESSION["commuterid"];

    // Define other necessary variables as needed for insertion or processing

    // SQL query to insert the received data into the database table 'booking'
    $sql = "INSERT INTO booking (toda, pickuppoint, dropoffpoint, status, fare, convenienceFee, passengerCount, driverETA, bookingdate, commuterid, distance) 
            VALUES ('$nearestTODA', '$pickupCoord', '$dropoffCoord', 'pending', '$fare', '$convenienceFee', '$passengerCount', '$timeMinutes2', NOW(), $commuterid, $distance)";

    // Perform the SQL query and handle success or failure
    if (mysqli_query($conn, $sql)) {
        echo "New record inserted successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Handle cases where the request method is not POST
    echo "Invalid request method";
}
?>