<?php
require("session.php");
require("db.php");

$sql = "SELECT id, nazwa, kategoria, opis, idObrazu FROM wydarzenia";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Wydarzeń</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>
    <?php include('menu.php'); ?>

    <h1>Lista Wydarzeń</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Kategoria</th>
                <th>Opis</th>
                <th>ID Obrazu</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nazwa']); ?></td>
                        <td><?php echo htmlspecialchars($row['kategoria']); ?></td>
                        <td><?php echo htmlspecialchars($row['opis']); ?></td>
                        <td><?php echo htmlspecialchars($row['idObrazu']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Brak wydarzeń do wyświetlenia</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
