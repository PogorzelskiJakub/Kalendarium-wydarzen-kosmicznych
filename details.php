<?php
require("session.php");
require("db.php");

$idWydarzenia = $_GET['id'];
$idUzytkownika = $_SESSION['id'];

// Pobranie szczegółów wydarzenia
$sql = "SELECT * FROM wydarzenia WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idWydarzenia);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

// Sprawdzenie, czy wydarzenie jest obserwowane
$sql_followed = "SELECT id FROM obserwowane WHERE idWydarzenia = ? AND idUzytkownika = ?";
$stmt_followed = $conn->prepare($sql_followed);
$stmt_followed->bind_param("ii", $idWydarzenia, $idUzytkownika);
$stmt_followed->execute();
$isFollowed = $stmt_followed->get_result()->num_rows > 0;

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szczegóły Wydarzenia</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
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
        .container {
            display: flex;
            justify-content: space-between;
        }
        .details {
            flex: 1;
            margin-right: 20px;
        }
        .gallery {
            flex: 2;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }
        .image-grid img {
            width: 100%;
            height: auto;
            cursor: pointer;
        }
        .image-grid .caption {
            text-align: center;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="container">
        <div class="details">
            <h2><?php echo htmlspecialchars($event['nazwa']); ?></h2>
            <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($event['kategoria']); ?></p>
            <p><strong>Data:</strong> <?php echo htmlspecialchars($event['data']); ?></p>
            <p><strong>Opis:</strong> <?php echo nl2br(htmlspecialchars($event['opis'])); ?></p>
            <p><strong>Obserwujących:</strong> 
                <?php
                $sql_followers = "SELECT COUNT(*) as count FROM obserwowane WHERE idWydarzenia = ?";
                $stmt_followers = $conn->prepare($sql_followers);
                $stmt_followers->bind_param("i", $idWydarzenia);
                $stmt_followers->execute();
                $result_followers = $stmt_followers->get_result()->fetch_assoc();
                echo $result_followers['count'];
                ?>
            </p>
            <button id="follow-btn" data-followed="<?php echo $isFollowed ? '1' : '0'; ?>">
                <?php echo $isFollowed ? 'Usuń z obserwowanych' : 'Dodaj do obserwowanych'; ?>
            </button>

            <h3>Dodaj nowe zdjęcie:</h3>
            <form action="upload_image.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="idWydarzenia" value="<?php echo $idWydarzenia; ?>">
                <input type="file" name="image" accept="image/*" required>
                <button type="submit">Prześlij</button>
            </form>
        </div>

        <div class="gallery">
            <h3>Galeria zdjęć</h3>
            <div class="image-grid">
                <?php
                $sql_images = "SELECT obraz, idUzytkownika FROM obrazy WHERE idWydarzenia = ?";
                $stmt_images = $conn->prepare($sql_images);
                $stmt_images->bind_param("i", $idWydarzenia);
                $stmt_images->execute();
                $result_images = $stmt_images->get_result();
                while($image = $result_images->fetch_assoc()) {
                    echo '<div>';
                    echo '<img src="zdjecia/' . htmlspecialchars($image['obraz']) . '" alt="Zdjęcie" onclick="toggleImage(this)">';
                    echo '<div class="caption">dodane przez: ' . htmlspecialchars($image['idUzytkownika']) . '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('follow-btn').addEventListener('click', function() {
            var btn = this;
            var followed = btn.getAttribute('data-followed') === '1';
            var action = followed ? 'unfollow_event.php' : 'follow_event.php';
            var idWydarzenia = <?php echo $idWydarzenia; ?>;
            
            fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'idWydarzenia=' + idWydarzenia
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    btn.textContent = followed ? 'Dodaj do obserwowanych' : 'Usuń z obserwowanych';
                    btn.setAttribute('data-followed', followed ? '0' : '1');
                } else {
                    alert('Wystąpił błąd. Spróbuj ponownie później.');
                }
            });
        });

        function toggleImage(img) {
            if (img.classList.contains('fullscreen')) {
                img.classList.remove('fullscreen');
                document.body.style.overflow = 'auto';
            } else {
                img.classList.add('fullscreen');
                document.body.style.overflow = 'hidden';
            }
        }
    </script>
    <style>
        .fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .fullscreen img {
            max-width: 90%;
            max-height: 90%;
        }
    </style>
</body>
</html>
