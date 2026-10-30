<?php
require_once 'admin-auth.php';
require_once 'start.php';

// Ստանում ենք id-ն URL-ից (?id=3)
$orderId = $_GET['id'] ?? null;

// Եթե id չկա → վերադարձրու admin.php
if ($orderId === null || !is_numeric($orderId)) {
    header('Location: admin.php');
    exit;
}

// Կարդում ենք պատվերը DB-ից
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

// Եթե պատվերը չկա → վերադարձրու admin.php
if (!$order) {
    header('Location: admin.php');
    exit;
}

// Կարդում ենք ապրանքները
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Պատվեր <?= htmlspecialchars($order['order_number']) ?></title>
</head>
<body>
    <?php
$backLink = ['url' => 'admin.php', 'label' => 'Բոլոր պատվերները'];
require 'admin-header.php';
?>

    <div class="admin-container">
        <h1 class="admin-page-title">
            Պատվեր՝ <?= htmlspecialchars($order['order_number']) ?>
        </h1>

        <div class="admin-order-box">
            <h2>👤 Հաճախորդի տվյալները</h2>

            <div class="admin-order-row">
                <span class="label">Անուն</span>
                <span class="value"><?= htmlspecialchars($order['name']) ?></span>
            </div>
            <div class="admin-order-row">
                <span class="label">Հեռախոս</span>
                <span class="value"><?= htmlspecialchars($order['phone']) ?></span>
            </div>
            <div class="admin-order-row">
                <span class="label">Հասցե</span>
                <span class="value"><?= htmlspecialchars($order['address']) ?></span>
            </div>
            <div class="admin-order-row">
                <span class="label">Ամսաթիվ</span>
                <span class="value"><?= htmlspecialchars($order['created_at']) ?></span>
            </div>
        </div>

        <div class="admin-order-box">
            <h2>📦 Գնված ապրանքները</h2>

            <?php foreach ($items as $item): ?>
                <div class="admin-order-row">
                    <span class="label">
                        <?= htmlspecialchars($item['product_name']) ?>
                        × <?= $item['quantity'] ?>
                        (<?= number_format($item['price']) ?> Դ)
                    </span>
                    <span class="value">
                        <?= number_format(($item['price'] - $item['discount']) * $item['quantity']) ?> Դ
                    </span>
                </div>
            <?php endforeach; ?>

            <div class="admin-order-row" style="border-top: 1px solid var(--border); margin-top: 10px; padding-top: 15px;">
                <span class="label" style="color: var(--text-main); font-weight: 700; font-size: 16px;">Ընդհանուր</span>
                <span class="value" style="color: var(--success); font-size: 18px;">
                    <?= number_format($order['total']) ?> Դ
                </span>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Իմ Խանութը։ Ադմին վահանակ</p>
        </div>
    </footer>
</body>
</html>