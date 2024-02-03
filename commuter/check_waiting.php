<?php
session_start();
include_once("../db/dbconn.php");

$commuterid = $_SESSION["commuterid"];

if ($conn === false) {
    die("Database connection failed.");
}

$query = "SELECT PlateNumber FROM booking WHERE commuterid = ? ORDER BY bookingdate DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $commuterid);
$stmt->execute();
$stmt->bind_result($PlateNumber);

if ($stmt->fetch()) {
    if ($PlateNumber !== null) {
        echo "notnull";
    }
} else {
    echo "No booking found for the user.";
}

$stmt->close();
$conn->close();
?>