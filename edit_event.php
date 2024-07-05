<?php
require("session.php");
require("db.php");

// Sprawdzenie czy użytkownik jest zalogowany i jaka jest jego rola
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

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];

// Pobranie istniejących danych wydarzenia
$sql_event = "SELECT nazwa, kategoria, data, opis FROM wydarzenia WHERE id = $id";
$result_event = $conn->query($sql_event);

if ($result_event->num_rows > 0) {
    $event = $result_event->fetch_assoc();
} else {
    // Przekierowanie, jeśli wydarzenie o podanym identyfikatorze nie istnieje
    header("Location: list.php");
    exit();
}

// Obsługa przesyłania formularza edycji
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $nazwa = $_POST["nazwa"];
    $kategoria = $_POST["kategoria"];
    $data = $_POST["data"];
    $opis = $_POST["opis"];

    $sql_update = "UPDATE wydarzenia SET nazwa = '$nazwa', kategoria = '$kategoria', data = '$data', opis = '$opis' WHERE id = $id";

    if ($conn->query($sql_update) === TRUE) {
        // Przekierowanie po zaktualizowaniu
        header("Location: details.php?id=$id");
        exit();
    } else {
        echo "Błąd podczas aktualizacji wydarzenia: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Wydarzenie</title>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <h2>Edytuj Wydarzenie</h2>
    
    <form action="edit_event.php?id=<?php echo $id; ?>" method="POST">
        <label for="nazwa">Nazwa:</label>
        <input type="text" id="nazwa" name="nazwa" value="<?php echo htmlspecialchars($event['nazwa']); ?>" required><br><br>
        
        <label for="kategoria">Kategoria:</label>
        <input type="text" id="kategoria" name="kategoria" value="<?php echo htmlspecialchars($event['kategoria']); ?>" required><br><br>
        
        <label for="data">Data:</label>
        <input type="date" id="data" name="data" value="<?php echo isset($event['data']) ? $event['data'] : ''; ?>" required><br><br>
        
        <label for="opis">Opis:</label><br>
        <textarea id="opis" name="opis" rows="4" cols="50"><?php echo htmlspecialchars($event['opis']); ?></textarea><br><br>
        
        <button type="submit" name="update">Zapisz zmiany</button>
    </form>
</body>
</html>
