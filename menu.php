<?php
require("session.php");
require("db.php");

$rola = "";
if (isset($_SESSION["login"])) {
    $idUzytkownika = $_SESSION["id"];
    $sql = "SELECT rola FROM uzytkownicy WHERE id = $idUzytkownika";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rola = $row['rola'];
    }
}
?>
<nav class="menu">
    <ul>
        <li><a href="index.php">Kalendarz</a></li>
        <li><a href="list.php">Lista</a></li>
        <li><a href="waiting.php">Moje propozycje</a></li>
        <?php if (isset($_SESSION["login"])): ?>
            <?php if ($rola === "admin"): ?>
                <li><a href="admin.php">Panel Administratora</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Wyloguj</a></li>
        <?php else: ?>
            <li><a href="login.php">Zaloguj</a></li>
        <?php endif; ?>
    </ul>
</nav>
