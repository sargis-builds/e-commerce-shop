<?php
require_once 'start.php';

$pageType = $pageType ?? 'full';
?>
<header class="top-header">
    <div class="left-group">
        <a href="index.php" class="logo">
            <span class="logo-text">🛒 Իմ Խանութը</span>
            <span class="logo-line"></span>
        </a>
    </div>

    <?php if ($pageType === 'full'): ?>
        <form method="get" action="index.php" class="center-group">
    <div class="cat">
        <select id="categoryFilter">
            <option value="all">Բոլորը</option>
            <?php foreach (getAllCategories() as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>">
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="search">
        <input type="text" name="search" placeholder="Որոնել..." 
       autocomplete="off"
       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </div>
    <button type="submit">🔍</button>
</form>
    <?php endif; ?>

        <div class="right-group">
        <?php if ($pageType === 'full' || basename($_SERVER['PHP_SELF']) === 'cart.php'): ?>
            <a href="history.php" class="header-link history-link">
                📜 <span class="history-text">Գնումներ</span>
            </a>
        <?php endif; ?>

        <div class="lang">
            <select>
                <option>🇦🇲</option>
                <option>🇬🇧</option>
                <option>🇷🇺</option>
            </select>
        </div>

        <!-- ՆՈՐ ԵՆԹԱԽՈՒՄԲԸ -->
        <div class="user-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-greeting">
                    <span class="greeting-text">Ողջույն, </span><?= htmlspecialchars($_SESSION['user_name']) ?>
                </span>
                <a href="user-logout.php" class="header-link">Ելք</a>
            <?php else: ?>
                <a href="user-login.php" class="header-link">Մուտք</a>
            <?php endif; ?>
        </div>
        <!-- ԵՆԹԱԽՈՒՄԲԸ ԱՎԱՐՏՎԵՑ -->
    </div>
</header>