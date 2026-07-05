<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$base = '../';
$active_menu = 'laporan';
$active_sub = 'lap_pinjam';
$page_title = 'Laporan Peminjaman - Gudang';
$page_header = 'Laporan Peminjaman';
$page_icon = '&#128203;';

$alasan = $_GET['alasan'] ?? '';
$dari   = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

// ---------------------------------------------------
// Bangun query gabungan: setiap baris peminjaman menghasilkan
// event "Pinjam" (tanggal_pinjam), dan kalau sudah dikembalikan
// juga menghasilkan event "Dikembalikan" (tanggal_kembali).
// Alasan otomatis mengikuti jenis event ini.
// ---------------------------------------------------
$wherePinjam = [];
$paramsPinjam = [];
$typesPinjam = '';
if ($dari !== '') { $wherePinjam[] = 'p.tanggal_pinjam >= ?'; $paramsPinjam[] = $dari; $typesPinjam .= 's'; }
if ($sampai !== '') { $wherePinjam[] = 'p.tanggal_pinjam <= ?'; $paramsPinjam[] = $sampai; $typesPinjam .= 's'; }

$whereKembali = ["p.status = 'Dikembalikan'"];
$paramsKembali = [];
$typesKembali = '';
if ($dari !== '') { $whereKembali[] = 'p.tanggal_kembali >= ?'; $paramsKembali[] = $dari; $typesKembali .= 's'; }
if ($sampai !== '') { $whereKembali[] = 'p.tanggal_kembali <= ?'; $paramsKembali[] = $sampai; $typesKembali .= 's'; }

$sqlPinjam = "SELECT p.id, p.tanggal_pinjam AS tanggal, 'Pinjam' AS alasan, p.diinput_oleh AS oleh,
                     h.nama, h.cid, k.nama_barang, k.class_senjata, p.jumlah
              FROM peminjaman p
              JOIN homies h ON h.id = p.id_homies
              JOIN kategori_barang k ON k.id = p.id_barang";
if ($wherePinjam) { $sqlPinjam .= ' WHERE ' . implode(' AND ', $wherePinjam); }

$sqlKembali = "SELECT p.id, p.tanggal_kembali AS tanggal, 'Dikembalikan' AS alasan, p.dikembalikan_oleh AS oleh,
                      h.nama, h.cid, k.nama_barang, k.class_senjata, p.jumlah
               FROM peminjaman p
               JOIN homies h ON h.id = p.id_homies
               JOIN kategori_barang k ON k.id = p.id_barang
               WHERE " . implode(' AND ', $whereKembali);

$params = array_merge($paramsPinjam, $paramsKembali);
$types = $typesPinjam . $typesKembali;

$sql = "SELECT * FROM (($sqlPinjam) UNION ALL ($sqlKembali)) t";
if ($alasan === 'Pinjam' || $alasan === 'Dikembalikan') {
    $sql .= ' WHERE t.alasan = ?';
    $params[] = $alasan;
    $types .= 's';
}
$sql .= ' ORDER BY t.tanggal DESC, t.id DESC';

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$rows = [];
$totalPinjam = 0;
$totalKembali = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    if ($row['alasan'] === 'Pinjam') {
        $totalPinjam += (int)$row['jumlah'];
    } else {
        $totalKembali += (int)$row['jumlah'];
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel">
    <h3>Filter Laporan</h3>
    <form method="GET" action="laporan_peminjaman.php" class="filter-row">
        <div class="form-group">
            <label for="alasan">Alasan</label>
            <select id="alasan" name="alasan">
                <option value="">Semua</option>
                <option value="Pinjam" <?= $alasan === 'Pinjam' ? 'selected' : '' ?>>Pinjam</option>
                <option value="Dikembalikan" <?= $alasan === 'Dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
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
        <?php if ($alasan !== '' || $dari !== '' || $sampai !== ''): ?>
        <div class="form-group">
            <a href="laporan_peminjaman.php" class="btn-cancel-form" style="display:inline-flex;align-items:center;padding:10px 22px;">Reset</a>
        </div>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <h3>&#128203; Riwayat Peminjaman</h3>
    </div>
    <div class="panel-toolbar">
        <span></span>
        <div class="search-box">
            <input type="text" placeholder="Cari ..." data-table-search="#laporanPinjamTable">
            <span class="icon">&#128269;</span>
        </div>
    </div>

    <table class="data-table" id="laporanPinjamTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Homies</th>
                <th>CID</th>
                <th>Nama Senjata</th>
                <th>Detail</th>
                <th>Jumlah</th>
                <th>Alasan</th>
                <th>Diinput Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="9" style="text-align:center; color:var(--text-muted);">Belum ada data peminjaman.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e(date('d-m-Y', strtotime($r['tanggal']))) ?></td>
                    <td><?= e($r['nama']) ?></td>
                    <td><?= e($r['cid']) ?></td>
                    <td><?= e($r['nama_barang']) ?></td>
                    <td><span class="badge badge-chip"><?= e($r['class_senjata']) ?></span></td>
                    <td><?= $r['alasan'] === 'Pinjam' ? '-' : '+' ?><?= (int)$r['jumlah'] ?></td>
                    <td>
                        <?php if ($r['alasan'] === 'Pinjam'): ?>
                            <span class="badge badge-pinjam">Pinjam</span>
                        <?php else: ?>
                            <span class="badge badge-kembali">Dikembalikan</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $r['oleh'] !== null && $r['oleh'] !== '' ? e($r['oleh']) : '<span style="color:var(--text-muted);">-</span>' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span>Total Baris: <?= count($rows) ?> | Total Dipinjam: <?= $totalPinjam ?> | Total Dikembalikan: <?= $totalKembali ?></span>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
