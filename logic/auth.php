<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/db.php';

function db_get_remember_token(string $tokenHash) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM remember_tokens WHERE token_hash = ? LIMIT 1');
    $stmt->execute([$tokenHash]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row : null;
}

function db_set_remember_token(string $tokenHash, ?string $email, string $type, int $expires, ?string $userAgent = null, ?string $ip = null) {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO remember_tokens (token_hash, user_email, type, expires, user_agent, ip) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE expires = VALUES(expires), user_agent = VALUES(user_agent), ip = VALUES(ip)');
    return $stmt->execute([$tokenHash, $email, $type, $expires, $userAgent, $ip]);
}

function db_delete_remember_token(string $tokenHash) {
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE token_hash = ?');
    return $stmt->execute([$tokenHash]);
}

if (!defined('ADMIN_EMAIL')) {
    define('ADMIN_EMAIL', isset($ADMIN_EMAIL) && $ADMIN_EMAIL ? $ADMIN_EMAIL : 'admin@technikum-wien.at');
}
if (!defined('ADMIN_PASS_HASH')) {
    define('ADMIN_PASS_HASH', !empty($ADMIN_PASS_HASH) ? $ADMIN_PASS_HASH : '');
}

if (!isset($_SESSION['user']) && isset($_COOKIE['remember_user'])) {
    $token = $_COOKIE['remember_user'];
    $tokenHash = hash('sha256', $token);
    $row = db_get_remember_token($tokenHash);
    if ($row && ($row['type'] ?? '') === 'user') {
        if ((int)($row['expires'] ?? 0) >= time()) {
            $u = findUserByEmail($row['user_email'] ?? $row['email'] ?? '');
            if ($u) {
                $_SESSION['user'] = $u;
            }
        } else {
            db_delete_remember_token($tokenHash);
            setcookie('remember_user', '', time() - 3600, '/');
        }
    }
}

if (!isset($_SESSION['admin_logged_in']) && isset($_COOKIE['remember_admin'])) {
    $token = $_COOKIE['remember_admin'];
    $tokenHash = hash('sha256', $token);
    $row = db_get_remember_token($tokenHash);
    if ($row && ($row['type'] ?? '') === 'admin') {
        if ((int)($row['expires'] ?? 0) >= time()) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $row['user_email'] ?? $row['email'] ?? null;
        } else {
            db_delete_remember_token($tokenHash);
            setcookie('remember_admin', '', time() - 3600, '/');
        }
    }
}


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
        global $pdo;
        $stmt = $pdo->prepare('SELECT id, email, password, displayname, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row : null;
    }
}


if (!function_exists('isUserLoggedIn')) {
    function isUserLoggedIn(): bool {
        return !empty($_SESSION['user_logged_in']) || !empty($_SESSION['user']);
    }
}

if (!function_exists('isAdminLoggedIn')) {
    function isAdminLoggedIn(): bool {
        return !empty($_SESSION['admin_logged_in']);
    }
}

if (!function_exists('register')) {
    function register($email, $password, $displayname) {
        global $pdo;
        if (findUserByEmail($email)) return false;
        $countStmt = $pdo->query('SELECT COUNT(*) AS c FROM users');
        $count = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['c'];
        $role = ($count === 0) ? 'admin' : 'user';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (email, password, displayname, role) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$email, $hash, $displayname, $role]);
    }
}

if (!function_exists('login')) {
    function login($email, $password) {
        $user = findUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            if (!empty($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                db_set_remember_token($tokenHash, $user['email'], 'user', time() + 60*60*24*30, $_SERVER['HTTP_USER_AGENT'] ?? null, $_SERVER['REMOTE_ADDR'] ?? null);
                setcookie('remember_user', $token, [
                    'expires' => time() + 60*60*24*30,
                    'path' => '/',
                    'secure' => (!empty($_SERVER['HTTPS'])),
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
            $token = $_COOKIE['remember_user'];
            $tokenHash = hash('sha256', $token);
            db_delete_remember_token($tokenHash);
            setcookie('remember_user', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => (!empty($_SERVER['HTTPS'])),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            unset($_COOKIE['remember_user']);
        }
    }
}


if (!function_exists('checkAdminCredentials')) {
    function checkAdminCredentials(string $email, string $password): bool {
        if (strcasecmp($email, ADMIN_EMAIL) !== 0) return false;
        return password_verify($password, ADMIN_PASS_HASH);
    }
}

if (!function_exists('loginAdmin')) {
    function loginAdmin(string $email, bool $remember = false) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $email;
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            db_set_remember_token($tokenHash, $email, 'admin', time() + 60*60*24*30, $_SERVER['HTTP_USER_AGENT'] ?? null, $_SERVER['REMOTE_ADDR'] ?? null);
            setcookie('remember_admin', $token, [
                'expires' => time() + 60*60*24*30,
                'path' => '/',
                'secure' => (!empty($_SERVER['HTTPS'])),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
    }
}

if (!function_exists('logoutAdmin')) {
    function logoutAdmin() {
        if (isset($_COOKIE['remember_admin'])) {
            $token = $_COOKIE['remember_admin'];
            $tokenHash = hash('sha256', $token);
            db_delete_remember_token($tokenHash);
            setcookie('remember_admin', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => (!empty($_SERVER['HTTPS'])),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            unset($_COOKIE['remember_admin']);
        }
        $_SESSION['admin_logged_in'] = false;
        unset($_SESSION['admin_user']);
    }
}
