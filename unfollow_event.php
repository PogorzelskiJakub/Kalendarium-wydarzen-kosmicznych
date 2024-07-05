<?php
require("session.php");
require("db.php");

$idWydarzenia = $_POST['idWydarzenia'];
$idUzytkownika = $_SESSION['id'];

// Usunięcie wydarzenia z obserwowanych
$sql = "DELETE FROM obserwowane WHERE idWydarzenia = ? AND idUzytkownika = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $idWydarzenia, $idUzytkownika);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}
?>
