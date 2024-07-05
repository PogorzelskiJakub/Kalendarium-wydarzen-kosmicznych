<?php
require("session.php");
require("db.php");

$rola = "";
if(isset($_SESSION["login"])) {
    $idUzytkownika = $_SESSION["id"];
    $sql = "SELECT rola FROM uzytkownicy WHERE id = $idUzytkownika";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rola = $row['rola'];
    }
}

// Sprawdzenie, czy przekazano identyfikator wydarzenia
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];

// Obsługa przesyłania zdjęć użytkownika
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $image = $_FILES["image"];
    $userId = $_SESSION["id"];

    // Sprawdzenie czy plik jest obrazem
    $check = getimagesize($image["tmp_name"]);
    if ($check !== false) {
        $fileName = uniqid() . "_" . basename($image["name"]);
        $uploadDir = "zdjęcia/";
        $targetFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($image["tmp_name"], $targetFilePath)) {
            $sql_insert = "INSERT INTO obrazy (obraz, idWydarzenia, idUzytkownika) VALUES ('$fileName', $id, $userId)";
            if ($conn->query($sql_insert) === TRUE) {
                header("Location: details.php?id=$id");
                exit();
            } else {
                echo "Błąd podczas zapisywania zdjęcia do bazy danych.";
            }
        } else {
            echo "Wystąpił problem podczas przesyłania zdjęcia.";
        }
    } else {
        echo "Przesłany plik nie jest obrazem.";
    }
}
$sql_event = "SELECT nazwa, kategoria, data, opis FROM wydarzenia WHERE id = $id";
$result_event = $conn->query($sql_event);

if ($result_event->num_rows > 0) {
    $event = $result_event->fetch_assoc();
    $sql_followers = "SELECT COUNT(*) AS count FROM obserwowane WHERE idWydarzenia = $id";
    $result_followers = $conn->query($sql_followers);
    $followers_count = $result_followers->fetch_assoc()['count'];
} else {
    // Przekierowanie, jeśli wydarzenie o podanym identyfikatorze nie istnieje
    header("Location: list.php");
    exit();
}

// pobieranie galerii zdjęć z informacją o użytkowniku
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <div class="container">
    <div class="sidebar">
        <h2>Informacje o wydarzeniu</h2>
        <div class="info">
            <p><strong>Nazwa:</strong> <?php echo htmlspecialchars($event['nazwa']); ?></p>
            <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($event['kategoria']); ?></p>
            <p><strong>Data:</strong> <?php echo isset($event['data']) ? htmlspecialchars($event['data']) : ''; ?></p>
            <p><strong>Opis:</strong> <?php echo htmlspecialchars($event['opis']); ?></p>
            <p class="followers"><strong>Ilość obserwujących:</strong> <?php echo htmlspecialchars($followers_count); ?></p>
        </div>
    
        <?php if (isset($_SESSION['id'])): ?>
        <?php
            $idUzytkownika = $_SESSION['id'];
            $sql_check_follow = "SELECT id FROM obserwowane WHERE idWydarzenia = $id AND idUzytkownika = $idUzytkownika";
            $result_check_follow = $conn->query($sql_check_follow);
            $is_following = $result_check_follow->num_rows > 0;
        ?>

        <?php if ($is_following): ?>
            <form action="unfollow.php" method="POST">
                <input type="hidden" name="idWydarzenia" value="<?php echo $id; ?>">
                <button type="submit" class="button">Usuń z obserwowanych</button>
            </form>
        <?php else: ?>
            <form action="follow.php" method="POST">
                <input type="hidden" name="idWydarzenia" value="<?php echo $id; ?>">
                <button type="submit" class="button">Dodaj do obserwowanych</button>
            </form>
        <?php endif; ?>
        <?php endif; ?>

        <div class="upload-form">
            <h3>Udostępnij swoje zdjęcie</h3>
            <form action="details.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
                <input type="file" name="image" required>
                <button type="submit" class="button">Prześlij</button>
            </form>
        </div>

        <?php if ($rola === 'admin'): ?>
            <form action="delete_event.php" method="POST">
                <input type="hidden" name="idWydarzenia" value="<?php echo $id; ?>">
                <button type="submit" class="button">Usuń to wydarzenie</button>
            </form>
            <a href="edit_event.php?id=<?php echo $id; ?>" class="button">Edytuj to wydarzenie</a>
        <?php endif; ?>
    </div>
        <div class="main-content">
            <h2>Galeria Zdjęć</h2>
            <div class="gallery">
                <?php while ($photo = $result_photos->fetch_assoc()): ?>
                    <div class="photo" onclick="openModal(this)">
                        <img src="zdjęcia/<?php echo htmlspecialchars($photo['obraz']); ?>" alt="Zdjęcie">
                        <div class="author">Dodane przez: <?php echo htmlspecialchars($photo['login']); ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Modal for displaying full-size image -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content" id="img01"></div>
    </div>

    <script>
        function openModal(element) {
            var modal = document.getElementById("myModal");
            var modalImg = document.getElementById("img01");
            modal.style.display = "block";
            modalImg.innerHTML = element.innerHTML;
        }

        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
