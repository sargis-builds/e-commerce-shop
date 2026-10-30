<?php
require_once 'admin-auth.php';
require_once 'start.php';

// Ստանում ենք բոլոր ապրանքները, վերջինները՝ սկզբում
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$allProducts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Ապրանքներ — Իմ Խանութը</title>
</head>
<body>
    <?php require 'admin-header.php'; ?>

    <div class="admin-container">
        <div class="admin-page-head">
            <h1 class="admin-page-title">Ապրանքներ (<?= count($allProducts) ?>)</h1>
            <a href="admin-product-form.php" class="admin-add-btn">
                + Ավելացնել ապրանք
            </a>
        </div>

        <?php if (empty($allProducts)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">📦</div>
                <h2>Դեռ ապրանքներ չկան</h2>
                <p>Սեղմիր «Ավելացնել ապրանք» կոճակը՝ առաջինը ստեղծելու համար</p>
                <a href="admin-product-form.php" class="btn-shop">+ Ավելացնել ապրանք</a>
            </div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ապրանք</th>
                            <th>Կատեգորիա</th>
                            <th>Գին</th>
                            <th>Զեղչ</th>
                            <th>Քանակ</th>
                            <th>Գործողություններ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allProducts as $p): ?>
                            <tr>
                                <td>#<?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= htmlspecialchars($p['category']) ?></td>
                                <td><?= number_format($p['price']) ?> Դ</td>
                                <td><?= $p['discount'] > 0 ? number_format($p['discount']) . ' Դ' : '—' ?></td>
                                <td>
                                    <span class="<?= $p['stock'] <= 0 ? 'stock-no' : '' ?>">
                                        <?= $p['stock'] ?>
                                    </span>
                                </td>
                                <td class="admin-actions">
                                    <a href="admin-product-form.php?id=<?= $p['id'] ?>" 
                                       class="admin-action-edit">
                                        ✏️ Խմբագրել
                                    </a>
                                    <a href="admin-product-delete.php?id=<?= $p['id'] ?>" 
                                       class="admin-action-delete"
                                       onclick="return confirm('Համոզվա՞ծ ես, որ ուզում ես ջնջել «<?= htmlspecialchars($p['name']) ?>»-ը։');">
                                        🗑️ Ջնջել
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Իմ Խանութը։ Ադմին վահանակ</p>
        </div>
    </footer>
</body>
</html>