<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking | TriSakay</title>
    <?php
    $imagePath = "../img/Logo_Nobg.png";
    ?>
    <link rel="icon" href="<?php echo $imagePath; ?>" type="image/png" />
    <?php
    include '../dependencies/dependencies.php';
    ?>
    <link rel="stylesheet" href="../css/waiting.css">
</head>

<body>
    <?php
    include('../php/navbar_commuter.php');
    ?>
    <div class="loading">
        <p><b>Searching for drivers...</b></p>
        <p id="wait-time">Estimated wait time: <span id="countdown">5m 0s</span></p>
        <span class="load"><i class="fa-solid fa-spinner fa-spin-pulse fa-xl" style="color: #000000;"></i></span>
        <button id="cancel-button" type="submit" class="btn btn-default custom-btn red-text" style="display: none;">
            Cancel Booking
        </button>
    </div>

    <script type="text/javascript">
        var timer;
        var waitTime = 5; // Wait time in minutes
        var countdown = waitTime * 60; // Convert minutes to seconds

        function updateTimer() {
            var minutes = Math.floor(countdown / 60);
            var seconds = countdown % 60;
            var countdownText = minutes + "m " + seconds + "s";
            document.getElementById("countdown").textContent = countdownText;
            countdown--;

            if (countdown < 0) {
                clearInterval(timer);
                document.getElementById("wait-time").innerHTML = "No drivers are available in your area at this time.<br>Please try again later or cancel your request.";
                document.getElementById("cancel-button").style.display = "block";
            }
        }

        function startTimer() {
            timer = setInterval(updateTimer, 1000);
        }

        window.onload = function () {
            startTimer();
        }
        function checkBookingStatus() {
            // Make an AJAX request to check the bodynumber
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "check_waiting.php", true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;

                    if (response === "notnull") {
                        // Redirect the user to found.php
                        window.location.href = "found.php";
                    }
                }
            };

            xhr.send();
        }

        // Call the function initially
        checkBookingStatus();

        // Set up a recurring check every 3 seconds
        setInterval(checkBookingStatus, 3000);

    </script>

</body>

</html>