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

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword !== $confirmPassword) {
        $errors[] = '비밀번호가 일치하지 않습니다.';
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt->execute([$hashedPassword, $user['id']])) {
            $_SESSION['password_changed'] = true;
            header('Location: profile.php');
            exit;
        } else {
            $errors[] = '비밀번호 변경에 실패했습니다.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>비밀번호 변경</title>
    <link rel="stylesheet" href="forum.css">
    <style>
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
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-card h1 {
            margin-top: 0;
            color: #2d3748;
            font-size: 26px;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-card input[type="password"] {
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

        .form-card input[type="password"]:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

        .form-card .error-list {
            color: red;
            margin-bottom: 16px;
        }

        .form-card .success-msg {
            color: green;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="form-card">
            <h1>비밀번호 변경</h1>

            <?php if ($success): ?>
                <p class="success-msg"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST">
                <input type="password" name="new_password" placeholder="새 비밀번호" required>
                <input type="password" name="confirm_password" placeholder="비밀번호 확인" required>
                <input type="submit" value="변경하기">
            </form>
            <a href="profile.php" class="back-link">← 프로필로 돌아가기</a>
        </div>
    </div>
</body>
</html>
