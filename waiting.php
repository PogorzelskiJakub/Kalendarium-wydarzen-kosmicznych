<?php
require("session.php");
require("db.php");

// Obsługa usuwania propozycji
if(isset($_POST['delete_proposal'])) {
    $id_proposal_to_delete = $_POST['delete_proposal'];
    $sql_delete = "DELETE FROM propozycje WHERE id = $id_proposal_to_delete";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<p>Sugestia została usunięta.</p>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Obsługa edycji propozycji
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_proposal'])) {
    $id = $_POST['edit_proposal'];
    $nazwa = $_POST['nazwa'];
    $kategoria = $_POST['kategoria'];
    $data = $_POST['data'];
    $opis = $_POST['opis'];

    $sql_update = "UPDATE propozycje SET nazwa = '$nazwa', kategoria = '$kategoria', data = '$data', opis = '$opis' WHERE id = $id";

    if ($conn->query($sql_update) === TRUE) {
        echo "<p>Sugestia została zaktualizowana.</p>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Pobieranie oczekujących propozycji
$idUzytkownika = $_SESSION['id'];
$sql = "SELECT id, nazwa, kategoria, data, opis FROM propozycje WHERE status = 'Waiting' AND idUzytkownika = $idUzytkownika";
$result = $conn->query($sql);

$proposals = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $proposals[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oczekujące Sugestie</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .menu {
            display: flex;
            justify-content: space-between;
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 20px;
        }
        .menu a {
            text-decoration: none;
            color: black;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .menu a:hover {
            background-color: #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .form-container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container label, .form-container input, .form-container textarea, .form-container select {
            display: block;
            margin-bottom: 10px;
        }
        .form-container input, .form-container textarea, .form-container select {
            width: 100%;
            padding: 8px;
            font-size: 1em;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php include 'menu.php'; ?>
    <div class="menu">
        <div>
            <a href="waiting.php">Oczekujące</a>
            <a href="accepted.php">Zaakceptowane</a>
            <a href="denied.php">Odrzucone</a>
            <a href="new.php">Dodaj nową</a>
        </div>
    </div>

    <h2>Oczekujące Sugestie</h2>

    <table>
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Kategoria</th>
                <th>Data</th>
                <th>Opis</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proposals as $proposal): ?>
                <tr>
                    <td><?php echo htmlspecialchars($proposal['nazwa']); ?></td>
                    <td><?php echo htmlspecialchars($proposal['kategoria']); ?></td>
                    <td><?php echo isset($proposal['data']) ? htmlspecialchars($proposal['data']) : ''; ?></td>
                    <td><?php echo htmlspecialchars($proposal['opis']); ?></td>
                    <td>
                        <!-- Formularz usuwania propozycji -->
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="delete_proposal" value="<?php echo $proposal['id']; ?>">
                            <button type="submit">Usuń</button>
                        </form>

                        <!-- Formularz edycji propozycji -->
                        <button onclick="showEditForm(<?php echo $proposal['id']; ?>)">Edytuj</button>

                        <!-- Formularz ukryty do edycji propozycji -->
                        <div id="edit_form_<?php echo $proposal['id']; ?>" class="form-container" style="display: none;">
                            <h3>Edytuj sugestię</h3>
                            <form method="POST">
                                <input type="hidden" name="edit_proposal" value="<?php echo $proposal['id']; ?>">
                                
                                <label for="nazwa_<?php echo $proposal['id']; ?>">Nazwa:</label>
                                <input type="text" id="nazwa_<?php echo $proposal['id']; ?>" name="nazwa" value="<?php echo htmlspecialchars($proposal['nazwa']); ?>" required>
                                
                                <label for="kategoria_<?php echo $proposal['id']; ?>">Kategoria:</label>
                                <select id="kategoria_<?php echo $proposal['id']; ?>" name="kategoria" required>
                                    <option value="Ziemia" <?php if ($proposal['kategoria'] == 'Ziemia') echo 'selected'; ?>>Ziemia</option>
                                    <option value="Układ Słoneczny" <?php if ($proposal['kategoria'] == 'Układ Słoneczny') echo 'selected'; ?>>Układ Słoneczny</option>
                                    <option value="Droga Mleczna" <?php if ($proposal['kategoria'] == 'Droga Mleczna') echo 'selected'; ?>>Droga Mleczna</option>
                                    <option value="Reszta Wszechświata" <?php if ($proposal['kategoria'] == 'Reszta Wszechświata') echo 'selected'; ?>>Reszta Wszechświata</option>
                                </select>
                                
                                <label for="data_<?php echo $proposal['id']; ?>">Data:</label>
                                <input type="date" id="data_<?php echo $proposal['id']; ?>" name="data" value="<?php echo htmlspecialchars($proposal['data']); ?>" required>
                                
                                <label for="opis_<?php echo $proposal['id']; ?>">Opis:</label>
                                <textarea id="opis_<?php echo $proposal['id']; ?>" name="opis" rows="4" required><?php echo htmlspecialchars($proposal['opis']); ?></textarea>
                                
                                <button type="submit">Zapisz zmiany</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function showEditForm(id) {
            var formId = 'edit_form_' + id;
            var form = document.getElementById(formId);
            form.style.display = 'block';
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
