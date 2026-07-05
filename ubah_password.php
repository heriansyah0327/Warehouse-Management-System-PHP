<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$base = '';
$active_menu = '';
$page_title = 'Ubah Password - Gudang';
$page_header = 'Ubah Password';
$page_icon = '&#128273;';

$errors = [];
$uid = current_user()['id'];

$stmt = mysqli_prepare($conn, 'SELECT password FROM users WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $uid);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$currentPasswordInDb = $row['password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = trim($_POST['old_password'] ?? '');
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if ($old === '' || $new === '' || $confirm === '') {
        $errors[] = 'Semua kolom wajib diisi.';
    } elseif ($old !== $currentPasswordInDb) {
        $errors[] = 'Password lama yang kamu masukkan salah.';
    } elseif ($new !== $confirm) {
        $errors[] = 'Konfirmasi password baru tidak cocok.';
    } else {
        $upd = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?');
        mysqli_stmt_bind_param($upd, 'si', $new, $uid);
        mysqli_stmt_execute($upd);
        flash_set('Password berhasil diubah.');
        header('Location: dashboard.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="form-panel plain" style="max-width:520px;">
    <h3>Ubah Password</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="ubah_password.php">
        <div class="form-group">
            <label for="old_password">Password Lama</label>
            <input type="password" id="old_password" name="old_password" placeholder="Masukkan password lama ..." required>
        </div>
        <div class="form-group">
            <label for="new_password">Password Baru</label>
            <input type="password" id="new_password" name="new_password" placeholder="Masukkan password baru ..." required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password Baru</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru ..." required>
        </div>
        <div class="form-actions-panel">
            <button type="submit" class="btn-primary-form">Simpan Password</button>
            <a href="dashboard.php" class="btn-cancel-form" style="display:inline-flex;align-items:center;">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
