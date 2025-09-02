<?php
// auth.php

// 1) 세션 시작 (한 번만)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) 세션 타임아웃 설정 (초 단위)
define('SESSION_TIMEOUT', 30 * 60); // 예: 30분

/**
 * 마지막 활동 시간 갱신
 */
function refreshLastActivity(): void {
    $_SESSION['last_activity'] = time();
}

/**
 * 세션이 유효한지(바이탈체크) 확인
 *
 * @return bool 세션이 만료되지 않았으면 true
 */
function isSessionAlive(): bool {
    if (! isset($_SESSION['last_activity'])) {
        // 최초 호출 시 타임스탬프 설정
        refreshLastActivity();
        return true;
    }
    return (time() - $_SESSION['last_activity']) < SESSION_TIMEOUT;
}

/**
 * 로그인 상태 확인 (세션 유효성 포함)
 *
 * @return bool 로그인 중이고, 세션이 살아있으면 true
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && isSessionAlive();
}

/**
 * 현재 로그인 사용자 정보 가져오기
 *
 * @return array|null ['id'=>…, 'nickname'=>…] 또는 null
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id'       => $_SESSION['user_id'],
            'nickname' => $_SESSION['nickname']
        ];
    }
    return null;
}

/**
 * 로그아웃 처리 (세션 파기)
 */
function logout(): void {
    // 세션 데이터 비우기
    $_SESSION = [];
    // 쿠키 삭제
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    // 세션 자체 파기
    session_destroy();
}

/**
 * 로그아웃 상태 확인
 *
 * @return bool 로그아웃 상태면 true
 */
function isLoggedOut(): bool {
    return ! isLoggedIn();
}

/**
 * 로그인 필요 페이지 진입 시 호출
 * - 로그인 안 됐거나 세션 만료 시 리디렉트 및 세션 파기
 * - 로그인 상태면 마지막 활동 시간 갱신
 */
function requireLogin(): void {
    if (! isLoggedIn()) {
        logout();
        header('Location: login.php');
        exit;
    }
    // 세션 연장
    refreshLastActivity();
}
