<?php
include('../php/session_commuter.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TriSakay | Commuter</title>
  <?php
  $imagePath = "../img/Logo_Nobg.png";
  ?>
  <link rel="icon" href="<?php echo $imagePath; ?>" type="image/png" />
  <?php
  include '../dependencies/dependencies.php';
  ?>
  <link rel="stylesheet" href="../css/commuter.css">
  


</head>

<body>

  <?php
  include('../php/navbar_commuter.php');
  ?>
  <?php
  $imagePath = "../img/Logo_Nobg.png";

  ?>
  <div class="center-image">
    <img src="<?php echo $imagePath; ?>" alt="Image Description">
  </div>
  <?php
  $imagePath = "../img/Logo_Nobg.png";
  include('../db/dbconn.php');

  $commuterId = $_SESSION["commuterid"];
  $query = "SELECT firstname FROM commuter WHERE commuterid = ?";
  $stmt = mysqli_prepare($conn, $query);

  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $commuterId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
      if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $firstname = $row['firstname'];
        echo "<h5>We hope you enjoy your next ride with <strong>TriSakay</strong>, $firstname.</h5>";
      } else {
        echo "<h5>User not found.</h5>";
      }
    } else {
      echo "<h5>Error querying the database.</h5>";
    }

    mysqli_stmt_close($stmt);
  } else {
    echo "<h5>Error preparing the statement.</h5>";
  }

  mysqli_close($conn);
  ?>







  <div class="container">
    <div class="row">
      <div class="col-md-6 mb-2">
        <button type="submit" class="btn btn-default custom-btn" onclick="redirectToMyLocation()">
          <i class="fa-solid fa-street-view fa-lg" style="color: #ffffff;"></i> My Location
        </button>
      </div>
      <div class="col-md-6 mb-2">
        <button type="submit" class="btn btn-default custom-btn" onclick="redirectToBookings()">
          <i class="fa-solid fa-map-location-dot fa-lg" style="color: #ffffff;"></i> Book Now
        </button>
      </div>
      <div class="col-md-6 mb-2">
        <button type="submit" class="btn btn-default custom-btn" onclick="redirectToBookings()">
          <i class="fa-solid fa-qrcode fa-lg" style="color: #ffffff;"></i> Scan QR
        </button>
      </div>
    </div>
  </div>

  <script src="../js/button.js"></script>
</body>

</html>