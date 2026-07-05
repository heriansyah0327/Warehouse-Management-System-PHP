<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$base = '../';
$active_menu = 'homies';
$page_title = 'Edit Homies - Gudang';
$page_header = 'Edit Homies';
$page_icon = '&#9998;';

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, 'SELECT * FROM homies WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$homie = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$homie) {
    flash_set('Data homies tidak ditemukan.', 'danger');
    header('Location: menajemen_homies.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $cid      = trim($_POST['cid'] ?? '');
    $nomor_hp = trim($_POST['nomor_hp'] ?? '');

    if ($nama === '' || $cid === '') {
        $errors[] = 'Nama dan CID wajib diisi.';
    } else {
        $check = mysqli_prepare($conn, 'SELECT id FROM homies WHERE cid = ? AND id <> ? LIMIT 1');
        mysqli_stmt_bind_param($check, 'si', $cid, $id);
        mysqli_stmt_execute($check);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            $errors[] = 'CID sudah dipakai homies lain.';
        } else {
            $nomorHpValue = $nomor_hp === '' ? null : $nomor_hp;
            $upd = mysqli_prepare($conn, 'UPDATE homies SET nama=?, cid=?, nomor_hp=? WHERE id=?');
            mysqli_stmt_bind_param($upd, 'sssi', $nama, $cid, $nomorHpValue, $id);
            mysqli_stmt_execute($upd);
            flash_set('Data homies "' . $nama . '" berhasil diperbarui.');
            header('Location: menajemen_homies.php');
            exit;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel plain">
    <h3>Edit Homies</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_homies.php?id=<?= (int)$id ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" value="<?= e($_POST['nama'] ?? $homie['nama']) ?>" required>
            </div>
            <div class="form-group">
                <label for="cid">CID</label>
                <input type="text" id="cid" name="cid" value="<?= e($_POST['cid'] ?? $homie['cid']) ?>" required>
            </div>
            <div class="form-group">
                <label for="nomor_hp">Nomor HP <span style="font-weight:400; color:var(--text-muted);">(opsional)</span></label>
                <input type="text" id="nomor_hp" name="nomor_hp" value="<?= e($_POST['nomor_hp'] ?? $homie['nomor_hp']) ?>">
            </div>
        </div>
        <div class="form-actions-panel" style="margin-top:18px;">
            <button type="submit" class="btn-primary-form">Simpan Perubahan</button>
            <a href="menajemen_homies.php" class="btn-cancel-form" style="display:inline-flex;align-items:center;">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
