<nav class="navbar navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="commuter.php">
            <img src="../img/navbar.png" alt="Logo" height="50" width="auto" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
            style="background-color: #3b8875">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-center">
                <li class="nav-item">
                    <a class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'commuter.php') {
                        echo 'active';
                    } ?>" href="commuter.php"><i class="fa-solid fa-house fa-lg" style="color: #ffffff;"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'history.php') {
                        echo 'active';
                    } ?>" href="history.php"><i class="fa-solid fa-clock-rotate-left fa-lg"
                            style="color: #ffffff;"></i> History</a>
                </li>


                <li class="nav-item dropdown">
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

                                mysqli_stmt_close($stmt);
                            } else {
                                echo "<h1>User not found.</h1>";
                            }
                        } else {
                            echo "<h1>Error querying the database.</h1>";
                        }
                    } else {
                        echo "<h1>Error preparing the statement.</h1>";
                    }
                    ?>

                    <a class="nav-link dropdown-toggle <?php if (basename($_SERVER['PHP_SELF']) == 'manage.php') {
                        echo 'active';
                    } ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user fa-lg" style="color: #ffffff;"></i>
                        <?php echo "$firstname"; ?>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="manage.php"><i class="fa-solid fa-gear fa-lg"
                                    style="color: #ffffff;"></i> Manage Account</a></li>
                        <li><a class="dropdown-item" href="lost.php"><i class="fa-solid fa-magnifying-glass fa-lg"
                                    style="color: #ffffff;"></i> Report Driver and Recover Item</a></li>
                        <li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <a class="dropdown-item" href="../php/logout.php" style="color: red;"><i
                                    class="fa-solid fa-right-from-bracket fa-lg" style="color: #ffffff;"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>