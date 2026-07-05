<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . base_url('index.php'));
        exit;
    }
}

function current_user() {
    return [
        'id'        => $_SESSION['user_id'] ?? null,
        'username'  => $_SESSION['username'] ?? null,
        'full_name' => $_SESSION['full_name'] ?? null,
        'role'      => $_SESSION['role'] ?? null,
    ];
}

function is_admin() {
    return ($_SESSION['role'] ?? null) === 'admin';
}

function role_label() {
    return is_admin() ? 'Administrator' : 'Kepala Staff';
}

function require_admin() {
    if (!is_admin()) {
        header('Location: ' . base_url('dashboard.php'));
        exit;
    }
}

// Menghitung base path absolut (dari web root) untuk folder aplikasi ini,
// supaya redirect dari require_login()/require_admin() selalu benar walau
// dipanggil dari sub-folder (misal /staff/management_staff.php).
function base_url($path = '') {
    $appRootFs = realpath(__DIR__ . '/..'); // folder root project (tempat index.php berada)
    $docRoot   = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');

    if ($docRoot && $appRootFs && strpos($appRootFs, $docRoot) === 0) {
        $webRoot = str_replace('\\', '/', substr($appRootFs, strlen($docRoot)));
    } else {
        $webRoot = '';
    }

    return rtrim($webRoot, '/') . '/' . ltrim($path, '/');
}

function flash_set($msg, $type = 'success') {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function flash_get() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
