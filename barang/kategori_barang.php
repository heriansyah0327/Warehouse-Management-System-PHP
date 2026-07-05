<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_admin();

$base = '../';
$active_menu = 'barang';
$active_sub = 'kategori';
$page_title = 'Kategori Barang - Gudang';
$page_header = 'Kategori Barang';
$page_icon = '&#128230;';

$errors = [];

$CLASS_OPTIONS = ['Class 1', 'Class 2', 'Class 3', 'Class 4'];
$TAG_OPTIONS   = ['Bungkusan', 'Mentahan'];
$TAG_LAINNYA_OPTIONS = ['Vest', 'Ammo', 'Attachment'];

// ---------------------------------------------------
// Proses tambah kategori/barang baru
// ---------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_kategori'])) {
    $jenisPost = $_POST['jenis'] ?? '';
    $jenis = in_array($jenisPost, ['Senjata', 'Narko', 'Lainnya'], true) ? $jenisPost : 'Senjata';
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $class_senjata = null;
    $tag_narko     = null;
    $tag_lainnya   = null;

    if ($jenis === 'Senjata') {
        $class_senjata = $_POST['class_senjata'] ?? '';
        if (!in_array($class_senjata, $CLASS_OPTIONS, true)) {
            $errors[] = 'Class senjata tidak valid.';
        }
    } elseif ($jenis === 'Narko') {
        $tag_narko = $_POST['tag_narko'] ?? '';
        if (!in_array($tag_narko, $TAG_OPTIONS, true)) {
            $errors[] = 'Tag narko tidak valid.';
        }
    } else {
        $tag_lainnya = $_POST['tag_lainnya'] ?? '';
        if (!in_array($tag_lainnya, $TAG_LAINNYA_OPTIONS, true)) {
            $errors[] = 'Tag lainnya tidak valid.';
        }
    }

    if ($nama_barang === '') {
        $errors[] = 'Nama barang wajib diisi.';
    }

    if (!$errors) {
        $check = mysqli_prepare($conn, 'SELECT id FROM kategori_barang WHERE jenis = ? AND nama_barang = ? AND class_senjata <=> ? AND tag_narko <=> ? AND tag_lainnya <=> ? LIMIT 1');
        mysqli_stmt_bind_param($check, 'sssss', $jenis, $nama_barang, $class_senjata, $tag_narko, $tag_lainnya);
        mysqli_stmt_execute($check);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            $errors[] = 'Barang dengan jenis, nama, dan class/tag yang sama sudah ada. Gunakan halaman Barang Masuk untuk menambah stoknya.';
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO kategori_barang (jenis, nama_barang, class_senjata, tag_narko, tag_lainnya, stok) VALUES (?, ?, ?, ?, ?, 0)');
            mysqli_stmt_bind_param($stmt, 'sssss', $jenis, $nama_barang, $class_senjata, $tag_narko, $tag_lainnya);
            mysqli_stmt_execute($stmt);
            flash_set('Kategori barang "' . $nama_barang . '" berhasil ditambahkan.');
            header('Location: kategori_barang.php');
            exit;
        }
    }
}

// ---------------------------------------------------
// Ambil semua kategori barang
// ---------------------------------------------------
$list = [];
$res = mysqli_query($conn, 'SELECT id, jenis, nama_barang, class_senjata, tag_narko, tag_lainnya, stok FROM kategori_barang ORDER BY jenis ASC, nama_barang ASC');
while ($row = mysqli_fetch_assoc($res)) {
    $list[] = $row;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel">
    <h3>Tambah Kategori Barang</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="kategori_barang.php">
        <div class="form-grid">
            <div class="form-group">
                <label for="jenis">Kategori</label>
                <select id="jenis" name="jenis" data-toggle-group="jenisKategori">
                    <option value="Senjata" <?= (($_POST['jenis'] ?? 'Senjata') === 'Senjata') ? 'selected' : '' ?>>Senjata</option>
                    <option value="Narko" <?= (($_POST['jenis'] ?? '') === 'Narko') ? 'selected' : '' ?>>Narko</option>
                    <option value="Lainnya" <?= (($_POST['jenis'] ?? '') === 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" id="nama_barang" name="nama_barang" placeholder="Masukkan Nama Barang ..." value="<?= e($_POST['nama_barang'] ?? '') ?>" required>
            </div>
            <div class="form-group" data-toggle-target="jenisKategori" data-toggle-value="Senjata">
                <label for="class_senjata">Class Senjata</label>
                <select id="class_senjata" name="class_senjata">
                    <?php foreach ($CLASS_OPTIONS as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= (($_POST['class_senjata'] ?? '') === $opt) ? 'selected' : '' ?>><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" data-toggle-target="jenisKategori" data-toggle-value="Narko">
                <label for="tag_narko">Tag Narko</label>
                <select id="tag_narko" name="tag_narko">
                    <?php foreach ($TAG_OPTIONS as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= (($_POST['tag_narko'] ?? '') === $opt) ? 'selected' : '' ?>><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" data-toggle-target="jenisKategori" data-toggle-value="Lainnya">
                <label for="tag_lainnya">Tag Lainnya</label>
                <select id="tag_lainnya" name="tag_lainnya">
                    <?php foreach ($TAG_LAINNYA_OPTIONS as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= (($_POST['tag_lainnya'] ?? '') === $opt) ? 'selected' : '' ?>><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-actions-panel" style="margin-top:18px;">
            <button type="submit" name="add_kategori" value="1" class="btn-primary-form">Simpan</button>
            <button type="reset" class="btn-cancel-form">Batal</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <h3>&#128230; Data Kategori Barang</h3>
    </div>
    <div class="panel-toolbar">
        <span></span>
        <div class="search-box">
            <input type="text" placeholder="Cari ..." data-table-search="#kategoriTable">
            <span class="icon">&#128269;</span>
        </div>
    </div>

    <table class="data-table" id="kategoriTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Nama Barang</th>
                <th>Detail</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted);">Belum ada data kategori barang.</td></tr>
            <?php else: ?>
                <?php foreach ($list as $i => $b): ?>
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
                    <td><?= (int)$b['stok'] ?></td>
                    <td class="aksi-cell">
                        <a class="btn-icon edit" href="edit_kategori.php?id=<?= (int)$b['id'] ?>" title="Edit">&#9998;</a>
                        <button type="button" class="btn-icon delete" title="Hapus"
                            data-delete-url="hapus_kategori.php?id=<?= (int)$b['id'] ?>"
                            data-delete-label="barang &quot;<?= e($b['nama_barang']) ?>&quot;">&#128465;</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span>Jumlah Kategori Barang: <?= count($list) ?> Item</span>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>