<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT p.id, p.id_barang, p.jumlah, k.nama_barang, h.nama AS nama_homies, h.cid
                                    FROM peminjaman p
                                    JOIN kategori_barang k ON k.id = p.id_barang
                                    JOIN homies h ON h.id = p.id_homies
                                    WHERE p.id = ? AND p.status = 'Dipinjam' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($row) {
        $tanggal = date('Y-m-d');
        $oleh = current_user()['full_name'];
        $jumlah = (int)$row['jumlah'];
        $alasanMasuk = 'Dikembalikan oleh ' . $row['nama_homies'] . ' (' . $row['cid'] . ')';

        // Tambah stok kembali
        $upd = mysqli_prepare($conn, 'UPDATE kategori_barang SET stok = stok + ? WHERE id = ?');
        mysqli_stmt_bind_param($upd, 'ii', $jumlah, $row['id_barang']);
        mysqli_stmt_execute($upd);

        // Catat sebagai Barang Masuk (dengan alasan otomatis) supaya konsisten dengan histori stok
        $ins = mysqli_prepare($conn, 'INSERT INTO barang_masuk (id_barang, jumlah, alasan, tanggal, diinput_oleh) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($ins, 'iisss', $row['id_barang'], $jumlah, $alasanMasuk, $tanggal, $oleh);
        mysqli_stmt_execute($ins);

        // Update status peminjaman
        $updP = mysqli_prepare($conn, "UPDATE peminjaman SET status = 'Dikembalikan', tanggal_kembali = ?, dikembalikan_oleh = ? WHERE id = ?");
        mysqli_stmt_bind_param($updP, 'ssi', $tanggal, $oleh, $id);
        mysqli_stmt_execute($updP);

        flash_set('Senjata "' . $row['nama_barang'] . '" sebanyak ' . $jumlah . ' berhasil dikembalikan dan stok telah ditambahkan.');
    } else {
        flash_set('Data peminjaman tidak ditemukan atau sudah dikembalikan sebelumnya.', 'danger');
    }
}

header('Location: peminjaman.php');
exit;
