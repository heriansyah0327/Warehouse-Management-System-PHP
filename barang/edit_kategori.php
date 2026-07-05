<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_admin();

$base = '../';
$active_menu = 'barang';
$active_sub = 'kategori';
$page_title = 'Edit Kategori Barang - Gudang';
$page_header = 'Edit Kategori Barang';
$page_icon = '&#9998;';

$CLASS_OPTIONS = ['Class 1', 'Class 2', 'Class 3', 'Class 4'];
$TAG_OPTIONS   = ['Bungkusan', 'Mentahan'];
$TAG_LAINNYA_OPTIONS = ['Vest', 'Ammo', 'Attachment'];

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, 'SELECT * FROM kategori_barang WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$barang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$barang) {
    flash_set('Data kategori barang tidak ditemukan.', 'danger');
    header('Location: kategori_barang.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $check = mysqli_prepare($conn, 'SELECT id FROM kategori_barang WHERE jenis = ? AND nama_barang = ? AND class_senjata <=> ? AND tag_narko <=> ? AND tag_lainnya <=> ? AND id <> ? LIMIT 1');
        mysqli_stmt_bind_param($check, 'sssssi', $jenis, $nama_barang, $class_senjata, $tag_narko, $tag_lainnya, $id);
        mysqli_stmt_execute($check);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            $errors[] = 'Barang dengan jenis, nama, dan class/tag yang sama sudah ada.';
        } else {
            $upd = mysqli_prepare($conn, 'UPDATE kategori_barang SET jenis=?, nama_barang=?, class_senjata=?, tag_narko=?, tag_lainnya=? WHERE id=?');
            mysqli_stmt_bind_param($upd, 'sssssi', $jenis, $nama_barang, $class_senjata, $tag_narko, $tag_lainnya, $id);
            mysqli_stmt_execute($upd);
            flash_set('Kategori barang "' . $nama_barang . '" berhasil diperbarui.');
            header('Location: kategori_barang.php');
            exit;
        }
    }
}

$curJenis  = $_POST['jenis'] ?? $barang['jenis'];
$curClass  = $_POST['class_senjata'] ?? $barang['class_senjata'];
$curTag    = $_POST['tag_narko'] ?? $barang['tag_narko'];
$curTagLainnya = $_POST['tag_lainnya'] ?? $barang['tag_lainnya'];

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel plain">
    <h3>Edit Kategori Barang</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_kategori.php?id=<?= (int)$id ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="jenis">Kategori</label>
                <select id="jenis" name="jenis" data-toggle-group="jenisKategori">
                    <option value="Senjata" <?= ($curJenis === 'Senjata') ? 'selected' : '' ?>>Senjata</option>
                    <option value="Narko" <?= ($curJenis === 'Narko') ? 'selected' : '' ?>>Narko</option>
                    <option value="Lainnya" <?= ($curJenis === 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" id="nama_barang" name="nama_barang" value="<?= e($_POST['nama_barang'] ?? $barang['nama_barang']) ?>" required>
            </div>
            <div class="form-group" data-toggle-target="jenisKategori" data-toggle-value="Senjata">
                <label for="class_senjata">Class Senjata</label>
                <select id="class_senjata" name="class_senjata">
                    <?php foreach ($CLASS_OPTIONS as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= ($curClass === $opt) ? 'selected' : '' ?>><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" data-toggle-target="jenisKategori" data-toggle-value="Narko">
                <label for="tag_narko">Tag Narko</label>
                <select id="tag_narko" name="tag_narko">
                    <?php foreach ($TAG_OPTIONS as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= ($curTag === $opt) ? 'selected' : '' ?>><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" data-toggle-target="jenisKategori" data-toggle-value="Lainnya">
                <label for="tag_lainnya">Tag Lainnya</label>
                <select id="tag_lainnya" name="tag_lainnya">
                    <?php foreach ($TAG_LAINNYA_OPTIONS as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= ($curTagLainnya === $opt) ? 'selected' : '' ?>><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-actions-panel" style="margin-top:18px;">
            <button type="submit" class="btn-primary-form">Simpan Perubahan</button>
            <a href="kategori_barang.php" class="btn-cancel-form" style="display:inline-flex;align-items:center;">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>