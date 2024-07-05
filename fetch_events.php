<?php
require("db.php");
session_start();

$idUzytkownika = $_SESSION['id'];

// Zapytanie do pobrania wydarzeń i informacji o obserwowaniach
$sql = "
    SELECT wydarzenia.id, wydarzenia.nazwa, wydarzenia.data, 
           IF(obserwowane.id IS NOT NULL, 1, 0) AS obserwowane
    FROM wydarzenia
    LEFT JOIN obserwowane ON wydarzenia.id = obserwowane.idWydarzenia AND obserwowane.idUzytkownika = $idUzytkownika";

$result = $conn->query($sql);

$events = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['nazwa'],
            'start' => $row['data'],
            'color' => $row['obserwowane'] ? '#ff0000' : '#3788d8' // Kolor czerwony dla obserwowanych, niebieski dla pozostałych
        ];
    }
}

echo json_encode($events);
?>
