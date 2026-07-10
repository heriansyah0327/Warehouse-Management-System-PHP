<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$base = '../';
$active_menu = 'laporan';
$active_sub = 'lap_penjualan';
$page_title = 'Laporan Penjualan - Gudang';
$page_header = 'Laporan Penjualan';
$page_icon = '&#128203;';

$alasan = $_GET['alasan'] ?? '';
$dari   = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

// ---------------------------------------------------
// Bangun query gabungan: setiap baris penjualan menghasilkan
// event "Jual" (tanggal_penjualan), dan kalau sudah dikonfirmasi
// selesai juga menghasilkan event "Selesai" (tanggal_selesai).
// Alasan otomatis mengikuti jenis event ini.
// ---------------------------------------------------
$whereJual = [];
$paramsJual = [];
$typesJual = '';
if ($dari !== '') { $whereJual[] = 'p.tanggal_penjualan >= ?'; $paramsJual[] = $dari; $typesJual .= 's'; }
if ($sampai !== '') { $whereJual[] = 'p.tanggal_penjualan <= ?'; $paramsJual[] = $sampai; $typesJual .= 's'; }

$whereSelesai = ["p.status = 'Selesai'"];
$paramsSelesai = [];
$typesSelesai = '';
if ($dari !== '') { $whereSelesai[] = 'p.tanggal_selesai >= ?'; $paramsSelesai[] = $dari; $typesSelesai .= 's'; }
if ($sampai !== '') { $whereSelesai[] = 'p.tanggal_selesai <= ?'; $paramsSelesai[] = $sampai; $typesSelesai .= 's'; }

$sqlJual = "SELECT p.id, p.tanggal_penjualan AS tanggal, 'Jual' AS alasan, p.diinput_oleh AS oleh,
                   h.nama, h.cid, k.nama_barang, k.tag_narko, p.jumlah
            FROM penjualan p
            JOIN homies h ON h.id = p.id_homies
            JOIN kategori_barang k ON k.id = p.id_barang";
if ($whereJual) { $sqlJual .= ' WHERE ' . implode(' AND ', $whereJual); }

$sqlSelesai = "SELECT p.id, p.tanggal_selesai AS tanggal, 'Selesai' AS alasan, p.diselesaikan_oleh AS oleh,
                      h.nama, h.cid, k.nama_barang, k.tag_narko, p.jumlah
               FROM penjualan p
               JOIN homies h ON h.id = p.id_homies
               JOIN kategori_barang k ON k.id = p.id_barang
               WHERE " . implode(' AND ', $whereSelesai);

$params = array_merge($paramsJual, $paramsSelesai);
$types = $typesJual . $typesSelesai;

$sql = "SELECT * FROM (($sqlJual) UNION ALL ($sqlSelesai)) t";
if ($alasan === 'Jual' || $alasan === 'Selesai') {
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
$totalJual = 0;
$totalSelesai = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    if ($row['alasan'] === 'Jual') {
        $totalJual += (int)$row['jumlah'];
    } else {
        $totalSelesai += (int)$row['jumlah'];
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-panel">
    <h3>Filter Laporan</h3>
    <form method="GET" action="laporan_penjualan.php" class="filter-row">
        <div class="form-group">
            <label for="alasan">Alasan</label>
            <select id="alasan" name="alasan">
                <option value="">Semua</option>
                <option value="Jual" <?= $alasan === 'Jual' ? 'selected' : '' ?>>Jual</option>
                <option value="Selesai" <?= $alasan === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
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
            <a href="laporan_penjualan.php" class="btn-cancel-form" style="display:inline-flex;align-items:center;padding:10px 22px;">Reset</a>
        </div>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <h3>&#128203; Riwayat Penjualan</h3>
    </div>
    <div class="panel-toolbar">
        <span></span>
        <div class="search-box">
            <input type="text" placeholder="Cari ..." data-table-search="#laporanJualTable">
            <span class="icon">&#128269;</span>
        </div>
    </div>

    <table class="data-table" id="laporanJualTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Homies</th>
                <th>CID</th>
                <th>Nama Narko</th>
                <th>Detail</th>
                <th>Jumlah</th>
                <th>Alasan</th>
                <th>Diinput Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="9" style="text-align:center; color:var(--text-muted);">Belum ada data penjualan.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e(date('d-m-Y', strtotime($r['tanggal']))) ?></td>
                    <td><?= e($r['nama']) ?></td>
                    <td><?= e($r['cid']) ?></td>
                    <td><?= e($r['nama_barang']) ?></td>
                    <td><span class="badge badge-narko"><?= e($r['tag_narko']) ?></span></td>
                    <td><?= (int)$r['jumlah'] ?></td>
                    <td>
                        <?php if ($r['alasan'] === 'Jual'): ?>
                            <span class="badge badge-pinjam">Jual</span>
                        <?php else: ?>
                            <span class="badge badge-kembali">Selesai</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $r['oleh'] !== null && $r['oleh'] !== '' ? e($r['oleh']) : '<span style="color:var(--text-muted);">-</span>' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span>Total Baris: <?= count($rows) ?> | Total Terjual: <?= $totalJual ?> | Total Selesai: <?= $totalSelesai ?></span>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
