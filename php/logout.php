<?php
session_start(); // Resume the session

// Perform any additional session cleanup or logging out actions

session_destroy(); // Terminate the session

// Redirect to index.php which is outside the current folder
header("Location: ../index.php");
exit(); // Make sure to exit after redirection
?>