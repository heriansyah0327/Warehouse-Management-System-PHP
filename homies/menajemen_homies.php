<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$base = '../';
$active_menu = 'homies';
$page_title = 'Management Homies - Gudang';
$page_header = 'Management Homies';
$page_icon = '&#128101;';

$errors = [];

// ---------------------------------------------------
// Proses tambah homies baru
// ---------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_homies'])) {
    $nama     = trim($_POST['nama'] ?? '');
    $cid      = trim($_POST['cid'] ?? '');
    $nomor_hp = trim($_POST['nomor_hp'] ?? '');

    if ($nama === '' || $cid === '') {
        $errors[] = 'Nama dan CID wajib diisi.';
    } else {
        $check = mysqli_prepare($conn, 'SELECT id FROM homies WHERE cid = ? LIMIT 1');
        mysqli_stmt_bind_param($check, 's', $cid);
        mysqli_stmt_execute($check);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            $errors[] = 'CID sudah dipakai homies lain.';
        } else {
            $nomorHpValue = $nomor_hp === '' ? null : $nomor_hp;
            $stmt = mysqli_prepare($conn, 'INSERT INTO homies (nama, cid, nomor_hp) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sss', $nama, $cid, $nomorHpValue);
            mysqli_stmt_execute($stmt);
            flash_set('Homies "' . $nama . '" berhasil ditambahkan.');
            header('Location: menajemen_homies.php');
            exit;
        }
    }
}

// ---------------------------------------------------
// Ambil semua homies
// ---------------------------------------------------
$homiesList = [];
$res = mysqli_query($conn, 'SELECT id, nama, cid, nomor_hp, created_at FROM homies ORDER BY nama ASC');
while ($row = mysqli_fetch_assoc($res)) {
    $homiesList[] = $row;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel">
    <h3>Tambah Homies</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="menajemen_homies.php">
        <div class="form-grid">
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" placeholder="Masukkan Nama ..." value="<?= e($_POST['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="cid">CID</label>
                <input type="text" id="cid" name="cid" placeholder="Masukkan CID ..." value="<?= e($_POST['cid'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="nomor_hp">Nomor HP <span style="font-weight:400; color:var(--text-muted);">(opsional)</span></label>
                <input type="text" id="nomor_hp" name="nomor_hp" placeholder="Masukkan Nomor HP ..." value="<?= e($_POST['nomor_hp'] ?? '') ?>">
            </div>
        </div>
        <div class="form-actions-panel" style="margin-top:18px;">
            <button type="submit" name="add_homies" value="1" class="btn-primary-form">Simpan</button>
            <button type="reset" class="btn-cancel-form">Batal</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <h3>&#128101; Data Homies</h3>
    </div>
    <div class="panel-toolbar">
        <span></span>
        <div class="search-box">
            <input type="text" placeholder="Cari ..." data-table-search="#homiesTable">
            <span class="icon">&#128269;</span>
        </div>
    </div>

    <table class="data-table" id="homiesTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>CID</th>
                <th>Nomor HP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($homiesList)): ?>
                <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Belum ada data homies.</td></tr>
            <?php else: ?>
                <?php foreach ($homiesList as $i => $h): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($h['nama']) ?></td>
                    <td><?= e($h['cid']) ?></td>
                    <td><?= $h['nomor_hp'] !== null && $h['nomor_hp'] !== '' ? e($h['nomor_hp']) : '<span style="color:var(--text-muted);">-</span>' ?></td>
                    <td class="aksi-cell">
                        <a class="btn-icon edit" href="edit_homies.php?id=<?= (int)$h['id'] ?>" title="Edit">&#9998;</a>
                        <button type="button" class="btn-icon delete" title="Hapus"
                            data-delete-url="hapus_homies.php?id=<?= (int)$h['id'] ?>"
                            data-delete-label="homies &quot;<?= e($h['nama']) ?>&quot;">&#128465;</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span>Jumlah Homies: <?= count($homiesList) ?> Orang</span>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
