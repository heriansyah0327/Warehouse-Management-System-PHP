<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$base = '../';
$active_menu = 'barang';
$active_sub = 'keluar';
$page_title = 'Barang Keluar - Gudang';
$page_header = 'Barang Keluar';
$page_icon = '&#128230;';

$errors = [];

// ---------------------------------------------------
// Proses kurangi stok (barang keluar)
// ---------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_keluar'])) {
    $jenisPost = $_POST['jenis'] ?? '';
    $jenis = in_array($jenisPost, ['Senjata', 'Narko', 'Lainnya'], true) ? $jenisPost : 'Senjata';
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $jumlah    = (int)($_POST['jumlah'] ?? 0);
    $alasan    = trim($_POST['alasan'] ?? '');

    if ($id_barang <= 0) {
        $errors[] = 'Pilih barang terlebih dahulu.';
    }
    if ($jumlah <= 0) {
        $errors[] = 'Jumlah harus lebih dari 0.';
    }
    if ($alasan === '') {
        $errors[] = 'Alasan wajib diisi.';
    }

    $found = null;
    if (!$errors) {
        $check = mysqli_prepare($conn, 'SELECT id, nama_barang, stok FROM kategori_barang WHERE id = ? AND jenis = ? LIMIT 1');
        mysqli_stmt_bind_param($check, 'is', $id_barang, $jenis);
        mysqli_stmt_execute($check);
        $found = mysqli_fetch_assoc(mysqli_stmt_get_result($check));

        if (!$found) {
            $errors[] = 'Barang tidak ditemukan. Pastikan sudah dibuat di halaman Kategori Barang.';
        } elseif ((int)$found['stok'] < $jumlah) {
            $errors[] = 'Stok tidak cukup. Sisa stok "' . $found['nama_barang'] . '" saat ini: ' . (int)$found['stok'] . '.';
        }
    }

    if (!$errors) {
        $upd = mysqli_prepare($conn, 'UPDATE kategori_barang SET stok = stok - ? WHERE id = ?');
        mysqli_stmt_bind_param($upd, 'ii', $jumlah, $id_barang);
        mysqli_stmt_execute($upd);

        $tanggal = date('Y-m-d');
        $oleh = current_user()['full_name'];
        $ins = mysqli_prepare($conn, 'INSERT INTO barang_keluar (id_barang, jumlah, alasan, tanggal, diinput_oleh) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($ins, 'iisss', $id_barang, $jumlah, $alasan, $tanggal, $oleh);
        mysqli_stmt_execute($ins);

        flash_set('Stok "' . $found['nama_barang'] . '" berhasil dikurangi sebanyak ' . $jumlah . '.');
        header('Location: barang_keluar.php');
        exit;
    }
}

// ---------------------------------------------------
// Ambil daftar master barang per jenis (untuk dropdown)
// ---------------------------------------------------
$senjataList = [];
$res = mysqli_query($conn, "SELECT id, nama_barang, class_senjata, stok FROM kategori_barang WHERE jenis = 'Senjata' ORDER BY nama_barang ASC");
while ($row = mysqli_fetch_assoc($res)) { $senjataList[] = $row; }

$narkoList = [];
$res = mysqli_query($conn, "SELECT id, nama_barang, tag_narko, stok FROM kategori_barang WHERE jenis = 'Narko' ORDER BY nama_barang ASC");
while ($row = mysqli_fetch_assoc($res)) { $narkoList[] = $row; }

$lainnyaList = [];
$res = mysqli_query($conn, "SELECT id, nama_barang, tag_lainnya, stok FROM kategori_barang WHERE jenis = 'Lainnya' ORDER BY nama_barang ASC");
while ($row = mysqli_fetch_assoc($res)) { $lainnyaList[] = $row; }

// ---------------------------------------------------
// Tabel semua stok saat ini
// ---------------------------------------------------
$allBarang = [];
$res = mysqli_query($conn, 'SELECT id, jenis, nama_barang, class_senjata, tag_narko, tag_lainnya, stok FROM kategori_barang ORDER BY jenis ASC, nama_barang ASC');
while ($row = mysqli_fetch_assoc($res)) { $allBarang[] = $row; }

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel">
    <h3>Input Barang Keluar</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="barang_keluar.php">
        <div class="form-grid">
            <div class="form-group">
                <label for="jenis">Jenis Barang</label>
                <select id="jenis" name="jenis" data-toggle-group="jenisKeluar">
                    <option value="Senjata" <?= (($_POST['jenis'] ?? 'Senjata') === 'Senjata') ? 'selected' : '' ?>>Senjata</option>
                    <option value="Narko" <?= (($_POST['jenis'] ?? '') === 'Narko') ? 'selected' : '' ?>>Narko</option>
                    <option value="Lainnya" <?= (($_POST['jenis'] ?? '') === 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                </select>
            </div>

            <div class="form-group" data-toggle-target="jenisKeluar" data-toggle-value="Senjata">
                <label for="senjataSearchKeluar">Nama Senjata</label>
                <div class="search-select" data-search-select>
                    <input type="text" id="senjataSearchKeluar" class="ss-input" data-ss-search
                           placeholder="Cari nama senjata ..." autocomplete="off">
                    <input type="hidden" name="id_barang" data-ss-value>
                    <div class="ss-dropdown" data-ss-dropdown>
                        <?php foreach ($senjataList as $s): ?>
                            <div class="ss-option"
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

            <div class="form-group" data-toggle-target="jenisKeluar" data-toggle-value="Narko">
                <label for="id_barang_narko">Nama Narko</label>
                <select id="id_barang_narko" name="id_barang">
                    <?php if (empty($narkoList)): ?>
                        <option value="">-- Belum ada data, tambahkan di Kategori Barang --</option>
                    <?php else: ?>
                        <?php foreach ($narkoList as $n): ?>
                            <option value="<?= (int)$n['id'] ?>"><?= e($n['nama_barang']) ?> (<?= e($n['tag_narko']) ?>) - Stok: <?= (int)$n['stok'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group" data-toggle-target="jenisKeluar" data-toggle-value="Lainnya">
                <label for="id_barang_lainnya">Nama Barang Lainnya</label>
                <select id="id_barang_lainnya" name="id_barang">
                    <?php if (empty($lainnyaList)): ?>
                        <option value="">-- Belum ada data, tambahkan di Kategori Barang --</option>
                    <?php else: ?>
                        <?php foreach ($lainnyaList as $n): ?>
                            <option value="<?= (int)$n['id'] ?>"><?= e($n['nama_barang']) ?> (<?= e($n['tag_lainnya']) ?>) - Stok: <?= (int)$n['stok'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" id="jumlah" name="jumlah" min="1" placeholder="Masukkan Jumlah ..." value="<?= e($_POST['jumlah'] ?? '') ?>" required>
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="alasan">Alasan</label>
                <input type="text" id="alasan" name="alasan" placeholder="Masukkan Alasan Barang Keluar ..." value="<?= e($_POST['alasan'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-actions-panel" style="margin-top:18px;">
            <button type="submit" name="add_keluar" value="1" class="btn-primary-form">Simpan</button>
            <button type="reset" class="btn-cancel-form">Batal</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <h3>&#128230; Stok Barang Saat Ini</h3>
    </div>
    <div class="panel-toolbar">
        <span></span>
        <div class="search-box">
            <input type="text" placeholder="Cari ..." data-table-search="#stokTable">
            <span class="icon">&#128269;</span>
        </div>
    </div>

    <table class="data-table" id="stokTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Nama Barang</th>
                <th>Detail</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($allBarang)): ?>
                <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Belum ada data barang.</td></tr>
            <?php else: ?>
                <?php foreach ($allBarang as $i => $b): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <?php if ($b['jenis'] === 'Senjata'): ?>
                            <span class="badge badge-senjata">Senjata</span>
                        <?php elseif ($b['jenis'] === 'Narko'): ?>
                            <span class="badge badge-narko">Narko</span>
                        <?php else: ?>
                            <span class="badge badge-lainnya">Lainnya</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($b['nama_barang']) ?></td>
                    <td><span class="badge badge-chip"><?= e($b['class_senjata'] ?? $b['tag_narko'] ?? $b['tag_lainnya'] ?? '-') ?></span></td>
                    <td><?= (int)$b['stok'] === 0 ? '<span class="stok-low">0</span>' : (int)$b['stok'] ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span>Jumlah Jenis Barang: <?= count($allBarang) ?> Item</span>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>