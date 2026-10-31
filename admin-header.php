<?php
require_once 'start.php';

$adminName = $_SESSION['admin_username'] ?? 'Ադմին';
$backLink = $backLink ?? null;

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<header class="top-header admin-header">
    <div class="left-group">
        <a href="admin.php" class="logo">
            <span class="logo-text">🛒 Իմ Խանութը</span>
            <span class="logo-line"></span>
        </a>

        <nav class="admin-nav">
            <a href="admin.php" 
               class="admin-nav-link <?= $currentPage === 'admin.php' ? 'active' : '' ?>">
                📋 Պատվերներ
            </a>
            <a href="admin-products.php" 
               class="admin-nav-link <?= in_array($currentPage, ['admin-products.php', 'admin-product-form.php']) ? 'active' : '' ?>">
                📦 Ապրանքներ
            </a>
        </nav>
    </div>

    <div class="admin-header-title">
        <?php if ($backLink): ?>
            <a href="<?= htmlspecialchars($backLink['url']) ?>" class="admin-back-link">
                ← <?= htmlspecialchars($backLink['label']) ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="right-group">
        <div class="lang">
            <select>
                <option>🇦🇲</option>
                <option>🇬🇧</option>
                <option>🇷🇺</option>
            </select>
        </div>
        <span class="admin-greeting">Ողջույն, <?= htmlspecialchars($adminName) ?></span>
        <a href="admin-logout.php" class="admin-logout-btn">Ելք</a>
    </div>
</header>