<?php
require("session.php");
require("db.php");

$searchTerm = "";
$category = "";

if(isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}
if(isset($_GET['category'])) {
    $category = $_GET['category'];
}

// Zapytanie SQL bazujące na wybranej kategorii i wprowadzonej nazwie
$sql = "SELECT nazwa, kategoria, opis FROM wydarzenia WHERE 1=1";

// Dodanie warunku na nazwę
if (!empty($searchTerm)) {
    $sql .= " AND nazwa LIKE '%$searchTerm%'";
}

// Dodanie warunku na kategorię
if (!empty($category)) {
    $sql .= " AND kategoria = '$category'";
}

$result = $conn->query($sql);

// Pobranie listy unikalnych kategorii
$sql_categories = "SELECT DISTINCT kategoria FROM wydarzenia";
$result_categories = $conn->query($sql_categories);

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
        
        <label for="category">Kategoria:</label>
        <select id="category" name="category">
            <option value="">Wybierz kategorię</option>
            <?php while ($row_category = $result_categories->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($row_category['kategoria']); ?>" <?php if ($category == $row_category['kategoria']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($row_category['kategoria']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        
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
$conn->close();
?>
