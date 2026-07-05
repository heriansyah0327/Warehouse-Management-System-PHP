<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_admin();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($conn, 'DELETE FROM kategori_barang WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    flash_set('Kategori barang berhasil dihapus (riwayat masuk/keluar terkait ikut terhapus).');
}

header('Location: kategori_barang.php');
exit;
