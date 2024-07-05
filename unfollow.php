<?php
require("session.php");
require("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["idWydarzenia"])) {
    $idWydarzenia = $_POST["idWydarzenia"];
    $idUzytkownika = $_SESSION["id"];

    $sql = "DELETE FROM obserwowane WHERE idWydarzenia = $idWydarzenia AND idUzytkownika = $idUzytkownika";
    if ($conn->query($sql) === TRUE) {
        header("Location: details.php?id=$idWydarzenia");
        exit();
    } else {
        echo "Błąd podczas usuwania z obserwowanych: " . $conn->error;
    }
}

$conn->close();
?>
