<?php
$host = 'localhost';
$dbname = 'board';
$username = 'root';
$password = 'Huijung852?'; //MySQL 비밀번호

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
