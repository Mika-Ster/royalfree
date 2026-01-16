<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Token store file (remember tokens for users/admins) — moved to logic/
define('REMEMBER_TOKENS_FILE', __DIR__ . '/remember_tokens.json');

// Load configuration (ADMIN email / password hash) from logic/config.php
require_once __DIR__ . '/config.php';

// Database connection (provides $pdo)
require_once __DIR__ . '/db.php';

// Define ADMIN constants from config variables (with safe fallback for demo)
if (!defined('ADMIN_EMAIL')) {
    define('ADMIN_EMAIL', isset($ADMIN_EMAIL) && $ADMIN_EMAIL ? $ADMIN_EMAIL : 'admin@technikum-wien.at');
}
if (!defined('ADMIN_PASS_HASH')) {
    if (!empty($ADMIN_PASS_HASH)) {
        define('ADMIN_PASS_HASH', $ADMIN_PASS_HASH);
    } else {
        // demo fallback (use a generated hash only for development)
        define('ADMIN_PASS_HASH', password_hash('admin123', PASSWORD_BCRYPT));
    }
}

function read_remember_tokens() {
    if (!file_exists(REMEMBER_TOKENS_FILE)) return [];
    $c = file_get_contents(REMEMBER_TOKENS_FILE);
    $d = json_decode($c, true);
    return is_array($d) ? $d : [];
}

function write_remember_tokens(array $data) {
    file_put_contents(REMEMBER_TOKENS_FILE, json_encode($data));
}

// Auto-login via remember cookies (users + admin)
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_user'])) {
    $token = $_COOKIE['remember_user'];
    $tokenHash = hash('sha256', $token);
    $store = read_remember_tokens();
    if (isset($store[$tokenHash]) && $store[$tokenHash]['type'] === 'user') {
        if ($store[$tokenHash]['expires'] >= time()) {
            // fetch user from DB by email and set session
            $u = findUserByEmail($store[$tokenHash]['email']);
            if ($u) {
                $_SESSION['user'] = $u;
            }
        } else {
            unset($store[$tokenHash]); write_remember_tokens($store);
            setcookie('remember_user', '', time() - 3600, '/');
        }
    }
}

if (!isset($_SESSION['admin_logged_in']) && isset($_COOKIE['remember_admin'])) {
    $token = $_COOKIE['remember_admin'];
    $tokenHash = hash('sha256', $token);
    $store = read_remember_tokens();
    if (isset($store[$tokenHash]) && $store[$tokenHash]['type'] === 'admin') {
        if ($store[$tokenHash]['expires'] >= time()) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $store[$tokenHash]['email'];
        } else {
            unset($store[$tokenHash]); write_remember_tokens($store);
            setcookie('remember_admin', '', time() - 3600, '/');
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
        global $pdo;
        $stmt = $pdo->prepare('SELECT id, email, password, displayname, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row : null;
    }
}

// Helper: konsistente Prüfungen für eingeloggte User/Admin
if (!function_exists('isUserLoggedIn')) {
    function isUserLoggedIn(): bool {
        // Anpassbar: je nachdem, ob ihr 'user' (Array) oder 'user_logged_in' (Flag) verwendet
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
        // Determine role: first registered user becomes admin
        $countStmt = $pdo->query('SELECT COUNT(*) AS c FROM users');
        $count = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['c'];
        $role = ($count === 0) ? 'admin' : 'user';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (email, password, displayname, role) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$email, $hash, $displayname, $role]);
    }
}

// Login for regular users (stores secure random remember token)
if (!function_exists('login')) {
    function login($email, $password) {
        $user = findUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            if (!empty($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $store = read_remember_tokens();
                $store[$tokenHash] = ['email' => $user['email'], 'type' => 'user', 'expires' => time() + 60*60*24*30];
                write_remember_tokens($store);
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
            $store = read_remember_tokens();
            if (isset($store[$tokenHash]) && $store[$tokenHash]['type'] === 'user') unset($store[$tokenHash]);
            write_remember_tokens($store);
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

// --- Admin auth (demo). In production move credentials into env/config and use DB for tokens ---
// (ADMIN constants are defined above via logic/config.php)

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
            $store = read_remember_tokens();
            $store[$tokenHash] = ['email' => $email, 'type' => 'admin', 'expires' => time() + 60*60*24*30];
            write_remember_tokens($store);
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
            $store = read_remember_tokens();
            if (isset($store[$tokenHash]) && $store[$tokenHash]['type'] === 'admin') unset($store[$tokenHash]);
            write_remember_tokens($store);
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
