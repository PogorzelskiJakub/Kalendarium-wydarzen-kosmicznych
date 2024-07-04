<?php
require("session.php");
require("db.php");

// Obsługa dodawania nowej sugestii
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nazwa = $_POST['nazwa'];
    $kategoria = $_POST['kategoria'];
    $data = $_POST['data'];
    $opis = $_POST['opis'];
    $idUzytkownika = $_SESSION['id'];

    $sql = "INSERT INTO propozycje (nazwa, kategoria, data, opis, status, idUzytkownika)
            VALUES ('$nazwa', '$kategoria', '$data', '$opis', 'Waiting', '$idUzytkownika')";

    if ($conn->query($sql) === TRUE) {
        echo '<p style="color: green;">Nowa sugestia została dodana pomyślnie!</p>';
    } else {
        echo '<p style="color: red;">Błąd podczas dodawania sugestii: ' . $conn->error . '</p>';
    }
}

// Pobieranie propozycji z odpowiednimi statusami
function getProposalsByStatus($status) {
    global $conn;
    $idUzytkownika = $_SESSION['id'];
    $sql = "SELECT id, nazwa, kategoria, data, opis FROM propozycje WHERE status = '$status' AND idUzytkownika = $idUzytkownika";
    $result = $conn->query($sql);

    $proposals = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $proposals[] = $row;
        }
    }
    return $proposals;
}

// Pobieranie propozycji oczekujących
$pendingProposals = getProposalsByStatus('Waiting');

// Pobieranie propozycji zaakceptowanych
$acceptedProposals = getProposalsByStatus('Accepted');

// Pobieranie propozycji odrzuconych
$rejectedProposals = getProposalsByStatus('Rejected');
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje Propozycje</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .tabs {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            background-color: #f0f0f0;
            cursor: pointer;
            border: 1px solid #ccc;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
        }
        .tab.active {
            background-color: #fff;
            border-bottom: 1px solid #fff;
        }
        .tab-content {
            display: none;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 0 0 5px 5px;
        }
        .tab-content.active {
            display: block;
        }
        .add-new {
            margin-top: 20px;
        }
        form {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form label, form input, form textarea {
            display: block;
            margin-bottom: 10px;
        }
        form input, form textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        form button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <h2>Moje Propozycje</h2>
    
    <div class="tabs">
        <div class="tab active" data-tab="pending">Oczekujące</div>
        <div class="tab" data-tab="accepted">Zaakceptowane</div>
        <div class="tab" data-tab="rejected">Odrzucone</div>
        <div class="tab" data-tab="new">Dodaj nową</div>
    </div>
    
    <div id="pending" class="tab-content active">
        <!-- Lista propozycji oczekujących -->
        <table>
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Kategoria</th>
                    <th>Data</th>
                    <th>Opis</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingProposals as $proposal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($proposal['nazwa']); ?></td>
                        <td><?php echo htmlspecialchars($proposal['kategoria']); ?></td>
                        <td><?php echo isset($proposal['data']) ? htmlspecialchars($proposal['data']) : ''; ?></td>
                        <td><?php echo htmlspecialchars($proposal['opis']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div id="accepted" class="tab-content">
        <!-- Lista propozycji zaakceptowanych -->
        <table>
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Kategoria</th>
                    <th>Data</th>
                    <th>Opis</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($acceptedProposals as $proposal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($proposal['nazwa']); ?></td>
                        <td><?php echo htmlspecialchars($proposal['kategoria']); ?></td>
                        <td><?php echo isset($proposal['data']) ? htmlspecialchars($proposal['data']) : ''; ?></td>
                        <td><?php echo htmlspecialchars($proposal['opis']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div id="rejected" class="tab-content">
        <!-- Lista propozycji odrzuconych -->
        <table>
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Kategoria</th>
                    <th>Data</th>
                    <th>Opis</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rejectedProposals as $proposal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($proposal['nazwa']); ?></td>
                        <td><?php echo htmlspecialchars($proposal['kategoria']); ?></td>
                        <td><?php echo isset($proposal['data']) ? htmlspecialchars($proposal['data']) : ''; ?></td>
                        <td><?php echo htmlspecialchars($proposal['opis']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div id="new" class="tab-content">
        <!-- Formularz do dodawania nowej sugestii -->
        <h3>Dodaj nową sugestię wydarzenia</h3>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="nazwa">Nazwa wydarzenia:</label>
            <input type="text" id="nazwa" name="nazwa" required>
            
            <label for="kategoria">Kategoria:</label>
            <input type="text" id="kategoria" name="kategoria" required>
            
            <label for="data">Data (opcjonalnie):</label>
            <input type="date" id="data" name="data">
            
            <label for="opis">Opis:</label>
            <textarea id="opis" name="opis" rows="4" required></textarea>
            
            <button type="submit">Dodaj sugestię</button>
        </form>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Pobranie wszystkich elementów zakładek
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            // Ustawienie domyślnej aktywnej zakładki
            document.getElementById('pending').classList.add('active');
            
            // Obsługa kliknięcia na zakładkę
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Zresetowanie wszystkich zakładek
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(tc => tc.classList.remove('active'));
                    
                    // Aktywowanie klikniętej zakładki
                    this.classList.add('active');
                    const tabName = this.getAttribute('data-tab');
                    document.getElementById(tabName).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
