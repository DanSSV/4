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

    // Set the default timezone to Manila
    date_default_timezone_set('Asia/Manila');

    // Get the current date and time in Manila timezone
    $manilaDateTime = date('Y-m-d H:i:s');

    // Prepare the SQL statement with placeholders
    $sql = "INSERT INTO booking (toda, pickuppoint, dropoffpoint, status, fare, convenienceFee, passengerCount, driverETA, bookingdate, commuterid, distance) 
            VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ssssssssdd", $nearestTODA, $pickupCoord, $dropoffCoord, $fare, $convenienceFee, $passengerCount, $timeMinutes2, $manilaDateTime, $commuterid, $distance);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to waiting.php upon successful insertion
            header("Location: waiting.php");
            exit();
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Handle cases where the request method is not POST
    echo "Invalid request method";
}
?>
