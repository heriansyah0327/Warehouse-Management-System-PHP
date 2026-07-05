<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$base = '';
$active_menu = 'dashboard';
$page_title = 'Dashboard - Gudang';
$page_header = 'Dashboard';
$page_icon = '&#9635;';

// Hitung jumlah member (homies) & jumlah staff (akun admin + staff)
$jumlahMember = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM homies"))['c'] ?? 0;
$jumlahStaff  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users"))['c'] ?? 0;

include __DIR__ . '/includes/header.php';
?>

<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-icon">&#128101;</div>
            <div class="stat-text">
                <div class="label">Jumlah Homies</div>
                <div class="value"><?= (int)$jumlahMember ?></div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-icon">&#128100;</div>
            <div class="stat-text">
                <div class="label">Jumlah Staff (Admin & Staff)</div>
                <div class="value"><?= (int)$jumlahStaff ?></div>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h3>&#128337; Selamat datang</h3>
    </div>
    <p>Login berhasil sebagai <strong><?= e(current_user()['full_name']) ?></strong> (<?= e(role_label()) ?>).</p>
    <p style="color:var(--text-muted); font-size:13.5px;">
        Dashboard
    </p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
