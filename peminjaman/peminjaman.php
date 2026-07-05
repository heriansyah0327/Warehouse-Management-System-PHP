<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$base = '../';
$active_menu = 'pinjam';
$page_title = 'Pinjam Barang - Gudang';
$page_header = 'Pinjam Barang';
$page_icon = '&#128196;';

$errors = [];

// ---------------------------------------------------
// Proses tambah peminjaman (khusus kategori Senjata)
// ---------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_pinjam'])) {
    $id_homies = (int)($_POST['id_homies'] ?? 0);
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $jumlah    = (int)($_POST['jumlah'] ?? 0);

    if ($id_homies <= 0) {
        $errors[] = 'Pilih nama homies terlebih dahulu.';
    }
    if ($id_barang <= 0) {
        $errors[] = 'Pilih nama senjata terlebih dahulu.';
    }
    if ($jumlah <= 0) {
        $errors[] = 'Jumlah harus lebih dari 0.';
    }

    $homies = null;
    $senjata = null;
    if (!$errors) {
        $ch = mysqli_prepare($conn, 'SELECT id, nama, cid FROM homies WHERE id = ? LIMIT 1');
        mysqli_stmt_bind_param($ch, 'i', $id_homies);
        mysqli_stmt_execute($ch);
        $homies = mysqli_fetch_assoc(mysqli_stmt_get_result($ch));
        if (!$homies) {
            $errors[] = 'Homies tidak ditemukan. Pastikan dipilih dari daftar pencarian.';
        }

        $cs = mysqli_prepare($conn, "SELECT id, nama_barang, class_senjata, stok FROM kategori_barang WHERE id = ? AND jenis = 'Senjata' LIMIT 1");
        mysqli_stmt_bind_param($cs, 'i', $id_barang);
        mysqli_stmt_execute($cs);
        $senjata = mysqli_fetch_assoc(mysqli_stmt_get_result($cs));
        if (!$senjata) {
            $errors[] = 'Senjata tidak ditemukan. Pastikan dipilih dari daftar pencarian.';
        } elseif ((int)$senjata['stok'] < $jumlah) {
            $errors[] = 'Stok tidak cukup. Sisa stok "' . $senjata['nama_barang'] . '" saat ini: ' . (int)$senjata['stok'] . '.';
        }
    }

    if (!$errors) {
        $upd = mysqli_prepare($conn, 'UPDATE kategori_barang SET stok = stok - ? WHERE id = ?');
        mysqli_stmt_bind_param($upd, 'ii', $jumlah, $id_barang);
        mysqli_stmt_execute($upd);

        $tanggal = date('Y-m-d');
        $oleh = current_user()['full_name'];
        $ins = mysqli_prepare($conn, "INSERT INTO peminjaman (id_homies, id_barang, tanggal_pinjam, jumlah, status, diinput_oleh) VALUES (?, ?, ?, ?, 'Dipinjam', ?)");
        mysqli_stmt_bind_param($ins, 'iisis', $id_homies, $id_barang, $tanggal, $jumlah, $oleh);
        mysqli_stmt_execute($ins);

        // Catat juga sebagai Barang Keluar supaya muncul di Laporan Barang Keluar
        $alasanKeluar = 'Dipinjam oleh ' . $homies['nama'] . ' (' . $homies['cid'] . ')';
        $insKeluar = mysqli_prepare($conn, 'INSERT INTO barang_keluar (id_barang, jumlah, alasan, tanggal, diinput_oleh) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($insKeluar, 'iisss', $id_barang, $jumlah, $alasanKeluar, $tanggal, $oleh);
        mysqli_stmt_execute($insKeluar);

        flash_set('Senjata "' . $senjata['nama_barang'] . '" sebanyak ' . $jumlah . ' berhasil dipinjamkan ke ' . $homies['nama'] . '.');
        header('Location: peminjaman.php');
        exit;
    }
}

// ---------------------------------------------------
// Data untuk searchable-select (homies & senjata)
// ---------------------------------------------------
$homiesList = [];
$res = mysqli_query($conn, 'SELECT id, nama, cid FROM homies ORDER BY nama ASC');
while ($row = mysqli_fetch_assoc($res)) { $homiesList[] = $row; }

$senjataList = [];
$res = mysqli_query($conn, "SELECT id, nama_barang, class_senjata, stok FROM kategori_barang WHERE jenis = 'Senjata' ORDER BY nama_barang ASC");
while ($row = mysqli_fetch_assoc($res)) { $senjataList[] = $row; }

// ---------------------------------------------------
// Daftar peminjaman yang masih aktif (belum dikembalikan)
// ---------------------------------------------------
$aktifList = [];
$res = mysqli_query($conn, "SELECT p.id, p.jumlah, p.tanggal_pinjam, h.nama AS nama_homies, h.cid,
                                    k.nama_barang, k.class_senjata
                             FROM peminjaman p
                             JOIN homies h ON h.id = p.id_homies
                             JOIN kategori_barang k ON k.id = p.id_barang
                             WHERE p.status = 'Dipinjam'
                             ORDER BY p.tanggal_pinjam DESC, p.id DESC");
while ($row = mysqli_fetch_assoc($res)) { $aktifList[] = $row; }

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel">
    <h3>Pinjam Senjata</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="peminjaman.php">
        <div class="form-grid">
            <div class="form-group">
                <label for="homiesSearch">Nama Homies</label>
                <div class="search-select" data-search-select>
                    <input type="text" id="homiesSearch" class="ss-input" data-ss-search
                           placeholder="Cari nama atau CID homies ..." autocomplete="off">
                    <input type="hidden" name="id_homies" data-ss-value>
                    <div class="ss-dropdown" data-ss-dropdown>
                        <?php foreach ($homiesList as $h): ?>
                            <div class="ss-option"
                                 data-value="<?= (int)$h['id'] ?>"
                                 data-label="<?= e($h['nama']) ?> (<?= e($h['cid']) ?>)"
                                 data-search="<?= e($h['nama'] . ' ' . $h['cid']) ?>">
                                <span><?= e($h['nama']) ?></span>
                                <span class="ss-sub"><?= e($h['cid']) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="ss-empty">Homies tidak ditemukan. Tambahkan dulu di Menajemen Homies.</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="senjataSearch">Nama Senjata</label>
                <div class="search-select" data-search-select>
                    <input type="text" id="senjataSearch" class="ss-input" data-ss-search
                           placeholder="Cari nama senjata ..." autocomplete="off">
                    <input type="hidden" name="id_barang" data-ss-value>
                    <div class="ss-dropdown" data-ss-dropdown>
                        <?php foreach ($senjataList as $s): ?>
                            <div class="ss-option <?= (int)$s['stok'] <= 0 ? 'ss-option-disabled' : '' ?>"
                                 data-value="<?= (int)$s['id'] ?>"
                                 data-label="<?= e($s['nama_barang']) ?> (<?= e($s['class_senjata']) ?>)"
                                 data-search="<?= e($s['nama_barang'] . ' ' . $s['class_senjata']) ?>">
                                <span><?= e($s['nama_barang']) ?> <span class="ss-sub">(<?= e($s['class_senjata']) ?>)</span></span>
                                <span class="ss-sub">Stok: <?= (int)$s['stok'] ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="ss-empty">Senjata tidak ditemukan. Tambahkan dulu di Kategori Barang.</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" id="jumlah" name="jumlah" min="1" placeholder="Masukkan Jumlah ..." value="<?= e($_POST['jumlah'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-actions-panel" style="margin-top:18px;">
            <button type="submit" name="add_pinjam" value="1" class="btn-primary-form">Simpan</button>
            <button type="reset" class="btn-cancel-form">Batal</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <div>
            <h3>&#128196; Daftar Peminjaman Aktif</h3>
            <p class="panel-desc">&#128196; Daftar Senjata yang sedang dipinjam dan belum dikembalikan ke gudang. Klik Konfirmasi Pengembalian setelah senjata diterima gudang.</p>
        </div>
    </div>
    <div class="panel-toolbar">
        <span></span>
        <div class="search-box">
            <input type="text" placeholder="Cari ..." data-table-search="#peminjamanTable">
            <span class="icon">&#128269;</span>
        </div>
    </div>

    <table class="data-table" id="peminjamanTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Homies</th>
                <th>CID</th>
                <th>Nama Senjata</th>
                <th>Detail</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($aktifList)): ?>
                <tr><td colspan="8" style="text-align:center; color:var(--text-muted);">Belum ada peminjaman aktif.</td></tr>
            <?php else: ?>
                <?php foreach ($aktifList as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($p['nama_homies']) ?></td>
                    <td><?= e($p['cid']) ?></td>
                    <td><?= e($p['nama_barang']) ?></td>
                    <td><span class="badge badge-chip"><?= e($p['class_senjata']) ?></span></td>
                    <td><?= (int)$p['jumlah'] ?></td>
                    <td><?= e(date('d-m-Y', strtotime($p['tanggal_pinjam']))) ?></td>
                    <td class="aksi-cell">
                        <form method="POST" action="kembalikan.php" style="display:inline;"
                              onsubmit="return confirm('Konfirmasi: senjata &quot;<?= e(addslashes($p['nama_barang'])) ?>&quot; sebanyak <?= (int)$p['jumlah'] ?> dari <?= e(addslashes($p['nama_homies'])) ?> sudah dikembalikan?');">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn-return">&#10003; Konfirmasi Pengembalian</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span>Jumlah Peminjaman Aktif: <?= count($aktifList) ?> Baris</span>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
