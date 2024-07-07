<?php
require("session.php");
require("db.php");

// Inicjalizacja zmiennych na nazwę do wyszukiwania, kategorię do filtrowania i sortowanie
$searchTerm = "";
$category = "";
$order = "data"; // Domyślne sortowanie według daty
$showFollowed = false;

// Obsługa formularza wyszukiwania
if(isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}
if(isset($_GET['category'])) {
    $category = $_GET['category'];
}
if(isset($_GET['followed'])) {
    $showFollowed = $_GET['followed'] == 'true';
}

// Zapytanie SQL bazujące na wybranej kategorii i wprowadzonej nazwie, sortowanie po dacie
$sql = "SELECT id, nazwa, kategoria, data, opis FROM wydarzenia WHERE 1=1";

// Dodanie warunku na nazwę
if (!empty($searchTerm)) {
    $sql .= " AND nazwa LIKE '%$searchTerm%'";
}

// Dodanie warunku na kategorię
if (!empty($category)) {
    $sql .= " AND kategoria = '$category'";
}

// Dodanie warunku na obserwowane
if ($showFollowed) {
    $userId = $_SESSION["id"];
    $sql .= " AND id IN (SELECT idWydarzenia FROM obserwowane WHERE idUzytkownika = $userId)";
}

// Dodanie sortowania po dacie
$sql .= " ORDER BY $order";

$result = $conn->query($sql);

// Pobranie listy unikalnych kategorii
$sql_categories = "SELECT DISTINCT kategoria FROM wydarzenia";
$result_categories = $conn->query($sql_categories);

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
    <link rel="stylesheet" href="styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var rows = document.querySelectorAll("tr[data-href]");
            rows.forEach(row => {
                row.addEventListener("click", function(e) {
                    if (!e.target.classList.contains('follow')) {
                        window.location.href = this.dataset.href;
                    }
                });
            });
        });
    </script>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <div class="container">
        <h2>Lista Wydarzeń Kosmicznych</h2>
        
        <form action="list.php" method="GET" class="search-form">
            <div>
                <label for="search">Wyszukaj wydarzenie:</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
            </div>
            
            <div>
                <label for="category">Kategoria:</label>
                <select id="category" name="category">
                    <option value="">Wybierz kategorię</option>
                    <?php while ($row_category = $result_categories->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row_category['kategoria']); ?>" <?php if ($category == $row_category['kategoria']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($row_category['kategoria']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label for="followed">Pokaż tylko obserwowane:</label>
                <input type="checkbox" id="followed" name="followed" value="true" <?php if ($showFollowed) echo "checked"; ?>>
            </div>
            
            <button type="submit">Szukaj</button>
        </form>
        
        <table>
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Kategoria</th>
                    <th>Data</th>
                    <th>Opis</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr data-href="details.php?id=<?php echo $event['id']; ?>">
                        <td><?php echo htmlspecialchars($event['nazwa']); ?></td>
                        <td><?php echo htmlspecialchars($event['kategoria']); ?></td>
                        <td><?php echo isset($event['data']) ? htmlspecialchars($event['data']) : ''; ?></td>
                        <td><?php echo htmlspecialchars($event['opis']); ?></td>
                        <td>
                            <?php
                            $id = $event["id"];
                            $idUzytkownika = $_SESSION["id"];
                            $sql = "SELECT id FROM obserwowane WHERE idWydarzenia = $id AND idUzytkownika = $idUzytkownika";
                            $added = $conn->query($sql)->num_rows > 0;
                            $text = $added ? "Usuń z obserwowanych" : "Dodaj do obserwowanych";
                            echo "<p class='follow' data-wydarzenie='$id'>$text</p>";
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>

<?php
$conn->close();
?>
