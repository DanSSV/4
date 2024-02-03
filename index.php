<?php
session_start(); 

// Check if the "role" session variable is set.
if (isset($_SESSION["role"])) {
  $role = $_SESSION["role"];

  // Redirect the user to a page based on their role.
  header("Location: $role/$role.php");
  exit; // Make sure to exit the script after the redirection.
}

// If the "role" session variable is not set, the user stays on the current page.
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | TriSakay</title>
  <?php
  $imagePath = "..img/Logo_Nobg.png";
  ?>
  <link rel="icon" href="<?php echo $imagePath; ?>" type="image/png" />
  <?php
  include 'dependencies/dependencies.php';
  ?>
  <link rel="stylesheet" href="css/login.css" />
</head>

<body>
  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
    width="100%" height="100%" viewBox="0 0 1600 900" preserveAspectRatio="xMidYMax slice">
    <defs>
      <linearGradient id="bg">
        <stop offset="0%" style="stop-color:rgba(130, 158, 249, 0.06)"></stop>
        <stop offset="50%" style="stop-color:rgba(76, 190, 255, 0.6)"></stop>
        <stop offset="100%" style="stop-color:rgba(115, 209, 72, 0.2)"></stop>
      </linearGradient>
      <path id="wave" fill="url(#bg)" d="M-363.852,502.589c0,0,236.988-41.997,505.475,0
  s371.981,38.998,575.971,0s293.985-39.278,505.474,5.859s493.475,48.368,716.963-4.995v560.106H-363.852V502.589z" />
    </defs>
    <g>
      <use xlink:href='#wave' opacity=".3">
        <animateTransform attributeName="transform" attributeType="XML" type="translate" dur="10s" calcMode="spline"
          values="270 230; -334 180; 270 230" keyTimes="0; .5; 1" keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
          repeatCount="indefinite" />
      </use>
      <use xlink:href='#wave' opacity=".6">
        <animateTransform attributeName="transform" attributeType="XML" type="translate" dur="8s" calcMode="spline"
          values="-270 230;243 220;-270 230" keyTimes="0; .6; 1" keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
          repeatCount="indefinite" />
      </use>
      <use xlink:href='#wave' opacty=".9">
        <animateTransform attributeName="transform" attributeType="XML" type="translate" dur="6s" calcMode="spline"
          values="0 230;-140 200;0 230" keyTimes="0; .4; 1" keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
          repeatCount="indefinite" />
      </use>
    </g>
  </svg>
  <img src="img/nyenyeenye.png" alt="Baliwag City Logo" />
  <h1>Welcome</h1>
  <h2 style="color: white">Hi there! Sign in to continue.</h2>

  <div class="error-container">
    <?php
    if (isset($_GET['error'])) {
      if ($_GET['error'] === 'banned') {
        echo '<p class="error-message">Your account has been banned.<br> Please contact TriSakay for more information.</p>';
      } else {
        echo '<p class="error-message">Invalid username or password. Please try again.</p>';
      }
    }
    ?>
  </div>
  <div class="container2">
    <form action="login_back.php" method="POST">
      <div class="container d-flex justify-content-center">
        <div class="input-box">
          <div class="input-field">
            <input type="text" class="input" id="email" name="email" required autocomplete="off" />
            <label for="email"><i class="fa-solid fa-envelope fa-lg" style="color: #ffffff;"></i> Email</label>
          </div>
          <div class="input-field">
            <input type="password" class="input" id="password" name="password" required />
            <label for="password"><i class="fa-solid fa-key fa-lg" style="color: #ffffff;"></i> Password</label>
          </div>

          <div class="container1 d-flex justify-content-center mt-3">
            <p style="color: #f5f5f5">
              Forgot your
              <a href="forgot.php" style="color: #f5f5f5" onmouseover="this.style.color='#9ACD32'"
                onmouseout="this.style.color='#F5F5F5'">password?</a>
            </p>
          </div>

          <div class="container d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-default custom-btn">Sign in</button>
          </div>
        </div>
      </div>
    </form>

    <hr>

    <div class="container1 d-flex justify-content-center mt-3">
      <p style="color: #f5f5f5">
        Don’t have an account?
        <a href="signup.php" style="color: #f5f5f5" onmouseover="this.style.color='#9ACD32'"
          onmouseout="this.style.color='#F5F5F5'">Sign up</a>
      </p>
    </div>

  </div>

  <script>
    // JavaScript to show alert if login error exists
    <?php if (!empty($login_error)): ?>
      window.onload = function () {
        alert("<?php echo $login_error; ?>");
      };
    <?php endif; ?>
  </script>
</body>

</html>