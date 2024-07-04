<?php
require("session.php");
require("db.php");

// Inicjalizacja zmiennej na nazwę do wyszukiwania
$searchTerm = "";

// Sprawdzenie czy formularz wyszukiwania został wysłany
if(isset($_GET['search'])) {
    $searchTerm = $_GET['search'];

    // Pobierz wydarzenia z bazy danych na podstawie wyszukiwanej nazwy
    $sql = "SELECT nazwa, kategoria, opis FROM wydarzenia WHERE nazwa LIKE '%$searchTerm%'";
} else {
    // Pobierz wszystkie wydarzenia, jeśli formularz nie został wysłany
    $sql = "SELECT nazwa, kategoria, opis FROM wydarzenia";
}

$result = $conn->query($sql);

// Inicjalizacja zmiennej, która będzie przechowywać listę wydarzeń
$events = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Wydarzeń Kosmicznych</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        .search-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <h2>Lista Wydarzeń Kosmicznych</h2>
    
    <form action="list.php" method="GET" class="search-form">
        <label for="search">Wyszukaj wydarzenie:</label>
        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="submit">Szukaj</button>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Kategoria</th>
                <th>Opis</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo $event['nazwa']; ?></td>
                    <td><?php echo $event['kategoria']; ?></td>
                    <td><?php echo $event['opis']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

<?php
// Zamknij połączenie z bazą danych
$conn->close();
?>
