<?php
require 'db.php';
require_once 'auth.php';

if (!isLoggedIn()) {
    logout();
    header('Location: login.php');
    exit;
}

refreshLastActivity();

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, u.nickname FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) {
    die("게시글이 없습니다.");
}

$user = getCurrentUser();
$is_owner = $user && $user['id'] == $post['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']) && $is_owner) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="forum.css">
    <style>
        .read-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 40px 48px;
        }

        .read-card {
            background: white;
            border-radius: 16px;
            padding: 40px 48px;
            width: 100%;
            height: 90vh;
            box-sizing: border-box;
            overflow-y: auto;
        }

        .read-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .read-meta {
            font-size: 14px;
            color: #718096;
            margin-bottom: 20px;
        }

        .read-content {
            font-size: 16px;
            color: #2d3748;
            white-space: pre-wrap;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .read-image-left {
            margin-bottom: 30px;
        }

        .read-image-left img {
            max-width: 60%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            display: block;
        }

        .read-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .read-actions form,
        .read-actions a {
            display: inline-block;
        }

        .button {
            padding: 10px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .button:hover {
            background: #5a67d8;
        }

        .button.delete {
            background: #e53e3e;
        }

        .button.delete:hover {
            background: #c53030;
        }

        .back-link {
            display: inline-block;
            margin-top: 10px;
            color: #667eea;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="read-container">
        <div class="read-card">
            <div class="read-title"><?= htmlspecialchars($post['title']) ?></div>
            <div class="read-meta">
                작성자: <?= htmlspecialchars($post['nickname']) ?> | 작성일: <?= $post['created_at'] ?>
            </div>

            <div class="read-content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>

            <?php if (!empty($post['image_path'])): ?>
                <div class="read-image-left">
                    <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="첨부 이미지">
                </div>
            <?php endif; ?>

            <?php if ($is_owner): ?>
                <div class="read-actions">
                    <a href="update.php?id=<?= $id ?>" class="button">수정</a>
                    <form method="post" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                        <input type="submit" name="delete" value="삭제" class="button delete">
                    </form>
                </div>
            <?php endif; ?>

            <a href="index.php" class="back-link">← 목록으로</a>
        </div>
    </div>
</body>
</html>
