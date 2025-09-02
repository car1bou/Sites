<?php
require 'db.php';
require_once 'auth.php';

if (!isLoggedIn()) {
    logout();
    header('Location: login.php');
    exit;
}

refreshLastActivity();
$user = getCurrentUser();

// 로그인한 사용자의 글만 조회
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute(['uid' => $user['id']]);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($user['nickname']) ?>님의 프로필</title>
    <link rel="stylesheet" href="forum.css">
    <style>
        .profile-sub {
            margin-bottom: 24px;
            color: white;
        }

        .profile-actions {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .profile-actions a {
            margin-right: 10px;
            padding: 8px 14px;
            background: white;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #2d3748;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .profile-actions a:hover {
            background: #edf2f7;
        }
    </style>
</head>
<body>
<div class="board-container">
    <div class="board-header"><?= htmlspecialchars($user['nickname']) ?>님의 글 목록</div>
    <p class="profile-sub">작성한 게시글을 확인하고 수정할 수 있습니다.</p>

    <div class="profile-actions">
        <a href="modify.php">비밀번호 변경</a>
        <a href="index.php">← 메인으로</a>
    </div>

    <div class="board-card">
        <?php if (count($posts) === 0): ?>
            <p style="text-align:center; margin: 40px 0;">작성한 글이 없습니다.</p>
        <?php else: ?>
            <table class="board-table">
                <thead>
                <tr>
                    <th>번호</th>
                    <th>제목</th>
                    <th>작성일</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($posts as $i => $post): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><a href="read.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></td>
                        <td><?= htmlspecialchars($post['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

