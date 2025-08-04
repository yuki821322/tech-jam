<?php
session_start(); // セッション開始

// セッションの中身を削除
$_SESSION = [];

// Cookieの削除（セキュリティ対策）
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// セッションを破棄
session_destroy();

// ログインページなどにリダイレクト
header("Location: ../PHP/index.php");
exit;
