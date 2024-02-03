<?php

require '../db/dbconn.php';

// Set the default timezone to UTC
date_default_timezone_set('UTC');

// Get the DateTime object for Manila timezone
$manilaTimezone = new DateTimeZone('Asia/Manila');
$dateTime = new DateTime('now', $manilaTimezone);
$manilaTime = $dateTime->format('Y-m-d H:i:s');

$liveID = 5; // Hardcoded as per your requirement
$latitude = isset($_POST['latitude']) ? (float) $_POST['latitude'] : null;
$longitude = isset($_POST['longitude']) ? (float) $_POST['longitude'] : null;

if ($latitude !== null && $longitude !== null) {
    // The SQL query with placeholders for the values
    $sql = "UPDATE live SET LiveLat = ?, LiveLng = ?, DateUpdated = ? WHERE liveID = ?";

    // Preparing the query
    $stmt = mysqli_prepare($conn, $sql);

    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, 'ddsi', $latitude, $longitude, $manilaTime, $liveID);
    $execute = mysqli_stmt_execute($stmt);

    if ($execute) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unable to update coordinates']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing coordinates']);
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>