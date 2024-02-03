<?php
require_once '../db/dbconn.php'; // Adjust the path to where your Database class is located

$response = array();

// Assuming liveID is passed via POST request
$liveID = isset($_POST['liveID']) ? $_POST['liveID'] : '';

if ($liveID) {
    // Prepare your query
    $sql = "SELECT LiveLat, LiveLng, DateUpdated FROM live WHERE liveID = ?";

    // Prepare and execute the query
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $liveID);
    mysqli_stmt_execute($stmt);

    // Get results
    mysqli_stmt_bind_result($stmt, $LiveLat, $LiveLng, $DateUpdated);
    mysqli_stmt_fetch($stmt);

    if ($LiveLat !== null && $LiveLng !== null && $DateUpdated !== null) {
        $response['LiveLat'] = $LiveLat;
        $response['LiveLng'] = $LiveLng;
        $response['DateUpdated'] = $DateUpdated;
    } else {
        $response['error'] = "No data found for liveID: $liveID";
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    $response['error'] = "Missing liveID parameter";
}

// Close the connection
mysqli_close($conn);

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>