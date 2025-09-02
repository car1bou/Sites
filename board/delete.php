<?php
require 'db.php';
require_once 'auth.php';
requireLogin();
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post || $post['user_id'] != $_SESSION['user_id']) {
    die("권한이 없습니다.");
}
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php");
?>