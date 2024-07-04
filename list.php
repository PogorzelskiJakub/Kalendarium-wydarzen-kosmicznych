<?php
require("session.php");
require("db.php");

$sql = "SELECT nazwa, kategoria, opis FROM wydarzenia";
$result = $conn->query($sql);

//zmienna która będzie przechowywać listę wydarzeń
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
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <h2>Lista Wydarzeń Kosmicznych</h2>
    
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
