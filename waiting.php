<?php
require("session.php");
require("db.php");

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
        <div>
            <a href="logout.php">Wyloguj</a>
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proposals as $proposal): ?>
                <tr>
                    <td><?php echo htmlspecialchars($proposal['nazwa']); ?></td>
                    <td><?php echo htmlspecialchars($proposal['kategoria']); ?></td>
                    <td><?php echo isset($proposal['data']) ? htmlspecialchars($proposal['data']) : ''; ?></td>
                    <td><?php echo htmlspecialchars($proposal['opis']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
