<?php
require_once __DIR__ . '/functions.php';

function start_session()
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
    session_name('moc_admin');
    session_start();
}

function admin_login($email, $password)
{
    $email = strtolower(trim($email));
    $admin = q_one('SELECT * FROM admins WHERE email = ?', [$email]);
    if (!$admin || !password_verify($password, $admin['password_hash'])) return false;
    start_session();
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    return true;
}

function admin_logout()
{
    start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function is_admin()
{
    start_session();
    return !empty($_SESSION['admin_id']);
}

function current_admin_email() { return $_SESSION['admin_email'] ?? ''; }

function require_admin()
{
    if (!is_admin()) redirect(url('admin/login.php'));
}
