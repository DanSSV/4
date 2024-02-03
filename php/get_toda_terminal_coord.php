<?php
include '../db/dbconn.php';

$sql = "SELECT toda, terminal FROM todalocation";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $toda = $row["toda"];
        $terminal = $row["terminal"];
        $coordinates = explode(", ", $terminal);
        $data[] = [
            'toda' => $toda,
            'terminal' => [
                'lat' => floatval($coordinates[0]),
                'lng' => floatval($coordinates[1])
            ]
        ];
    }
    echo json_encode($data);
} else {
    echo "0 results";
}

$conn->close();
?>