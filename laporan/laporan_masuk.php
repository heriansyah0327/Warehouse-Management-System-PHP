<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$base = '../';
$active_menu = 'laporan';
$active_sub = 'lap_masuk';
$page_title = 'Laporan Barang Masuk - Gudang';
$page_header = 'Laporan Barang Masuk';
$page_icon = '&#128203;';

$jenis = $_GET['jenis'] ?? '';
$dari  = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$where = [];
$params = [];
$types = '';

if (in_array($jenis, ['Senjata', 'Narko', 'Lainnya'], true)) {
    $where[] = 'k.jenis = ?';
    $params[] = $jenis;
    $types .= 's';
}
if ($dari !== '') {
    $where[] = 'bm.tanggal >= ?';
    $params[] = $dari;
    $types .= 's';
}
if ($sampai !== '') {
    $where[] = 'bm.tanggal <= ?';
    $params[] = $sampai;
    $types .= 's';
}

$sql = 'SELECT bm.id, bm.jumlah, bm.alasan, bm.tanggal, bm.diinput_oleh, k.jenis, k.nama_barang, k.class_senjata, k.tag_narko, k.tag_lainnya
        FROM barang_masuk bm
        JOIN kategori_barang k ON k.id = bm.id_barang';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY bm.tanggal DESC, bm.id DESC';

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$rows = [];
$totalJumlah = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    $totalJumlah += (int)$row['jumlah'];
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel">
    <h3>Filter Laporan</h3>
    <form method="GET" action="laporan_masuk.php" class="filter-row">
        <div class="form-group">
            <label for="jenis">Kategori</label>
            <select id="jenis" name="jenis">
                <option value="">Semua</option>
                <option value="Senjata" <?= $jenis === 'Senjata' ? 'selected' : '' ?>>Senjata</option>
                <option value="Narko" <?= $jenis === 'Narko' ? 'selected' : '' ?>>Narko</option>
                <option value="Lainnya" <?= $jenis === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
            </select>
        </div>
        <div class="form-group">
            <label for="dari">Dari Tanggal</label>
            <input type="date" id="dari" name="dari" value="<?= e($dari) ?>">
        </div>
        <div class="form-group">
            <label for="sampai">Sampai Tanggal</label>
            <input type="date" id="sampai" name="sampai" value="<?= e($sampai) ?>">
        </div>
        <div class="form-group">
            <button type="submit" class="btn-primary-form" style="background:#fff;color:var(--blue-accent);padding:10px 22px;border-radius:var(--radius-sm);font-weight:700;border:none;">Terapkan</button>
        </div>
        <?php if ($jenis !== '' || $dari !== '' || $sampai !== ''): ?>
        <div class="form-group">
            <a href="laporan_masuk.php" class="btn-cancel-form" style="display:inline-flex;align-items:center;padding:10px 22px;">Reset</a>
        </div>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <h3>&#128203; Riwayat Barang Masuk</h3>
    </div>
    <div class="panel-toolbar">
        <span></span>
        <div class="search-box">
            <input type="text" placeholder="Cari ..." data-table-search="#laporanMasukTable">
            <span class="icon">&#128269;</span>
        </div>
    </div>

    <table class="data-table" id="laporanMasukTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Nama Barang</th>
                <th>Detail</th>
                <th>Jumlah Masuk</th>
                <th>Alasan</th>
                <th>Diinput Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="8" style="text-align:center; color:var(--text-muted);">Belum ada data barang masuk.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e(date('d-m-Y', strtotime($r['tanggal']))) ?></td>
                    <td>
                        <?php if ($r['jenis'] === 'Senjata'): ?>
                            <span class="badge badge-senjata">Senjata</span>
                        <?php elseif ($r['jenis'] === 'Narko'): ?>
                            <span class="badge badge-narko">Narko</span>
                        <?php else: ?>
                            <span class="badge badge-lainnya">Lainnya</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($r['nama_barang']) ?></td>
                    <td><span class="badge badge-chip"><?= e($r['class_senjata'] ?? $r['tag_narko'] ?? $r['tag_lainnya'] ?? '-') ?></span></td>
                    <td>+<?= (int)$r['jumlah'] ?></td>
                    <td><?= $r['alasan'] !== null && $r['alasan'] !== '' ? e($r['alasan']) : '<span style="color:var(--text-muted);">-</span>' ?></td>
                    <td><?= e($r['diinput_oleh']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span>Total Baris: <?= count($rows) ?> | Total Jumlah Masuk: <?= $totalJumlah ?></span>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>