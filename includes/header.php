<?php
// Variabel yang diharapkan sudah di-set oleh halaman pemanggil sebelum include:
// $base            -> '' atau '../'
// $page_title      -> judul tab browser
// $page_header     -> judul yang tampil di header berwarna (opsional, default = $page_title)
// $page_icon       -> HTML entity ikon di sebelah judul (opsional)
// $header_variant  -> 'gray' (default) atau 'purple'
// $active_menu / $active_sub -> dipakai sidebar.php
// $header_actions  -> HTML tambahan (misal tombol "+ Tambah") di kanan header (opsional)

$base = $base ?? '';
$page_header = $page_header ?? ($page_title ?? '');
$page_icon = $page_icon ?? '&#9635;';
$header_variant = $header_variant ?? 'gray';
$u = current_user();
$initial = strtoupper(substr($u['full_name'] ?? 'U', 0, 1));
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? 'Gudang') ?></title>
<link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<div class="app-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header <?= $header_variant === 'purple' ? 'purple' : '' ?>">
            <div class="page-header-row">
                <div style="display:flex; align-items:center; gap:16px;">
                    <button class="burger-desktop" data-toggle="sidebar" aria-label="Toggle menu">&#9776;</button>
                    <h2><span><?= $page_icon ?></span> <?= e($page_header) ?></h2>
                </div>

                <div style="display:flex; align-items:center; gap:14px;">
                    <?php if (!empty($header_actions)) echo $header_actions; ?>
                    <div class="topbar-user">
                        <button class="topbar-user-btn">
                            <div class="avatar-circle"><?= e($initial) ?></div>
                            <span>&#9660;</span>
                        </button>
                        <div class="topbar-dropdown">
                            <div class="dd-user">
                                <div class="avatar-circle"><?= e($initial) ?></div>
                                <div>
                                    <div class="u-name"><?= e($u['full_name']) ?></div>
                                    <div class="u-role"><?= e(role_label()) ?></div>
                                </div>
                            </div>
                            <a href="<?= $base ?>ubah_password.php">&#128273; Ubah Password</a>
                            <a href="<?= $base ?>logout.php" class="danger">&#8630; Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="progress-bar"><span></span></div>
        </div>

        <div class="content-area">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
            <?php endif; ?>
