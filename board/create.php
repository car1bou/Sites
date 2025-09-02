<?php
require 'db.php';
require_once 'auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $imagePath = null;

    // 이미지 저장 폴더 생성
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // 이미지 업로드 처리
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('img_', true) . '.' . $ext;
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    // DB 저장 (image_path 포함)
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, image_path, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $content, $imagePath, $user_id]);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글 작성</title>
    <link rel="stylesheet" href="forum.css">
    <style>
        /* 기존 스타일 유지 */
        .form-wrapper {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            font-size: 28px;
            margin-bottom: 24px;
            text-align: center;
        }

        .form-card input[type="text"],
        .form-card textarea,
        .form-card input[type="file"] {
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
        .form-card textarea:focus,
        .form-card input[type="file"]:focus {
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
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="form-card">
            <h1>글 작성</h1>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="제목" required>
                <textarea name="content" placeholder="내용" required></textarea>
                <input type="file" name="image" accept="image/*">
                <input type="submit" value="작성">
            </form>
            <a href="index.php" class="back-link">← 목록으로</a>
        </div>
    </div>
</body>
</html>
