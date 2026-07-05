<?php
// Variabel yang diharapkan sudah di-set oleh halaman pemanggil:
// $base          -> prefix path relatif ('' untuk root, '../' untuk sub-folder)
// $active_menu   -> 'dashboard' | 'barang' | 'pinjam' | 'homies' | 'staff' | 'laporan'
// $active_sub    -> nama sub-item aktif, opsional:
//                   'kategori' | 'masuk' | 'keluar' (menu barang)
//                   'lap_masuk' | 'lap_keluar' | 'lap_pinjam' (menu laporan)
$base = $base ?? '';
$active_menu = $active_menu ?? '';
$active_sub  = $active_sub ?? '';
$u = current_user();
$initial = strtoupper(substr($u['full_name'] ?? 'U', 0, 1));
?>
<aside class="sidebar">
    <div class="sidebar-top">
        <span>Gudang</span>
        <button class="burger" data-toggle="sidebar" aria-label="Toggle menu">&#9776;</button>
    </div>

    <div class="sidebar-user">
        <div class="avatar-circle"><?= e($initial) ?></div>
        <div>
            <div class="u-name"><?= e($u['full_name']) ?></div>
            <div class="u-role"><?= e(role_label()) ?></div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= $base ?>dashboard.php"><span class="icon">&#9635;</span> DashBoard</a>
            </li>

            <li>
                <div class="nav-parent <?= $active_menu === 'barang' ? 'active open' : '' ?>">
                    <span><span class="icon">&#128230;</span> Management Barang</span>
                    <span class="chevron">&#9660;</span>
                </div>
                <ul class="nav-sub <?= $active_menu === 'barang' ? 'open' : '' ?>">
                    <?php if (is_admin()): ?>
                    <li><a class="<?= $active_sub === 'kategori' ? 'active' : '' ?>" href="<?= $base ?>barang/kategori_barang.php">Kategori Barang</a></li>
                    <?php endif; ?>
                    <li><a class="<?= $active_sub === 'masuk' ? 'active' : '' ?>" href="<?= $base ?>barang/barang_masuk.php">Barang Masuk</a></li>
                    <li><a class="<?= $active_sub === 'keluar' ? 'active' : '' ?>" href="<?= $base ?>barang/barang_keluar.php">Barang Keluar</a></li>
                </ul>
            </li>

            <li class="nav-item <?= $active_menu === 'pinjam' ? 'active' : '' ?>">
                <a href="<?= $base ?>peminjaman/peminjaman.php"><span class="icon">&#128196;</span> Pinjam Barang</a>
            </li>

            <li class="nav-item <?= $active_menu === 'homies' ? 'active' : '' ?>">
                <a href="<?= $base ?>homies/menajemen_homies.php"><span class="icon">&#128101;</span> Management Homies</a>
            </li>

            <?php if (is_admin()): ?>
            <li class="nav-item <?= $active_menu === 'staff' ? 'active' : '' ?>">
                <a href="<?= $base ?>staff/management_staff.php"><span class="icon">&#128100;</span> Management Staff</a>
            </li>
            <?php endif; ?>

            <li>
                <div class="nav-parent <?= $active_menu === 'laporan' ? 'active open' : '' ?>">
                    <span><span class="icon">&#128203;</span> Laporan</span>
                    <span class="chevron">&#9660;</span>
                </div>
                <ul class="nav-sub <?= $active_menu === 'laporan' ? 'open' : '' ?>">
                    <li><a class="<?= $active_sub === 'lap_masuk' ? 'active' : '' ?>" href="<?= $base ?>laporan/laporan_masuk.php">Laporan Barang Masuk</a></li>
                    <li><a class="<?= $active_sub === 'lap_keluar' ? 'active' : '' ?>" href="<?= $base ?>laporan/laporan_keluar.php">Laporan Barang Keluar</a></li>
                    <li><a class="<?= $active_sub === 'lap_pinjam' ? 'active' : '' ?>" href="<?= $base ?>laporan/laporan_peminjaman.php">Laporan Peminjaman</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>
