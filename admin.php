<?php
require("session.php");

require("db.php");

// Obsługa zmiany statusu propozycji
if (isset($_POST['change_status'])) {
    $id_propozycji = $_POST['id_propozycji'];
    $status = $_POST['status'];

    // Aktualizacja statusu propozycji w tabeli propozycje
    $sql_update = "UPDATE propozycje SET status = '$status' WHERE id = $id_propozycji";
    if ($conn->query($sql_update) === TRUE) {
        echo "Status propozycji został zaktualizowany.";
    } else {
        echo "Błąd aktualizacji statusu propozycji: " . $conn->error;
    }

    // Jeśli propozycja została zaakceptowana, skopiuj ją do tabeli wydarzenia
    if ($status === 'Accepted') {
        $sql_select_propozycja = "SELECT * FROM propozycje WHERE id = $id_propozycji";
        $result_propozycja = $conn->query($sql_select_propozycja);

        if ($result_propozycja->num_rows > 0) {
            $row_propozycja = $result_propozycja->fetch_assoc();
            $nazwa = $row_propozycja['nazwa'];
            $kategoria = $row_propozycja['kategoria'];
            $data = $row_propozycja['data'];
            $opis = $row_propozycja['opis'];

            // Wstawienie propozycji do tabeli wydarzenia
            $sql_insert_wydarzenie = "INSERT INTO wydarzenia (nazwa, kategoria, data, opis) VALUES ('$nazwa', '$kategoria', '$data', '$opis')";
            if ($conn->query($sql_insert_wydarzenie) === TRUE) {
                echo "Propozycja została dodana jako wydarzenie.";
            } else {
                echo "Błąd dodawania propozycji jako wydarzenia: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <div class="container">
        <h2>Panel Administratora</h2>
        
        <h3>Oczekujące propozycje</h3>
        <table>
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Kategoria</th>
                    <th>Data</th>
                    <th>Opis</th>
                    <th>Status</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_propozycje = "SELECT * FROM propozycje WHERE status = 'Waiting'";
                $result_propozycje = $conn->query($sql_propozycje);

                if ($result_propozycje->num_rows > 0) {
                    while ($row = $result_propozycje->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nazwa']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['kategoria']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['data']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['opis']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>";
                        echo "<form method='POST'>";
                        echo "<input type='hidden' name='id_propozycji' value='" . $row['id'] . "'>";
                        echo "<select name='status'>";
                        echo "<option value='Waiting'>Oczekujące</option>";
                        echo "<option value='Accepted'>Zaakceptowane</option>";
                        echo "<option value='Denied'>Odrzucone</option>";
                        echo "</select>";
                        echo "<button type='submit' name='change_status'>Zmień status</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Brak oczekujących propozycji.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
