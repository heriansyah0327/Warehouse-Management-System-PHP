<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$base = '../';
$active_menu = 'penjualan';
$active_sub = 'jual';
$page_title = 'Penjualan - Gudang';
$page_header = 'Penjualan';
$page_icon = '&#128176;';

$errors = [];

// ---------------------------------------------------
// Proses tambah penjualan (khusus kategori Narko)
// ---------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_jual'])) {
    $id_homies = (int)($_POST['id_homies'] ?? 0);
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $jumlah    = (int)($_POST['jumlah'] ?? 0);

    if ($id_homies <= 0) {
        $errors[] = 'Pilih nama homies terlebih dahulu.';
    }
    if ($id_barang <= 0) {
        $errors[] = 'Pilih nama narko terlebih dahulu.';
    }
    if ($jumlah <= 0) {
        $errors[] = 'Jumlah harus lebih dari 0.';
    }

    $homies = null;
    $narko = null;
    if (!$errors) {
        $ch = mysqli_prepare($conn, 'SELECT id, nama, cid FROM homies WHERE id = ? LIMIT 1');
        mysqli_stmt_bind_param($ch, 'i', $id_homies);
        mysqli_stmt_execute($ch);
        $homies = mysqli_fetch_assoc(mysqli_stmt_get_result($ch));
        if (!$homies) {
            $errors[] = 'Homies tidak ditemukan. Pastikan dipilih dari daftar pencarian.';
        }

        $cn = mysqli_prepare($conn, "SELECT id, nama_barang, tag_narko, stok FROM kategori_barang WHERE id = ? AND jenis = 'Narko' AND tag_narko = 'Bungkusan' LIMIT 1");
        mysqli_stmt_bind_param($cn, 'i', $id_barang);
        mysqli_stmt_execute($cn);
        $narko = mysqli_fetch_assoc(mysqli_stmt_get_result($cn));
        if (!$narko) {
            $errors[] = 'Narko tidak ditemukan. Pastikan dipilih dari daftar pencarian.';
        } elseif ((int)$narko['stok'] < $jumlah) {
            $errors[] = 'Stok tidak cukup. Sisa stok "' . $narko['nama_barang'] . '" saat ini: ' . (int)$narko['stok'] . '.';
        }
    }

    if (!$errors) {
        $upd = mysqli_prepare($conn, 'UPDATE kategori_barang SET stok = stok - ? WHERE id = ?');
        mysqli_stmt_bind_param($upd, 'ii', $jumlah, $id_barang);
        mysqli_stmt_execute($upd);

        $tanggal = date('Y-m-d');
        $oleh = current_user()['full_name'];
        $ins = mysqli_prepare($conn, "INSERT INTO penjualan (id_homies, id_barang, tanggal_penjualan, jumlah, status, diinput_oleh) VALUES (?, ?, ?, ?, 'Proses', ?)");
        mysqli_stmt_bind_param($ins, 'iisis', $id_homies, $id_barang, $tanggal, $jumlah, $oleh);
        mysqli_stmt_execute($ins);

        // Catat juga sebagai Barang Keluar supaya muncul di Laporan Barang Keluar
        $alasanKeluar = 'Dijual oleh ' . $homies['nama'] . ' (' . $homies['cid'] . ')';
        $insKeluar = mysqli_prepare($conn, 'INSERT INTO barang_keluar (id_barang, jumlah, alasan, tanggal, diinput_oleh) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($insKeluar, 'iisss', $id_barang, $jumlah, $alasanKeluar, $tanggal, $oleh);
        mysqli_stmt_execute($insKeluar);

        flash_set('Narko "' . $narko['nama_barang'] . '" sebanyak ' . $jumlah . ' sedang dijual oleh ' . $homies['nama'] . '.');
        header('Location: penjualan.php');
        exit;
    }
}

// ---------------------------------------------------
// Data untuk searchable-select (homies & narko)
// ---------------------------------------------------
$homiesList = [];
$res = mysqli_query($conn, 'SELECT id, nama, cid FROM homies ORDER BY nama ASC');
while ($row = mysqli_fetch_assoc($res)) { $homiesList[] = $row; }

$narkoList = [];
$res = mysqli_query($conn, "SELECT id, nama_barang, tag_narko, stok FROM kategori_barang WHERE jenis = 'Narko' AND tag_narko = 'Bungkusan' ORDER BY nama_barang ASC");
while ($row = mysqli_fetch_assoc($res)) { $narkoList[] = $row; }

// ---------------------------------------------------
// Daftar penjualan yang masih proses (belum dikonfirmasi selesai)
// ---------------------------------------------------
$aktifList = [];
$res = mysqli_query($conn, "SELECT p.id, p.jumlah, p.tanggal_penjualan, h.nama AS nama_homies, h.cid,
                                    k.nama_barang, k.tag_narko
                             FROM penjualan p
                             JOIN homies h ON h.id = p.id_homies
                             JOIN kategori_barang k ON k.id = p.id_barang
                             WHERE p.status = 'Proses'
                             ORDER BY p.tanggal_penjualan DESC, p.id DESC");
while ($row = mysqli_fetch_assoc($res)) { $aktifList[] = $row; }

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel">
    <h3>Jual Narko</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="penjualan.php">
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
                <label for="narkoSearch">Nama Narko</label>
                <div class="search-select" data-search-select>
                    <input type="text" id="narkoSearch" class="ss-input" data-ss-search
                           placeholder="Cari nama narko ..." autocomplete="off">
                    <input type="hidden" name="id_barang" data-ss-value>
                    <div class="ss-dropdown" data-ss-dropdown>
                        <?php foreach ($narkoList as $n): ?>
                            <div class="ss-option <?= (int)$n['stok'] <= 0 ? 'ss-option-disabled' : '' ?>"
                                 data-value="<?= (int)$n['id'] ?>"
                                 data-label="<?= e($n['nama_barang']) ?> (<?= e($n['tag_narko']) ?>)"
                                 data-search="<?= e($n['nama_barang'] . ' ' . $n['tag_narko']) ?>">
                                <span><?= e($n['nama_barang']) ?> <span class="ss-sub">(<?= e($n['tag_narko']) ?>)</span></span>
                                <span class="ss-sub">Stok: <?= (int)$n['stok'] ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="ss-empty">Narko tidak ditemukan. Tambahkan dulu di Kategori Barang.</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" id="jumlah" name="jumlah" min="1" placeholder="Masukkan Jumlah ..." value="<?= e($_POST['jumlah'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-actions-panel" style="margin-top:18px;">
            <button type="submit" name="add_jual" value="1" class="btn-primary-form">Simpan</button>
            <button type="reset" class="btn-cancel-form">Batal</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <div>
            <h3>&#128176; Daftar Penjualan Proses</h3>
            <p class="panel-desc">&#128176; Daftar Narko yang sedang dijual dan belum dikonfirmasi selesai. Klik Konfirmasi Selesai setelah transaksi benar-benar rampung.</p>
        </div>
    </div>
    <div class="panel-toolbar">
        <span></span>
        <div class="search-box">
            <input type="text" placeholder="Cari ..." data-table-search="#penjualanTable">
            <span class="icon">&#128269;</span>
        </div>
    </div>

    <table class="data-table" id="penjualanTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Homies</th>
                <th>CID</th>
                <th>Nama Narko</th>
                <th>Detail</th>
                <th>Jumlah</th>
                <th>Tanggal Penjualan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($aktifList)): ?>
                <tr><td colspan="8" style="text-align:center; color:var(--text-muted);">Belum ada penjualan yang sedang proses.</td></tr>
            <?php else: ?>
                <?php foreach ($aktifList as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($p['nama_homies']) ?></td>
                    <td><?= e($p['cid']) ?></td>
                    <td><?= e($p['nama_barang']) ?></td>
                    <td><span class="badge badge-narko"><?= e($p['tag_narko']) ?></span></td>
                    <td><?= (int)$p['jumlah'] ?></td>
                    <td><?= e(date('d-m-Y', strtotime($p['tanggal_penjualan']))) ?></td>
                    <td class="aksi-cell">
                        <form method="POST" action="selesai.php" style="display:inline;"
                              onsubmit="return confirm('Konfirmasi: penjualan &quot;<?= e(addslashes($p['nama_barang'])) ?>&quot; sebanyak <?= (int)$p['jumlah'] ?> oleh <?= e(addslashes($p['nama_homies'])) ?> sudah selesai?');">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn-return">&#10003; Konfirmasi Selesai</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span>Jumlah Penjualan Proses: <?= count($aktifList) ?> Baris</span>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
