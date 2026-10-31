<?php
require 'start.php';

// ─── Կարդում ենք id-ն URL-ից ───
$productId = $_GET['id'] ?? null;

// Եթե id չկա կամ թիվ չէ → գլխավոր էջ
if ($productId === null || !is_numeric($productId)) {
    header('Location: index.php');
    exit;
}

// ─── Վերցնում ենք ապրանքը DB-ից ───
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$data = $stmt->fetch();

// Եթե ապրանք չկա → գլխավոր էջ
if (!$data) {
    header('Location: index.php');
    exit;
}

// ─── Ստեղծում ենք Product object ───
$product = new Product(
    $pdo,
    $data['id'],
    $data['name'],
    $data['stock'],
    $data['price'],
    $data['category'],
    $data['discount'],
    $data['description']
);

// ─── Out-of-stock-ի հաշվարկ ───
$cartQty = 0;
if (!empty($_SESSION['cart']->products)) {
    foreach ($_SESSION['cart']->products as $item) {
        if ($item['product']->id === $product->id) {
            $cartQty = $item['quantity'];
            break;
        }
    }
}
$available = $product->stock - $cartQty;

// ─── Նման ապրանքներ (նույն կատեգորիայից, առանց ընթացիկի) ───
$stmt = $pdo->prepare("
    SELECT * FROM products
    WHERE category = ? AND id != ?
    ORDER BY RAND()
    LIMIT 4
");
$stmt->execute([$product->category, $product->id]);
$relatedData = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title><?= htmlspecialchars($product->name) ?> — Իմ Խանութը</title>
</head>
<body>
    <?php $pageType = 'minimal'; require 'header.php'; ?>

    <div class="product-page">
        <!-- ═══ Ձախ՝ նկար ═══ -->
        <div class="product-page-image">
            <?php if ($product->hasSuperMassiveDiscount()): ?>
                <span class="hot-badge super">🔥 -<?= $product->getDiscountPercent() ?>%</span>
            <?php elseif ($product->hasMassiveDiscount()): ?>
                <span class="hot-badge">🔥 -<?= $product->getDiscountPercent() ?>%</span>
            <?php elseif ($product->discount > 0): ?>
                <span class="sale-badge">-<?= $product->getDiscountPercent() ?>%</span>
            <?php endif; ?>
        </div>

        <!-- ═══ Աջ՝ տեղեկություն ═══ -->
        <div class="product-page-info">
            <a href="index.php?search=<?= urlencode($product->category) ?>" class="product-page-cat">
                <?= htmlspecialchars($product->category) ?>
            </a>

            <h1 class="product-page-name"><?= htmlspecialchars($product->name) ?></h1>

            <p class="product-page-desc"><?= htmlspecialchars($product->description) ?></p>

            <div class="product-page-price">
                <?php if ($product->discount > 0): ?>
                    <span class="price-old"><?= number_format($product->price) ?> Դ</span>
                    <span class="price-new"><?= number_format($product->price - $product->discount) ?> Դ</span>
                    <span class="price-save">Խնայում եք <?= number_format($product->discount) ?> Դ</span>
                <?php else: ?>
                    <span class="price-new"><?= number_format($product->price) ?> Դ</span>
                <?php endif; ?>
            </div>

            <div class="product-page-stock">
                <?php if ($available > 0): ?>
                    <span class="stock-ok">✓ Առկա է <?= $available ?> հատ</span>
                <?php else: ?>
                    <span class="stock-no">✕ Սպառված է</span>
                <?php endif; ?>
            </div>

            <?php if ($available > 0): ?>
                <div class="product-page-actions">
                    <div class="qty-selector">
                        <div class="qty-controls">
                            <button type="button" class="qty-btn" id="qtyMinus">−</button>
                            <input type="text" id="qtyInput" value="1" 
                                   data-max="<?= $available ?>" readonly>
                            <button type="button" class="qty-btn" id="qtyPlus">+</button>
                        </div>
                    </div>
                    <button 
                        class="add-to-cart product-page-btn"
                        data-id="<?= $product->id ?>"
                        id="addBtn"
                    >
                        🛒 Ավելացնել զամբյուղ
                    </button>
                </div>
            <?php else: ?>
                <button class="product-page-btn" disabled>Սպառվել է</button>
            <?php endif; ?>

            <a href="cart.php" class="product-page-cart-link">
                Անցնել զամբյուղ →
            </a>
        </div>
    </div>

    <!-- ═══ Նման ապրանքներ ═══ -->
    <?php if (!empty($relatedData)): ?>
        <div class="category-block">
            <h2>Նման ապրանքներ</h2>
            <div class="products-grid">
                <?php foreach ($relatedData as $rdata): ?>
                    <?php
                    $rp = new Product(
                        $pdo,
                        $rdata['id'], $rdata['name'], $rdata['stock'],
                        $rdata['price'], $rdata['category'],
                        $rdata['discount'], $rdata['description']
                    );
                    $rCartQty = 0;
                    if (!empty($_SESSION['cart']->products)) {
                        foreach ($_SESSION['cart']->products as $item) {
                            if ($item['product']->id === $rp->id) {
                                $rCartQty = $item['quantity'];
                                break;
                            }
                        }
                    }
                    $rAvail = $rp->stock - $rCartQty;
                    ?>
                    <div class="product-link">
                        <div class="product <?= $rAvail <= 0 ? 'out-of-stock' : '' ?>" data-available="<?= $rAvail ?>">
                            <a href="product.php?id=<?= $rp->id ?>" style="text-decoration: none; color: inherit; display: block;">
                                <div class="product-image">
                                    <?php if ($rp->hasSuperMassiveDiscount()): ?>
                                        <span class="hot-badge super">🔥 -<?= $rp->getDiscountPercent() ?>%</span>
                                    <?php elseif ($rp->hasMassiveDiscount()): ?>
                                        <span class="hot-badge">🔥 -<?= $rp->getDiscountPercent() ?>%</span>
                                    <?php elseif ($rp->discount > 0): ?>
                                        <span class="sale-badge">-<?= $rp->getDiscountPercent() ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <h3><?= htmlspecialchars($rp->name) ?></h3>
                            </a>
                            <p class="description"><?= htmlspecialchars($rp->description) ?></p>
                            <p>Արժեքը՝ <?= number_format($rp->price) ?> Դ</p>
                            <p>Զեղչ՝ <?= number_format($rp->discount) ?> Դ</p>
                            <p>Առկա է <?= number_format($rp->stock) ?> Հատ</p>
                            <p>Զեղչված արժեքը՝ <?= number_format($rp->price - $rp->discount) ?> Դ</p>
                            <button class="add-to-cart" data-id="<?= $rp->id ?>">
    <?= $rp->stock <= 0 ? 'Սպառվել է' : 'Ավելացնել (🛒)' ?>
</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

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