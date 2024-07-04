<?php
require("session.php");
require("db.php");

// Sprawdzenie czy użytkownik jest zalogowany i jaka jest jego rola
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
?>
<nav>
    <a href="index.php">Kalendarz</a>
    <a href="list.php">Lista</a>
        <a href="waiting.php">Moje propozycje</a>
        <?php if(isset($_SESSION["login"])): ?>
        <?php if($rola === "admin"): ?>
            <a href="admin.php">Panel Administratora</a>
        <?php endif; ?>
        <a href="logout.php">Wyloguj</a>
    <?php else: ?>
        <a href="login.php">Zaloguj</a>
    <?php endif; ?>
</nav>
