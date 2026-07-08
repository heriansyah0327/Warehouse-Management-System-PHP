<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$base = '../';
$active_menu = 'penjualan';
$active_sub = 'data_jual';
$page_title = 'Data Penjualan - Gudang';
$page_header = 'Data Penjualan';
$page_icon = '&#128202;';

const TARGET_PENJUALAN = 500;

// ---------------------------------------------------
// Kumpulkan semua "awal minggu" (Senin) yang perlu ada di daftar:
// - minggu berjalan (selalu ada walau belum ada transaksi)
// - semua minggu yang sudah punya transaksi penjualan
// Diurutkan dari yang TERBARU -> Minggu 1, lalu makin lama makin besar nomornya.
// ---------------------------------------------------
$today = new DateTime();
$seninBerjalan = (clone $today)->modify('-' . (((int)$today->format('N')) - 1) . ' days')->format('Y-m-d');

$weekStarts = [$seninBerjalan => true];
$res = mysqli_query($conn, 'SELECT DISTINCT SUBDATE(tanggal_penjualan, WEEKDAY(tanggal_penjualan)) AS awal_minggu FROM penjualan');
while ($row = mysqli_fetch_assoc($res)) { $weekStarts[$row['awal_minggu']] = true; }

$weekStartList = array_keys($weekStarts);
rsort($weekStartList); // terbaru duluan

$mingguList = [];
foreach ($weekStartList as $i => $ws) {
    $mingguList[$i + 1] = [
        'start' => $ws,
        'end'   => (new DateTime($ws))->modify('+6 days')->format('Y-m-d'),
    ];
}

// ---------------------------------------------------
// Tentukan Minggu yang dipilih (default: Minggu 1 = minggu berjalan)
// ---------------------------------------------------
$mingguParam = (int)($_GET['minggu'] ?? 1);
if (!isset($mingguList[$mingguParam])) {
    $mingguParam = 1;
}
$rangeTerpilih = $mingguList[$mingguParam];
$start = $rangeTerpilih['start'];
$end = $rangeTerpilih['end'];

$hariIni = date('Y-m-d');
$isCurrent = ($hariIni >= $start && $hariIni <= $end);

// ---------------------------------------------------
// Daftar homies (tetap/fix): tabel selalu menampilkan semua homies
// yang ada di Menajemen Homies, walau belum ada transaksi di minggu itu.
// ---------------------------------------------------
$homiesAll = [];
$res = mysqli_query($conn, 'SELECT id, nama, cid FROM homies ORDER BY nama ASC');
while ($row = mysqli_fetch_assoc($res)) { $homiesAll[] = $row; }

// ---------------------------------------------------
// Hitung rekap untuk Minggu terpilih
// ---------------------------------------------------
$proses = [];
$stmt = mysqli_prepare($conn, "SELECT id_homies, SUM(jumlah) AS total FROM penjualan
                                WHERE status = 'Proses' AND tanggal_penjualan BETWEEN ? AND ?
                                GROUP BY id_homies");
mysqli_stmt_bind_param($stmt, 'ss', $start, $end);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($r)) { $proses[(int)$row['id_homies']] = (int)$row['total']; }

$selesai = [];
$stmt2 = mysqli_prepare($conn, "SELECT id_homies, SUM(jumlah) AS total FROM penjualan
                                 WHERE status = 'Selesai' AND tanggal_penjualan BETWEEN ? AND ?
                                 GROUP BY id_homies");
mysqli_stmt_bind_param($stmt2, 'ss', $start, $end);
mysqli_stmt_execute($stmt2);
$r2 = mysqli_stmt_get_result($stmt2);
while ($row = mysqli_fetch_assoc($r2)) { $selesai[(int)$row['id_homies']] = (int)$row['total']; }

$rows = [];
foreach ($homiesAll as $h) {
    $total = $selesai[$h['id']] ?? 0;
    $rows[] = [
        'nama'     => $h['nama'],
        'cid'      => $h['cid'],
        'proses'   => $proses[$h['id']] ?? 0,
        'total'    => $total,
        'tercapai' => $total >= TARGET_PENJUALAN,
    ];
}

include __DIR__ . '/../includes/header.php';
?>

<div class="panel">
    <div class="panel-header">
        <div>
            <h3>&#128202; Rekap Penjualan <?= $mingguParam ?> (<?= e(date('d-m-Y', strtotime($start))) ?> s/d <?= e(date('d-m-Y', strtotime($end))) ?>)
                <?php if ($isCurrent): ?><span class="badge badge-pinjam">Minggu Berjalan</span><?php endif; ?>
            </h3>
            <p class="panel-desc">Target penjualan per homies: <?= TARGET_PENJUALAN ?>. Warna merah = belum tercapai, hijau = sudah tercapai.</p>
        </div>
    </div>

    <?php
    $totalMinggu = count($mingguList);
    $windowSize = 5;
    $winStart = max(1, $mingguParam - 2);
    $winEnd = min($totalMinggu, $winStart + $windowSize - 1);
    $winStart = max(1, $winEnd - $windowSize + 1);
    $adaPrev = $mingguParam > 1;
    $adaNext = $mingguParam < $totalMinggu;
    ?>
    <div class="pagination-toolbar">
        <span></span>
        <div class="pagination">
            <?php if ($adaPrev): ?>
                <a href="?minggu=<?= $mingguParam - 1 ?>">&laquo;</a>
            <?php else: ?>
                <span class="pagination-spacer">&laquo;</span>
            <?php endif; ?>

            <?php for ($n = $winStart; $n <= $winEnd; $n++): ?>
                <?php $w = $mingguList[$n]; ?>
                <a href="?minggu=<?= $n ?>"
                   class="<?= $n === $mingguParam ? 'active' : '' ?>"
                   title="<?= e(date('d-m-Y', strtotime($w['start']))) ?> s/d <?= e(date('d-m-Y', strtotime($w['end']))) ?><?= $n === 1 ? ' (Berjalan)' : '' ?>">
                    <?= $n ?>
                </a>
            <?php endfor; ?>

            <?php if ($adaNext): ?>
                <a href="?minggu=<?= $mingguParam + 1 ?>">&raquo;</a>
            <?php else: ?>
                <span class="pagination-spacer">&raquo;</span>
            <?php endif; ?>
        </div>

        <select id="filterStatus" class="select-sort">
            <option value="semua">Semua</option>
            <option value="tercapai">Tercapai</option>
            <option value="belum">Belum Tercapai</option>
        </select>
    </div>

    <table class="data-table" id="dataPenjualanTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Homies</th>
                <th>CID</th>
                <th>Proses Menjual</th>
                <th>Total Penjualan</th>
                <th>Target Penjualan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted);">Belum ada homies terdaftar.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $i => $r): ?>
                <tr data-status="<?= $r['tercapai'] ? 'tercapai' : 'belum' ?>">
                    <td><?= $i + 1 ?></td>
                    <td><?= e($r['nama']) ?></td>
                    <td><?= e($r['cid']) ?></td>
                    <td><?= (int)$r['proses'] ?></td>
                    <td><?= (int)$r['total'] ?></td>
                    <td>
                        <span class="badge <?= $r['tercapai'] ? 'badge-kembali' : 'badge-senjata' ?>">
                            <?= (int)$r['total'] ?> / <?= TARGET_PENJUALAN ?> &mdash; <?= $r['tercapai'] ? 'Tercapai' : 'Belum Tercapai' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="table-footer">
        <span id="dataPenjualanCount">Menampilkan <?= count($rows) ?> Homies</span>
    </div>
</div>

<script>
(function () {
    var statusSelect = document.getElementById('filterStatus');
    var countLabel = document.getElementById('dataPenjualanCount');
    var rows = document.querySelectorAll('#dataPenjualanTable tbody tr[data-status]');

    function applyFilters() {
        var status = statusSelect.value;
        var visible = 0;
        rows.forEach(function (row) {
            var show = status === 'semua' || row.getAttribute('data-status') === status;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (countLabel) countLabel.textContent = 'Menampilkan ' + visible + ' Homies';
    }

    if (statusSelect) statusSelect.addEventListener('change', applyFilters);
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
