<?php
require("session.php");
require("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["idWydarzenia"])) {
    $idWydarzenia = $_POST["idWydarzenia"];
    $idUzytkownika = $_SESSION["id"];

    $sql = "INSERT INTO obserwowane (idWydarzenia, idUzytkownika) VALUES ($idWydarzenia, $idUzytkownika)";
    if ($conn->query($sql) === TRUE) {
        header("Location: details.php?id=$idWydarzenia");
        exit();
    } else {
        echo "Błąd podczas dodawania do obserwowanych: " . $conn->error;
    }
}

$conn->close();
?>
