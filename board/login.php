<?php
require 'db.php';
require_once 'auth.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = trim($_POST['nickname']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE nickname = ?");
    $stmt->execute([$nickname]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nickname'] = $user['nickname'];
        header("Location: index.php");
        exit;
    } else {
        $error = "닉네임 또는 비밀번호가 잘못되었습니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>로그인</title>
    <link rel="stylesheet" href="login-form.css">
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">Devsign</h1>
        <h1 class="login-title">로그인</h1>
        <p class="login-subtitle">닉네임과 비밀번호를 입력하세요</p>
      </div>

      <?php if (isset($error)): ?>
        <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form class="login-form" method="post" action="login.php">
        <div class="input-group">
          <input type="text" name="nickname" class="login-input" placeholder=" " required>
          <label class="login-label">닉네임</label>
          <div class="input-border"></div>
        </div>

        <div class="input-group">
          <input type="password" name="password" class="login-input" placeholder=" " required>
          <label class="login-label">비밀번호</label>
          <div class="input-border"></div>
        </div>

        <button type="submit" class="login-button">로그인</button>
      </form>

      <div class="login-footer">
        계정이 없으신가요? <a href="register.php" class="signup-link">회원가입</a>
      </div>
    </div>
  </div>
</body>
</html>
