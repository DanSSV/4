<?php
require_once "db/dbconn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_email = $_POST["email"];
    $input_password = $_POST["password"];

    // Hash the input password using SHA-512
    $hashed_password = hash("sha512", $input_password);

    // Check in the commuter table
    $stmt = $conn->prepare("SELECT commuterid, password, status FROM commuter WHERE email = ?");
    $stmt->bind_param("s", $input_email);
    $stmt->execute();
    $stmt->store_result();

    $stmt->bind_result($commuterid, $password, $status);
    $stmt->fetch();

    if ($stmt->num_rows == 1 && $hashed_password == $password) {
        if ($status !== null) {
            // Account is banned
            header("Location: index.php?error=banned");
            exit();
        }

        session_id($commuterid);
        session_start();

        $_SESSION["commuterid"] = $commuterid;
        $_SESSION["role"] = "commuter"; // Set role session for commuter

        header("Location: commuter/commuter.php");
        exit();
    }

    // Check in the dispatcher table
    $stmt = $conn->prepare("SELECT dispatcherid, password, status FROM dispatcher WHERE email = ?");
    $stmt->bind_param("s", $input_email);
    $stmt->execute();
    $stmt->store_result();

    $stmt->bind_result($dispatcherid, $password, $status);
    $stmt->fetch();

    if ($stmt->num_rows == 1 && $hashed_password == $password) {
        if ($status !== null) {
            // Account is banned
            header("Location: index.php?error=banned");
            exit();
        }

        session_id($dispatcherid);
        session_start();

        $_SESSION["dispatcherid"] = $dispatcherid;
        $_SESSION["role"] = "dispatcher"; // Set role session for dispatcher

        header("Location: dispatcher/dispatcher.php");
        exit();
    }

    // Check in the driver table
    $stmt = $conn->prepare("SELECT driverid, password, status FROM driver WHERE email = ?");
    $stmt->bind_param("s", $input_email);
    $stmt->execute();
    $stmt->store_result();

    $stmt->bind_result($driverid, $password, $status);
    $stmt->fetch();

    if ($stmt->num_rows == 1 && $hashed_password == $password) {
        if ($status !== null) {
            // Account is banned
            header("Location: index.php?error=banned");
            exit();
        }

        session_id($driverid);
        session_start();

        $_SESSION["driverid"] = $driverid;
        $_SESSION["role"] = "driver"; // Set role session for driver

        header("Location: driver/driver.php");
        exit();
    }

    $stmt->close();

    // If not found in any table, redirect with an error
    header("Location: index.php?error=true");
    exit();
}

$conn->close();
?>