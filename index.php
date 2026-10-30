<?php
require 'start.php';

// ─── Զամբյուղի քանակները (out-of-stock-ի համար) ───
$cartQty = [];
if (!empty($_SESSION['cart']->products)) {
    foreach ($_SESSION['cart']->products as $item) {
        $cartQty[$item['product']->name] = $item['quantity'];
    }
}

// ─── Որոնում ───
$search = trim($_GET['search'] ?? '');
$searchResults = [];
$searchCount = 0;

if ($search !== '') {
    $starts   = $search . '%';
    $contains = '%' . $search . '%';

    $stmt = $pdo->prepare("
        SELECT *,
            CASE
                WHEN name LIKE ? THEN 1
                WHEN name LIKE ? THEN 2
                WHEN description LIKE ? THEN 3
                ELSE 4
            END AS relevance
        FROM products
        WHERE name LIKE ? OR description LIKE ?
        ORDER BY relevance ASC, name ASC
    ");
    $stmt->execute([$starts, $contains, $contains, $contains, $contains]);
    $searchResults = $stmt->fetchAll();
    $searchCount = count($searchResults);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Իմ Խանութը</title>
</head>
<body>
    <?php $pageType = 'full'; require 'header.php'; ?>

    <?php if ($search !== ''): ?>
        <!-- ═══════════ ՈՐՈՆՄԱՆ ՌԵԺԻՄ ═══════════ -->
        <div class="category-block">
            <h2>«<?= htmlspecialchars($search) ?>» որոնման արդյունքները (<?= $searchCount ?>)</h2>

            <?php if ($searchCount === 0): ?>
                <div class="empty-cart">
                    <div class="empty-cart-icon">🔍</div>
                    <h2>Ոչինչ չի գտնվել</h2>
                    <p>«<?= htmlspecialchars($search) ?>» որոնման համար արդյունք չկա</p>
                    <a href="index.php" class="btn-shop">Վերադառնալ գլխավոր էջ</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($searchResults as $data): ?>
                        <?php
                        $product = new Product(
                            $data['id'],
                            $data['name'],
                            $data['stock'],
                            $data['price'],
                            $data['category'],
                            $data['discount'],
                            $data['description']
                        );
                        $qtyInCart = $cartQty[$product->name] ?? 0;
                        $available = $product->stock - $qtyInCart;
                        ?>
                        <div class="product <?= $available <= 0 ? 'out-of-stock' : '' ?>" data-available="<?= $available ?>">
                            <a href="product.php?id=<?= $product->id ?>" class="product-link">
                                <div class="product-image">
                                    <?php if ($product->hasSuperMassiveDiscount()): ?>
                                        <span class="hot-badge super">🔥 -<?= $product->getDiscountPercent() ?>%</span>
                                    <?php elseif ($product->hasMassiveDiscount()): ?>
                                        <span class="hot-badge">🔥 -<?= $product->getDiscountPercent() ?>%</span>
                                    <?php elseif ($product->discount > 0): ?>
                                        <span class="sale-badge">-<?= $product->getDiscountPercent() ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <h3><?= htmlspecialchars($product->name) ?></h3>
                                <p class="description"><?= htmlspecialchars($product->description) ?></p>
                                <p>Արժեքը՝ <?= number_format($product->price) ?> Դ</p>
                                <p>Զեղչ՝ <?= number_format($product->discount) ?> Դ</p>
                                <p>Առկա է <?= number_format($product->stock) ?> Հատ</p>
                                <p>Զեղչված արժեքը՝ <?= number_format($product->price - $product->discount) ?> Դ</p>
                            </a>
                            <button class="add-to-cart" data-name="<?= htmlspecialchars($product->name) ?>">
                                <?= $available <= 0 ? 'Սպառվել է' : 'Ավելացնել (🛒)' ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- ═══════════ ՍՈՎՈՐԱԿԱՆ ՌԵԺԻՄ ═══════════ -->

        <?php
        $allProducts = [];

        foreach ($products as $category => $items) {
            foreach ($items as $product) {
                $allProducts[] = $product;
            }
        }
        ?>

        <div class="category-block" data-category="all">
            <h2>Բոլորը</h2>

            <div class="products-grid">
                <?php foreach ($allProducts as $index => $product): ?>
                    <?php
                    $qtyInCart = $cartQty[$product->name] ?? 0;
                    $available = $product->stock - $qtyInCart;
                    ?>
                    <div class="product <?= $available <= 0 ? 'out-of-stock' : '' ?>" data-available="<?= $available ?>">
                        <a href="product.php?id=<?= $product->id ?>" class="product-link">
                            <div class="product-image">
                                <?php if ($product->hasSuperMassiveDiscount()): ?>
                                    <span class="hot-badge super">🔥 -<?= $product->getDiscountPercent() ?>%</span>
                                <?php elseif ($product->hasMassiveDiscount()): ?>
                                    <span class="hot-badge">🔥 -<?= $product->getDiscountPercent() ?>%</span>
                                <?php elseif ($product->discount > 0): ?>
                                    <span class="sale-badge">-<?= $product->getDiscountPercent() ?>%</span>
                                <?php endif; ?>
                            </div>
                            <h3><?= htmlspecialchars($product->name) ?></h3>
                            <p class="description"><?= htmlspecialchars($product->description) ?></p>
                            <p>Արժեքը՝ <?= number_format($product->price) ?> Դ</p>
                            <p>Զեղչ՝ <?= number_format($product->discount) ?> Դ</p>
                            <p>Առկա է <?= number_format($product->stock) ?> Հատ</p>
                            <p>Զեղչված արժեքը՝ <?= number_format($product->price - $product->discount) ?> Դ</p>
                        </a>
                        <button class="add-to-cart" data-name="<?= htmlspecialchars($product->name) ?>">
                            <?= $available <= 0 ? 'Սպառվել է' : 'Ավելացնել (🛒)' ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php foreach ($products as $category => $items): ?>
            <div class="category-block" data-category="<?= htmlspecialchars($category) ?>">
                <h2><?= htmlspecialchars($category) ?></h2>

                <div class="products-grid">
                    <?php foreach ($items as $index => $product): ?>
                        <?php
                        $qtyInCart = $cartQty[$product->name] ?? 0;
                        $available = $product->stock - $qtyInCart;
                        ?>
                        <div class="product <?= $available <= 0 ? 'out-of-stock' : '' ?>" data-available="<?= $available ?>">
                            <a href="product.php?id=<?= $product->id ?>" class="product-link">
                                <div class="product-image">
                                    <?php if ($product->hasSuperMassiveDiscount()): ?>
                                        <span class="hot-badge super">🔥 -<?= $product->getDiscountPercent() ?>%</span>
                                    <?php elseif ($product->hasMassiveDiscount()): ?>
                                        <span class="hot-badge">🔥 -<?= $product->getDiscountPercent() ?>%</span>
                                    <?php elseif ($product->discount > 0): ?>
                                        <span class="sale-badge">-<?= $product->getDiscountPercent() ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <h3><?= htmlspecialchars($product->name) ?></h3>
                                <p class="description"><?= htmlspecialchars($product->description) ?></p>
                                <p>Արժեքը՝ <?= number_format($product->price) ?> Դ</p>
                                <p>Զեղչ՝ <?= number_format($product->discount) ?> Դ</p>
                                <p>Առկա է <?= number_format($product->stock) ?> Հատ</p>
                                <p>Զեղչված արժեքը՝ <?= number_format($product->price - $product->discount) ?> Դ</p>
                            </a>
                            <button class="add-to-cart" data-name="<?= htmlspecialchars($product->name) ?>">
                                <?= $available <= 0 ? 'Սպառվել է' : 'Ավելացնել (🛒)' ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <a href="cart.php" class="cart-header">
        <span>🛒</span>
        <span class="cart-count"><?= count($_SESSION['cart']->products ?? []) ?></span>
    </a>

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