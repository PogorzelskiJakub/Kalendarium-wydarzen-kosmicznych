<?php
require("session.php");
require("db.php");
?>
<nav>
    <a href="index.php">Kalendarz</a>
    <a href="list.php">Lista</a>
    <?php if(isset($_SESSION["login"])): ?>
        <a href="waiting.php">Moje propozycje</a>
        <a href="logout.php">Wyloguj</a>
    <?php else: ?>
        <a href="login.php">Zaloguj</a>
    <?php endif; ?>
</nav>
