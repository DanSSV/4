<?php
include('../php/session_commuter.php');
?>
<?php
include('../db/dbconn.php');

$commuterid = $_SESSION["commuterid"];

$query = "SELECT platenumber FROM booking WHERE commuterid = $commuterid AND status = 'accepted' ORDER BY bookingdate DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $platenumber = $row['platenumber'];

    // Calculate the average rating
    $ratingQuery = "SELECT AVG(rating) AS avgRating FROM booking WHERE platenumber = '$platenumber' AND status = 'completed'";
    $ratingResult = mysqli_query($conn, $ratingQuery);

    if ($ratingResult && mysqli_num_rows($ratingResult) > 0) {
        $ratingRow = mysqli_fetch_assoc($ratingResult);
        $avgRating = $ratingRow['avgRating'];
    } else {
        $avgRating = 0; // If there are no completed bookings, set the average rating to 0.
    }
}

$query = "SELECT name, toda, bodynumber FROM driver WHERE platenumber = '$platenumber'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $driverName = $row['name'];
    $toda = $row['toda'];
    $bodyNumber = $row['bodynumber'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php
    $imagePath = "../img/Logo_Nobg.png";
    ?>
    <link rel="icon" href="<?php echo $imagePath; ?>" type="image/png" />
    <?php
    include '../dependencies/dependencies.php';
    ?>
    <link rel="stylesheet" href="../css/found.css">
</head>

<body>
    <?php
    include('../php/navbar_commuter.php');
    ?>
    <div class="loading">
        <h5><b>We've found you a driver, and they're on their way to pick you up.</b></h5>
        <hr>
        <p>Driver:
            <?php echo $driverName; ?>
        </p>
        <p>Body Number:
            <?php echo $bodyNumber; ?>
        </p>
        <p>Toda:
            <?php echo $toda; ?>
        </p>
        <p>Plate Number:
            <?php echo $platenumber; ?>
        </p>
        <p>Rating:
            <?php echo number_format($avgRating, 1); ?> <i class="fa-solid fa-star" style="color: #033957;"></i>
        </p>
        <button id="cancel-button" type="submit" class="btn btn-default custom-btn">
            Scan QR
        </button>
    </div>
    <script>
        document.getElementById("cancel-button").addEventListener("click", function () {
            window.location.href = "scan.php";
        });
    </script>

</body>

</html>