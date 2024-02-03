<?php
require_once("../db/dbconn.php"); // Include your existing database connection script

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $coordinatesArray = $_POST["coordinates"];
    $borders = json_encode($coordinatesArray); // Convert to JSON for storage

    $sql = "INSERT INTO route (borders) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $borders);
        if (mysqli_stmt_execute($stmt)) {
            echo "success"; // Return a success message
        } else {
            echo "Error saving coordinates: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>