<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT p.id, k.nama_barang, h.nama AS nama_homies, p.jumlah
                                    FROM penjualan p
                                    JOIN kategori_barang k ON k.id = p.id_barang
                                    JOIN homies h ON h.id = p.id_homies
                                    WHERE p.id = ? AND p.status = 'Proses' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($row) {
        $tanggal = date('Y-m-d');
        $oleh = current_user()['full_name'];

        // Hanya update status, stok TIDAK dikembalikan karena narko sudah terjual/habis
        $upd = mysqli_prepare($conn, "UPDATE penjualan SET status = 'Selesai', tanggal_selesai = ?, diselesaikan_oleh = ? WHERE id = ?");
        mysqli_stmt_bind_param($upd, 'ssi', $tanggal, $oleh, $id);
        mysqli_stmt_execute($upd);

        flash_set('Penjualan "' . $row['nama_barang'] . '" sebanyak ' . (int)$row['jumlah'] . ' oleh ' . $row['nama_homies'] . ' berhasil dikonfirmasi selesai.');
    } else {
        flash_set('Data penjualan tidak ditemukan atau sudah dikonfirmasi selesai sebelumnya.', 'danger');
    }
}

header('Location: penjualan.php');
exit;
