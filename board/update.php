<?php
require 'db.php';
require_once 'auth.php';

if (!isLoggedIn()) {
    logout();
    header('Location: login.php');
    exit;
}

refreshLastActivity();
requireLogin();

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post || $post['user_id'] != $_SESSION['user_id']) {
    die("권한이 없습니다.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $content, $id]);
        header("Location: read.php?id=$id");
        exit;
    } else {
        $error = "제목과 내용을 입력하세요.";
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글 수정</title>
    <link rel="stylesheet" href="forum.css">
    <style>
        .form-wrapper {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .form-card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-card h1 {
            margin-top: 0;
            color: #2d3748;
            font-size: 26px;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-card input[type="text"],
        .form-card textarea {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 16px;
            background: #f7fafc;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-card input[type="text"]:focus,
        .form-card textarea:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-card textarea {
            height: 200px;
            resize: vertical;
        }

        .form-card input[type="submit"] {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 16px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-card input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .form-card .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .form-card .back-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .form-card .error-msg {
            color: red;
            margin-bottom: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="form-card">
            <h1>글 수정</h1>

            <?php if (isset($error)): ?>
                <p class="error-msg"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post">
                <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
                <textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
                <input type="submit" value="저장">
            </form>

            <a href="read.php?id=<?= $id ?>" class="back-link">← 글로 돌아가기</a>
        </div>
    </div>
</body>
</html>
