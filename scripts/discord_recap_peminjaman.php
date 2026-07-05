<?php
// =====================================================
// Rekap Harian "Daftar Peminjaman Aktif" -> Discord
// Dijalankan otomatis via Cron Job / Task Scheduler
// =====================================================

require_once __DIR__ . '/../config/db.php';

// -----------------------------------------------------
// 1. Isi URL webhook Discord kamu di sini
// -----------------------------------------------------
$DISCORD_WEBHOOK_URL = 'https://discord.com/api/webhooks/1523253436697542766/2KsXYcUGV7EiNNCgTFOedZKlJe_tK55f-fkNR6vDm0ll7vWOYwJDZnnh4EeHUUNkykDf';

// -----------------------------------------------------
// 2. Ambil data peminjaman aktif (sama seperti peminjaman.php)
// -----------------------------------------------------
$aktifList = [];
$res = mysqli_query($conn, "SELECT p.id, p.jumlah, p.tanggal_pinjam, h.nama AS nama_homies, h.cid,
                                    k.nama_barang, k.class_senjata
                             FROM peminjaman p
                             JOIN homies h ON h.id = p.id_homies
                             JOIN kategori_barang k ON k.id = p.id_barang
                             WHERE p.status = 'Dipinjam'
                             ORDER BY p.tanggal_pinjam ASC, p.id ASC");
while ($row = mysqli_fetch_assoc($res)) { $aktifList[] = $row; }

// -----------------------------------------------------
// 3. Susun isi pesan Discord (embed)
// -----------------------------------------------------
$tanggalHariIni = date('d-m-Y');

if (empty($aktifList)) {
    $description = 'Tidak ada senjata yang sedang dipinjam saat ini. Semua sudah dikembalikan.';
} else {
    $lines = [];
    foreach ($aktifList as $i => $p) {
        $lines[] = ($i + 1) . '. **' . $p['nama_homies'] . '** (<@' . $p['cid'] . '>) - '
                  . $p['nama_barang'] . ' x**' . (int)$p['jumlah'] . '**'
                  . ', dipinjam sejak ' . date('d-m-Y', strtotime($p['tanggal_pinjam']));
    }
    $description = implode("\n", $lines);
}
$payload = [
    'username' => 'Gudang Bot',
    'embeds' => [[
        'title' => '📋 Rekap Peminjaman Aktif — ' . $tanggalHariIni,
        'description' => $description,
        'color' => empty($aktifList) ? 3066993 : 15105570, // hijau kalau kosong, oranye kalau ada
        'footer' => ['text' => 'Total senjata belum dikembalikan: ' . count($aktifList)],
    ]],
];

// -----------------------------------------------------
// 4. Kirim ke Discord via cURL
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
// 5. Log hasil kirim (opsional, buat debugging cron)
// -----------------------------------------------------
$logLine = '[' . date('Y-m-d H:i:s') . '] HTTP ' . $httpCode . ' - ' . count($aktifList) . ' peminjaman aktif' . PHP_EOL;
file_put_contents(__DIR__ . '/discord_recap.log', $logLine, FILE_APPEND);

echo $httpCode === 204 ? "Rekap berhasil dikirim ke Discord.\n" : "Gagal kirim rekap. HTTP $httpCode. Response: $response\n";
