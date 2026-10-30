<?php
require 'start.php';
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Զամբյուղ — Իմ Խանութը</title>
</head>
<body>
    <?php $pageType = 'minimal'; require 'header.php'; ?>

    <h1 class="cart-title">Ձեր Զամբյուղը</h1>

    <div id="cart-content">
        <?php if (empty($cart->products)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Ձեր զամբյուղը դատարկ է</h2>
                <p>Անցեք գլխավոր էջ գնումներ սկսելու համար</p>
                <a href="index.php" class="btn-shop">Սկսել գնումները</a>
            </div>
        <?php else: ?>
            <?php foreach ($cart->products as $index => $item): ?>
                <?php $product = $item['product']; ?>
                <?php $quantity = $item['quantity']; ?>
                <div class="cart-item" data-name="<?= htmlspecialchars($product->name) ?>">
                    <div class="cart-item-image">
                        <?php if ($product->discount > 0): ?>
                            <span class="discount-badge">-<?= $product->discount ?> Դ</span>
                        <?php endif; ?>
                    </div>
                    <div class="cart-item-info">
                        <h3><?= htmlspecialchars($product->name) ?></h3>
                        <p><?= htmlspecialchars($product->category) ?></p>
                        <p class="item-quantity">Քանակը՝ <span class="qty-value"><?= $quantity ?></span> հատ</p>
                    </div>
                    <div class="cart-item-actions">
                        <div class="cart-item-price">
                            <span class="old-price"><?= number_format($product->price * $quantity) ?> Դ</span>
                            <span class="new-price"><?= number_format(($product->price - $product->discount) * $quantity) ?> Դ</span>
                        </div>
                        <div class="cart-item-buttons">
                            <button type="button" class="del-btn" data-name="<?= htmlspecialchars($product->name) ?>">Ջնջ․</button>
                            <button type="button" class="minus-btn" data-name="<?= htmlspecialchars($product->name) ?>">−</button>
                            <button type="button" class="plus-btn" data-name="<?= htmlspecialchars($product->name) ?>">+</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-summary">
                <p class="old-price">Արժեքը՝ <?= number_format($cart->getTotalDefault()) ?> Դ</p>
                <p class="saved">🎉 Իմ զեղչը՝ -<?= number_format($cart->getTotalDiscount()) ?> Դ</p>
                <h3>Ընդհանուր՝ <?= number_format($cart->getTotal()) ?> Դ</h3>
                <a href="index.php" class="btn-shop">Շարունակել գնումները</a>
                <a href="checkout.php" class="btn-checkout">Անցնել վճարման</a>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>🛒 Իմ Խանութը</h4>
                <p>Որակյալ ապրանքներ, արագ առաքում</p>
            </div>
            <div class="footer-section">
                <h4>Հղումներ</h4>
                <p>Մեր մասին</p>
                <p>Կապ</p>
                <p>Առաքում</p>
            </div>
            <div class="footer-section">
                <h4>Կապ</h4>
                <p>📞 +374 XX XXX XXX</p>
                <p>✉️ info@shop.am</p>
                <p>📍 Երևան, Հայաստան</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 Իմ Խանութը։ Բոլոր իրավունքները պաշտպանված են։</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>