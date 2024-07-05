<?php
require("session.php");
require("db.php");

$rola = "";
if(isset($_SESSION["login"])) {
    $idUzytkownika = $_SESSION["id"];
    $sql = "SELECT rola FROM uzytkownicy WHERE id = $idUzytkownika";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rola = $row['rola'];
    }
}

if ($rola !== 'admin') {
    header("Location: list.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["idWydarzenia"])) {
    $idWydarzenia = $_POST["idWydarzenia"];

    $sql_delete_event = "DELETE FROM wydarzenia WHERE id = $idWydarzenia";
    if ($conn->query($sql_delete_event) === TRUE) {
        $sql_delete_images = "DELETE FROM obrazy WHERE idWydarzenia = $idWydarzenia";
        $conn->query($sql_delete_images);
        header("Location: list.php");
        exit();
    } else {
        echo "Błąd podczas usuwania wydarzenia: " . $conn->error;
    }
}

$conn->close();
?>
