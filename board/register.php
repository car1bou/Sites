<?php
require 'db.php';

$error = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $nickname = trim($_POST['nickname']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "올바른 이메일 형식이 아닙니다.";
    } elseif (strlen($nickname) > 20) {
        $error = "닉네임은 20자 이하로 입력해주세요.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT email, nickname FROM users WHERE email = ? OR nickname = ?");
            $stmt->execute([$email, $nickname]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                if ($existing['email'] === $email) {
                    $error = "이미 사용 중인 이메일입니다.";
                } else {
                    $error = "이미 사용 중인 닉네임입니다.";
                }
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (email, password, nickname) VALUES (?, ?, ?)");
                $stmt->execute([$email, $hashedPassword, $nickname]);
                header("Location: login.php?signup=success");
                exit;
            }
        } catch (PDOException $e) {
            $error = "회원가입 실패: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
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
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-card h1 {
            margin-top: 0;
            color: #2d3748;
            font-size: 28px;
            margin-bottom: 24px;
            text-align: center;
        }

        .form-card input[type="email"],
        .form-card input[type="password"],
        .form-card input[type="text"] {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 16px;
            background: #f7fafc;
            outline: none;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .form-card input:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-card input[type="submit"] {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 14px;
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

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="form-card">
            <h1>회원가입</h1>
            <?php if (!empty($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="post">
                <input type="email" name="email" placeholder="이메일" required>
                <input type="password" name="password" placeholder="비밀번호" required>
                <input type="text" name="nickname" placeholder="닉네임 (20자 이하)" required>
                <input type="submit" value="가입하기">
            </form>
            <a href="login.php" class="back-link">← 로그인하러 가기</a>
        </div>
    </div>
</body>
</html>
