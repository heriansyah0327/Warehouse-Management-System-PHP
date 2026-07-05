<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_admin();

$base = '../';
$active_menu = 'staff';
$page_title = 'Management Staff - Gudang';
$page_header = 'Management Staff';
$page_icon = '&#128100;';

$errors = [];

// ---------------------------------------------------
// Proses tambah akun staff/admin baru
// ---------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $username  = trim($_POST['username'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role      = $_POST['role'] ?? 'staff';

    if ($role !== 'admin' && $role !== 'staff') {
        $role = 'staff';
    }

    if ($username === '' || $password === '' || $full_name === '') {
        $errors[] = 'Semua kolom wajib diisi.';
    } else {
        $check = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? LIMIT 1');
        mysqli_stmt_bind_param($check, 's', $username);
        mysqli_stmt_execute($check);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            $errors[] = 'Username sudah dipakai, pilih username lain.';
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssss', $username, $password, $full_name, $role);
            mysqli_stmt_execute($stmt);
            flash_set('Akun ' . ($role === 'admin' ? 'admin' : 'staff') . ' "' . $full_name . '" berhasil ditambahkan.');
            header('Location: management_staff.php');
            exit;
        }
    }
}

// ---------------------------------------------------
// Ambil semua akun
// ---------------------------------------------------
$users = [];
$res = mysqli_query($conn, 'SELECT id, username, full_name, role, created_at FROM users ORDER BY role ASC, full_name ASC');
while ($row = mysqli_fetch_assoc($res)) {
    $users[] = $row;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel">
    <h3>Tambah Akun Staff / Admin</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="management_staff.php">
        <div class="form-grid">
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" placeholder="Masukkan Nama Lengkap ..." value="<?= e($_POST['full_name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="staff" <?= (($_POST['role'] ?? '') === 'staff') ? 'selected' : '' ?>>Staff</option>
                    <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan Username ..." value="<?= e($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="text" id="password" name="password" placeholder="Masukkan Password ..." required>
            </div>
        </div>
        <div class="form-actions-panel" style="margin-top:18px;">
            <button type="submit" name="add_staff" value="1" class="btn-primary-form">Simpan</button>
            <button type="reset" class="btn-cancel-form">Batal</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <h3>&#128100; Data Staff & Admin</h3>
    </div>
    <div class="panel-toolbar">
        <span></span>
        <div class="search-box">
            <input type="text" placeholder="Cari ..." data-table-search="#staffTable">
            <span class="icon">&#128269;</span>
        </div>
    </div>

    <table class="data-table" id="staffTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Belum ada data akun.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $i => $usr): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($usr['full_name']) ?></td>
                    <td><?= e($usr['username']) ?></td>
                    <td><?= $usr['role'] === 'admin' ? 'Administrator' : 'Kepala Staff' ?></td>
                    <td class="aksi-cell">
                        <a class="btn-icon edit" href="edit_staff.php?id=<?= (int)$usr['id'] ?>" title="Edit">&#9998;</a>
                        <?php if ((int)$usr['id'] !== (int)current_user()['id']): ?>
                            <button type="button" class="btn-icon delete" title="Hapus"
                                data-delete-url="hapus_staff.php?id=<?= (int)$usr['id'] ?>"
                                data-delete-label="akun &quot;<?= e($usr['full_name']) ?>&quot;">&#128465;</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span>Jumlah Akun: <?= count($users) ?> akun</span>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
