<?php
/**
 * Logout — hancurkan session dan redirect ke login.
 * Hanya menerima POST untuk mencegah logout paksa via link.
 */

require_once 'config/auth.php';
require_once 'config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

csrf_verify();

// Hancurkan session sepenuhnya
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: login.php');
exit;
