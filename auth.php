<?php
require_once __DIR__ . '/config.php';

define('APP_LOGIN_USER', 'cmnaour');
define('APP_LOGIN_PASSWORD', 'cmn2026');

function attemptLogin($username, $password)
{
    if ($username === APP_LOGIN_USER && $password === APP_LOGIN_PASSWORD) {
        session_regenerate_id(true);
        $_SESSION['auth_user'] = $username;
        return true;
    }

    return false;
}

function logoutUser()
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}