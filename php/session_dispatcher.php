<?php
session_start();

if ($_SESSION["role"] !== "dispatcher") {
    header("Location: ../index.php");
    exit; // Make sure to exit the script after the redirection
}

// Rest of your code for authenticated dispatcher
?>