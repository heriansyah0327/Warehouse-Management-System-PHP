<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_admin();

$base = '../';
$active_menu = 'staff';
$page_title = 'Edit Staff - Gudang';
$page_header = 'Edit Akun';
$page_icon = '&#9998;';

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$akun = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$akun) {
    flash_set('Data akun tidak ditemukan.', 'danger');
    header('Location: management_staff.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $role      = $_POST['role'] ?? 'staff';
    if ($role !== 'admin' && $role !== 'staff') $role = 'staff';

    if ($username === '' || $full_name === '') {
        $errors[] = 'Nama lengkap dan username wajib diisi.';
    } else {
        $check = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1');
        mysqli_stmt_bind_param($check, 'si', $username, $id);
        mysqli_stmt_execute($check);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            $errors[] = 'Username sudah dipakai akun lain.';
        } else {
            // Jangan izinkan admin mengubah role akun sendiri (mencegah admin ke-lock out)
            if ((int)$akun['id'] === (int)current_user()['id']) {
                $role = $akun['role'];
            }

            if ($password !== '') {
                $upd = mysqli_prepare($conn, 'UPDATE users SET username=?, full_name=?, role=?, password=? WHERE id=?');
                mysqli_stmt_bind_param($upd, 'ssssi', $username, $full_name, $role, $password, $id);
            } else {
                $upd = mysqli_prepare($conn, 'UPDATE users SET username=?, full_name=?, role=? WHERE id=?');
                mysqli_stmt_bind_param($upd, 'sssi', $username, $full_name, $role, $id);
            }
            mysqli_stmt_execute($upd);

            // Sinkronkan session kalau admin sedang mengedit akunnya sendiri
            if ((int)$akun['id'] === (int)current_user()['id']) {
                $_SESSION['username']  = $username;
                $_SESSION['full_name'] = $full_name;
            }

            flash_set('Data akun "' . $full_name . '" berhasil diperbarui.');
            header('Location: management_staff.php');
            exit;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel plain">
    <h3>Edit Akun</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_staff.php?id=<?= (int)$id ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" value="<?= e($_POST['full_name'] ?? $akun['full_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" <?= ((int)$akun['id'] === (int)current_user()['id']) ? 'disabled' : '' ?>>
                    <option value="staff" <?= ($akun['role'] === 'staff') ? 'selected' : '' ?>>Staff</option>
                    <option value="admin" <?= ($akun['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
                <?php if ((int)$akun['id'] === (int)current_user()['id']): ?>
                    <small style="color:var(--text-muted);">Role akun sendiri tidak bisa diubah dari sini.</small>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= e($_POST['username'] ?? $akun['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password Baru (kosongkan jika tidak diubah)</label>
                <input type="text" id="password" name="password" placeholder="Masukkan password baru ...">
            </div>
        </div>
        <div class="form-actions-panel" style="margin-top:18px;">
            <button type="submit" class="btn-primary-form">Simpan Perubahan</button>
            <a href="management_staff.php" class="btn-cancel-form" style="display:inline-flex;align-items:center;">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
