<?php
include 'db/dbconn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $grade = $_POST['grade'];
    $section = $_POST['section'];
    $school = $_POST['school'];
    $surname = $_POST['surname'];
    $nickname = $_POST['nickname'];
    $middle = $_POST['middle'];
    $date = $_POST['date'];

    // Insert data into the 'test' table
    $sql = "INSERT INTO test (name, grade, section, school, surname, nickname, middle, date) 
            VALUES ('$name', '$grade', '$section', '$school', '$surname', '$nickname', '$middle', '$date')";

    if (mysqli_query($conn, $sql)) {
        echo "New record inserted successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>