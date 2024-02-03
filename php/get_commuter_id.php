<?php
session_start();
if (isset($_SESSION["commuterid"])) {
    echo $_SESSION["commuterid"];
} else {
    echo "Error: Commuter ID not found.";
}
?>
