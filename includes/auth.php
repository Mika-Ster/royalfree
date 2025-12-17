<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Auto-Login via Remember-Me-Cookie für User
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_user'])) {
    $token = $_COOKIE['remember_user'];
    if (isset($_SESSION['users'])) {
        foreach ($_SESSION['users'] as $u) {
            if (hash('sha256', $u['email']) === $token) {
                $_SESSION['user'] = $u;
                break;
            }
        }
    }
}

// Userspeicher initialisieren (Demo; später DB)
if (!isset($_SESSION['users'])) $_SESSION['users'] = [];

// Hilfsfunktionen (User)
if (!function_exists('currentUser')) {
    function currentUser() { return isset($_SESSION['user']) ? $_SESSION['user'] : null; }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() { return isset($_SESSION['user']); }
}

if (!function_exists('requireLogin')) {
    function requireLogin() { if (!isLoggedIn()) { header('Location: login.php'); exit(); } }
}

if (!function_exists('findUserByEmail')) {
    function findUserByEmail($email) {
        foreach ($_SESSION['users'] as $u) {
            if (strcasecmp($u['email'], $email) === 0) return $u;
        }
        return null;
    }
}

if (!function_exists('register')) {
    function register($email, $password, $displayname) {
        if (findUserByEmail($email)) return false;
        $new = [
            'id' => count($_SESSION['users']) + 1,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'displayname' => $displayname,
            'role' => count($_SESSION['users']) === 0 ? 'admin' : 'user'
        ];
        $_SESSION['users'][] = $new;
        return true;
    }
}

if (!function_exists('login')) {
    function login($email, $password) {
        $user = findUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            if (!empty($_POST['remember'])) {
                $token = hash('sha256', $user['email']);
                setcookie('remember_user', $token, [
                    'expires' => time() + 60*60*24*30,
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            }
            return true;
        }
        return false;
    }
}

if (!function_exists('logout')) {
    function logout() {
        if (isset($_SESSION['user'])) unset($_SESSION['user']);
        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            unset($_COOKIE['remember_user']);
        }
    }
}
