<?php
// =====================================================
// Rekap Mingguan "Data Penjualan" -> Discord
// Cuma menampilkan homies yang BELUM mencapai target penjualan
// minggu ini. Yang sudah tercapai tidak perlu di-notice lagi.
// Dijalankan otomatis via Cron Job / Task Scheduler
// =====================================================

require_once __DIR__ . '/../config/db.php';

// -----------------------------------------------------
// 1. Isi URL webhook Discord kamu di sini
// -----------------------------------------------------
$DISCORD_WEBHOOK_URL = 'ISI_URL_WEBHOOK_DISCORD_DI_SINI';

const TARGET_PENJUALAN = 500;

// -----------------------------------------------------
// 2. Tentukan rentang Minggu berjalan (Senin s/d Minggu),
//    sama persis seperti logika di penjualan/data_penjualan.php
// -----------------------------------------------------
$today = new DateTime();
$mulaiMinggu = (clone $today)->modify('-' . (((int)$today->format('N')) - 1) . ' days')->format('Y-m-d');
$akhirMinggu = (new DateTime($mulaiMinggu))->modify('+6 days')->format('Y-m-d');

// -----------------------------------------------------
// 3. Ambil semua homies + total penjualan (status Selesai)
//    minggu ini, urut dari yang paling kecil total-nya
// -----------------------------------------------------
$homiesAll = [];
$res = mysqli_query($conn, 'SELECT id, nama, cid FROM homies ORDER BY nama ASC');
while ($row = mysqli_fetch_assoc($res)) { $homiesAll[] = $row; }

$totalPerHomies = [];
$stmt = mysqli_prepare($conn, "SELECT id_homies, SUM(jumlah) AS total FROM penjualan
                                WHERE status = 'Selesai' AND tanggal_penjualan BETWEEN ? AND ?
                                GROUP BY id_homies");
mysqli_stmt_bind_param($stmt, 'ss', $mulaiMinggu, $akhirMinggu);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($r)) { $totalPerHomies[(int)$row['id_homies']] = (int)$row['total']; }

// -----------------------------------------------------
// 4. Filter: cuma yang BELUM tercapai (< TARGET_PENJUALAN)
// -----------------------------------------------------
$belumTercapai = [];
foreach ($homiesAll as $h) {
    $total = $totalPerHomies[$h['id']] ?? 0;
    if ($total < TARGET_PENJUALAN) {
        $belumTercapai[] = [
            'nama'  => $h['nama'],
            'cid'   => $h['cid'],
            'total' => $total,
        ];
    }
}
// Urutkan dari yang paling jauh dari target ke yang paling dekat
usort($belumTercapai, function ($a, $b) { return $a['total'] <=> $b['total']; });

// -----------------------------------------------------
// 5. Susun isi pesan Discord (embed)
// -----------------------------------------------------
$rangeLabel = date('d-m-Y', strtotime($mulaiMinggu)) . ' s/d ' . date('d-m-Y', strtotime($akhirMinggu));

if (empty($belumTercapai)) {
    $description = 'Semua homies sudah mencapai target penjualan minggu ini. 🎉';
} else {
    $lines = [];
    foreach ($belumTercapai as $i => $p) {
        $lines[] = ($i + 1) . '. **' . $p['nama'] . '** (<@' . $p['cid'] . '>) - '
                  . $p['total'] . '/' . TARGET_PENJUALAN;
    }
    $description = implode("\n", $lines);
}

$payload = [
    'username' => 'Gudang Bot',
    'embeds' => [[
        'title' => '📊 Rekap Penjualan Minggu Ini (' . $rangeLabel . ')',
        'description' => $description,
        'color' => empty($belumTercapai) ? 3066993 : 15158332, // hijau kalau semua tercapai, merah kalau masih ada yang kurang
        'footer' => ['text' => 'Belum mencapai target: ' . count($belumTercapai) . ' dari ' . count($homiesAll) . ' homies'],
    ]],
];

// -----------------------------------------------------
// 6. Kirim ke Discord via cURL
// -----------------------------------------------------
$ch = curl_init($DISCORD_WEBHOOK_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// -----------------------------------------------------
// 7. Log hasil kirim (opsional, buat debugging cron)
// -----------------------------------------------------
$logLine = '[' . date('Y-m-d H:i:s') . '] HTTP ' . $httpCode . ' - ' . count($belumTercapai) . ' homies belum capai target' . PHP_EOL;
file_put_contents(__DIR__ . '/discord_recap.log', $logLine, FILE_APPEND);

echo $httpCode === 204 ? "Rekap penjualan berhasil dikirim ke Discord.\n" : "Gagal kirim rekap. HTTP $httpCode. Response: $response\n";
