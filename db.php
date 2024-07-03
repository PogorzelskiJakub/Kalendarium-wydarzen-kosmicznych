<?php
$conn = new mysqli("localhost", "root", "", "Kalendarium");
if ($conn->connect_error) {
    exit("Connection failed: " . $conn->connect_error);
}
?>
