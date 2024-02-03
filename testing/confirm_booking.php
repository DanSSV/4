<?php
include '../db/dbconn.php'; // Include your database connection script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the pickuppoint and dropoffpoint values from the form
    $pickuppoint = $_POST['pickuppoint'];
    $dropoffpoint = $_POST['dropoffpoint'];
    $dropoffTodaName = $_POST['dropoffTodaName'];
    $status = "pending";
    $fare = $_POST['fare'];
    $convenienceFee = $_POST['convenienceFee'];
    $passengerCount = $_POST['passengerCount'];
    $driverETA = $_POST['driverETA'];
    $distanceKilometers = $_POST['distanceKilometers'];

    // Retrieve the commuterid from the session
    session_start(); // Start the session if not already started
    $commuterid = $_SESSION["commuterid"];

    // Perform the database update (insert or update, depending on your use case)
    $sql = "INSERT INTO booking (pickuppoint, dropoffpoint, toda, status, fare, convenienceFee, passengerCount, driverETA, distance, commuterid, bookingdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $pickuppoint, $dropoffpoint, $dropoffTodaName, $status, $fare, $convenienceFee, $passengerCount, $driverETA, $distanceKilometers, $commuterid);

    if ($stmt->execute()) {
        // Booking successfully saved in the database
        header("Location: waiting.php");
        exit;
    } else {
        // Handle the case where the database update fails
        echo "Error: " . $conn->error;
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>