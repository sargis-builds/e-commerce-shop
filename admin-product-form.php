<?php
require_once 'admin-auth.php';
require_once 'start.php';

// Ստուգում ենք՝ add է, թե edit
$productId = $_GET['id'] ?? null;
$isEdit = $productId !== null && is_numeric($productId);

$product = [
    'id'          => '',
    'name'        => '',
    'category'    => '',
    'price'       => '',
    'discount'    => 0,
    'stock'       => '',
    'description' => ''
];

// Եթե edit է → բեռնում ենք ապրանքը DB-ից
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $dbProduct = $stmt->fetch();

    if (!$dbProduct) {
        header('Location: admin-products.php');
        exit;
    }

    $product = $dbProduct;
}

$pageTitle = $isEdit ? 'Խմբագրել ապրանք' : 'Ավելացնել ապրանք';
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title><?= $pageTitle ?> — Իմ Խանութը</title>
</head>
<body>
    <?php
    $backLink = ['url' => 'admin-products.php', 'label' => 'Ապրանքներ'];
    require 'admin-header.php';
    ?>

    <div class="admin-container">
        <h1 class="admin-page-title"><?= $pageTitle ?></h1>

        <div class="admin-form-wrap">
            <form method="post" action="admin-product-save.php" class="admin-form">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <?php endif; ?>

                <!-- Անուն -->
                <div class="form-field">
                    <label>Ապրանքի անուն *</label>
                    <input type="text" name="name" required
                           value="<?= htmlspecialchars($product['name']) ?>"
                           placeholder="Օրինակ՝ iPhone 15">
                </div>

                <!-- Կատեգորիա -->
                <div class="form-field">
                    <label>Կատեգորիա *</label>
                    <input type="text" name="category" required
                           value="<?= htmlspecialchars($product['category']) ?>"
                           placeholder="Օրինակ՝ Էլեկտրոնիկա">
                    <p class="field-hint">Կատեգորիաները համընկնելու համար գրիր նույն ձևով (մեծատառ, առանց բացատի)</p>
                </div>

                <!-- Գին և Զեղչ -->
                <div class="form-row">
                    <div class="form-field">
                        <label>Գին (Դ) *</label>
                        <input type="number" name="price" required min="0" step="1"
                               value="<?= htmlspecialchars($product['price']) ?>"
                               placeholder="0">
                    </div>
                    <div class="form-field">
                        <label>Զեղչ (Դ)</label>
                        <input type="number" name="discount" min="0" step="1"
                               value="<?= htmlspecialchars($product['discount']) ?>"
                               placeholder="0">
                    </div>
                </div>

                <!-- Քանակ -->
                <div class="form-field">
                    <label>Քանակ պահեստում *</label>
                    <input type="number" name="stock" required min="0" step="1"
                           value="<?= htmlspecialchars($product['stock']) ?>"
                           placeholder="0">
                </div>

                <!-- Նկարագրություն -->
                <div class="form-field">
                    <label>Նկարագրություն</label>
                    <textarea name="description" rows="4"
                              placeholder="Ապրանքի մասին մանրամասներ"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <!-- Կոճակներ -->
                <div class="admin-form-actions">
                    <button type="submit" class="admin-save-btn">
                        <?= $isEdit ? '💾 Պահպանել փոփոխությունները' : '✅ Ավելացնել ապրանք' ?>
                    </button>
                    <a href="admin-products.php" class="admin-cancel-btn">Չեղարկել</a>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Իմ Խանութը։ Ադմին վահանակ</p>
        </div>
    </footer>
</body>
</html>