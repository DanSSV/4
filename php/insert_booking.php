<?php
session_start();
include '../db/dbconn.php';

$data = json_decode(file_get_contents("php://input"), true);

$commuterid = $data['commuterid'];
$status = $data['status'];
$pickupPoint = $data['pickupPoint'];
$dropoffPoint = $data['dropoffPoint'];
$pickupAddress = $data['pickupAddress'];
$dropoffAddress = $data['dropoffAddress'];
$fare = $data['fare'];
$convenienceFee = $data['convenienceFee'];
$distance = $data['distance'];

$query = "INSERT INTO booking (commuterid, status, pickupPoint, dropoffPoint, pickupAddress, dropoffAddress, fare, convenienceFee, distance) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssssssidd", $commuterid, $status, $pickupPoint, $dropoffPoint, $pickupAddress, $dropoffAddress, $fare, $convenienceFee, $distance);
$result = $stmt->execute();

if ($result) {
    echo "Booking inserted successfully.";
} else {
    echo "Error: " . $stmt->error;
}
?>