<?php
require("session.php");
require("db.php");

$idWydarzenia = $_POST['idWydarzenia'];
$idUzytkownika = $_SESSION['id'];

// Dodanie wydarzenia do obserwowanych
$sql = "INSERT INTO obserwowane (idWydarzenia, idUzytkownika) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $idWydarzenia, $idUzytkownika);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}
?>
