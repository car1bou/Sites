<?php
require_once '/Users/kimhuijung/Sites/board/auth.php';
require 'db.php';
require_once 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

refreshLastActivity();

$stmt = $pdo->query("SELECT p.*, u.nickname FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
$posts = $stmt->fetchAll();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판</title>
    <link rel="stylesheet" href="forum.css"> <!-- 여기로 바꿈 -->
</head>
<body>
<div class="board-container">
    <div class="board-header">자유 게시판</div>

    <?php if ($user): ?>
        <p style="color:white;">어서 오세요, <a href="profile.php" style="color: #ffd;"><?= htmlspecialchars($user['nickname']) ?></a> |
            <a href="logout.php" style="color: #ffd;">로그아웃</a>
        </p>
        <p><a href="create.php" class="signup-link">➕ 글 작성</a></p>
    <?php endif; ?>

    <div class="board-card">
        <?php foreach ($posts as $post): ?>
            <div class="post-box" onclick="location.href='read.php?id=<?= $post['id'] ?>';">
            <div class="post-title"><?= htmlspecialchars($post['title']) ?></div>
            <div class="post-meta"><?= htmlspecialchars($post['nickname']) ?> · <?= $post['created_at'] ?></div>
        </div>
    <?php endforeach; ?>

    </div>
</div>
</body>
</html>
