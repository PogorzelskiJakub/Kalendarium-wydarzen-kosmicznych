<?php
require("db.php");

$sql = "SELECT nazwa, data FROM wydarzenia";
$result = $conn->query($sql);

$events = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = [
            'title' => $row['nazwa'],
            'start' => $row['data']
        ];
    }
}

echo json_encode($events);
?>
