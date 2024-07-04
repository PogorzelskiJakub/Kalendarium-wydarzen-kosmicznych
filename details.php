<?php
require("session.php");
require("db.php");

// Sprawdzenie, czy przekazano identyfikator wydarzenia
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];

// Zapytanie SQL, aby pobrać szczegółowe informacje o wydarzeniu
$sql_event = "SELECT nazwa, kategoria, data, opis FROM wydarzenia WHERE id = $id";
$result_event = $conn->query($sql_event);

if ($result_event->num_rows > 0) {
    $event = $result_event->fetch_assoc();

    // Zapytanie SQL, aby pobrać ilość obserwujących to wydarzenie
    $sql_followers = "SELECT COUNT(*) AS count FROM obserwowane WHERE idWydarzenia = $id";
    $result_followers = $conn->query($sql_followers);
    $followers_count = $result_followers->fetch_assoc()['count'];
} else {
    // Przekierowanie, jeśli wydarzenie o podanym identyfikatorze nie istnieje
    header("Location: list.php");
    exit();
}

// Zapytanie SQL, aby pobrać galerię zdjęć z informacją o użytkowniku
$sql_photos = "SELECT obrazy.id, obrazy.obraz, uzytkownicy.login 
               FROM obrazy 
               LEFT JOIN uzytkownicy ON obrazy.idUzytkownika = uzytkownicy.id 
               WHERE obrazy.idWydarzenia = $id";
$result_photos = $conn->query($sql_photos);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szczegóły Wydarzenia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            width: 80%;
            display: flex;
            justify-content: space-between;
        }
        .sidebar {
            flex-basis: 30%;
        }
        .main-content {
            flex-basis: 60%;
        }
        .menu {
            width: 100%;
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .photo {
            position: relative;
        }
        .photo img {
            width: 100%;
            height: auto;
        }
        .photo .author {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 5px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <div class="container">
        <div class="sidebar">
            <h2>Informacje o wydarzeniu</h2>
            <p><strong>Nazwa:</strong> <?php echo htmlspecialchars($event['nazwa']); ?></p>
            <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($event['kategoria']); ?></p>
            <p><strong>Data:</strong> <?php echo isset($event['data']) ? htmlspecialchars($event['data']) : ''; ?></p>
            <p><strong>Opis:</strong> <?php echo htmlspecialchars($event['opis']); ?></p>
            <p><strong>Ilość obserwujących:</strong> <?php echo htmlspecialchars($followers_count); ?></p>
            
        </div>
        <div class="main-content">
            <h2>Galeria Zdjęć</h2>
            <div class="gallery">
                <?php while ($photo = $result_photos->fetch_assoc()): ?>
                    <div class="photo">
                        <img src="<?php echo htmlspecialchars($photo['obraz']); ?>" alt="Zdjęcie">
                        <div class="author">Dodane przez: <?php echo htmlspecialchars($photo['login']); ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
