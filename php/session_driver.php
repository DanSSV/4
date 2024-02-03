<?php
session_start();

if ($_SESSION["role"] !== "driver") {
    header("Location: ../index.php");
    exit; // Make sure to exit the script after the redirection
}

// Rest of your code for authenticated commuters
?>