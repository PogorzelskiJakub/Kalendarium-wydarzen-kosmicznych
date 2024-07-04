<?php
require("session.php");
require("db.php");

$idWydarzenia = $_REQUEST["idWydarzenia"];
$idUzytkownika = $_SESSION["id"];

$sql = "SELECT id FROM obserwowane WHERE idWydarzenia = $idWydarzenia AND idUzytkownika = $idUzytkownika";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $id = $result->fetch_object()->id;
    $sql = "DELETE FROM obserwowane WHERE id = $id";
} else {
    $sql = "INSERT INTO obserwowane (idWydarzenia, idUzytkownika) VALUES ($idWydarzenia, $idUzytkownika)";
}

if ($conn->query($sql) !== TRUE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
} else {
    echo "sukces";
}
?>
