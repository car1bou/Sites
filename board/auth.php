<?php
//auth.php

//세션 시작 (한 번만)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//세션 타임아웃 설정 (초 단위)
define('SESSION_TIMEOUT', 30 * 60); // 예: 30분


//마지막 활동 시간 갱신
 
function refreshLastActivity(): void {
    $_SESSION['last_activity'] = time();
}

//세션 바이탈체크
 
function isSessionAlive(): bool {
    if (! isset($_SESSION['last_activity'])) {
        //타임스탬프
        refreshLastActivity();
        return true;
    }
    return (time() - $_SESSION['last_activity']) < SESSION_TIMEOUT;
}


//로그인 상태 확인 (세션 유효성 포함)

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && isSessionAlive();
}

// 현재 로그인 사용자 정보 가져오기
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id'       => $_SESSION['user_id'],
            'nickname' => $_SESSION['nickname']
        ];
    }
    return null;
}

//로그아웃
function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function isLoggedOut(): bool {
    return ! isLoggedIn();
}

function requireLogin(): void {
    if (! isLoggedIn()) {
        logout();
        header('Location: login.php');
        exit;
    }
    // 세션 연장
    refreshLastActivity();
}
