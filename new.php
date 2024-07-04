<?php
require("session.php");
require("db.php");

// Obsługa dodawania nowej sugestii
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nazwa = $_POST['nazwa'];
    $kategoria = $_POST['kategoria'];
    $data = $_POST['data'];
    $opis = $_POST['opis'];
    $idUzytkownika = $_SESSION['id'];

    $sql = "INSERT INTO propozycje (nazwa, kategoria, data, opis, status, idUzytkownika) 
            VALUES ('$nazwa', '$kategoria', '$data', '$opis', 'Waiting', '$idUzytkownika')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Sugestia została dodana.</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj Nową Sugestię</title>
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

    <div class="form-container">
        <h2>Dodaj Nową Sugestię</h2>
        <form action="new.php" method="POST">
            <label for="nazwa">Nazwa:</label>
            <input type="text" id="nazwa" name="nazwa" required>
            
            <label for="kategoria">Kategoria:</label>
            <select id="kategoria" name="kategoria" required>
                <option value="">Wybierz kategorię</option>
                <option value="Ziemia">Ziemia</option>
                <option value="Układ Słoneczny">Układ Słoneczny</option>
                <option value="Droga Mleczna">Droga Mleczna</option>
                <option value="Reszta Wszechświata">Reszta Wszechświata</option>
            </select>
            
            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required>
            
            <label for="opis">Opis:</label>
            <textarea id="opis" name="opis" rows="4" required></textarea>
            
            <button type="submit">Dodaj Sugestię</button>
        </form>
    </div>
</body>
</html>
