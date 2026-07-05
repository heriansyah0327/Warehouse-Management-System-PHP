<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_admin();

$id = (int)($_GET['id'] ?? 0);

if ($id === (int)current_user()['id']) {
    flash_set('Kamu tidak bisa menghapus akun yang sedang kamu pakai sendiri.', 'danger');
    header('Location: management_staff.php');
    exit;
}

if ($id > 0) {
    $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    flash_set('Akun berhasil dihapus.');
}

header('Location: management_staff.php');
exit;
