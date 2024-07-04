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
        <!-- Treść dla zakładki "Oczekujące" -->
        <p>Lista propozycji oczekujących na akceptację.</p>
    </div>
    
    <div id="accepted" class="tab-content">
        <!-- Treść dla zakładki "Zaakceptowane" -->
        <p>Lista zaakceptowanych propozycji.</p>
    </div>
    
    <div id="rejected" class="tab-content">
        <!-- Treść dla zakładki "Odrzucone" -->
        <p>Lista odrzuconych propozycji.</p>
    </div>
    
    <div id="new" class="tab-content">
        <!-- Treść dla zakładki "Dodaj nową" -->
        <p>Formularz do dodawania nowych propozycji.</p>
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
