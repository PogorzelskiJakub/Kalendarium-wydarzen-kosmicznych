<?php
require("session.php");
require("db.php");

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

        // Przeniesienie pliku do katalogu zdjęcia
        if (move_uploaded_file($image["tmp_name"], $targetFilePath)) {
            // Zapisanie informacji o zdjęciu do bazy danych (tylko nazwa pliku)
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
            cursor: pointer;
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
        .upload-form {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }
        .modal-content img {
            width: 100%;
            height: auto;
        }
        .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
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
    
            <!-- Przyciski do dodawania i usuwania z obserwowanych -->
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
                    <button type="submit">Usuń z obserwowanych</button>
                </form>
                    <?php else: ?>
                    <form action="follow.php" method="POST">
                        <input type="hidden" name="idWydarzenia" value="<?php echo $id; ?>">
                        <button type="submit">Dodaj do obserwowanych</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>


            
            <!-- Formularz do przesyłania zdjęć -->
            <div class="upload-form">
                <h3>Udostępnij swoje zdjęcie</h3>
                <form action="details.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
                    <input type="file" name="image" required>
                    <button type="submit">Prześlij</button>
                </form>
            </div>
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
