<?php
require_once 'admin-auth.php';
require_once 'start.php';

// Ստանում ենք բոլոր պատվերները, նորերը՝ սկզբում
$stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <tytle>
        <h1 class="admin-page-title">Պատվերներ</h1>
    </tytle>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Ադմին — պատվերներ</title>
</head>
<body>
    <?php require 'admin-header.php'; ?>

    <div class="admin-container">
        <?php if (empty($orders)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">📭</div>
                <h2>Դեռ պատվերներ չկան</h2>
            </div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Համար</th>
                            <th>Անուն</th>
                            <th>Հեռախոս</th>
                            <th>Հասցե</th>
                            <th>Գումար</th>
                            <th>Ամսաթիվ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="order-id"><?= htmlspecialchars($o['order_number']) ?></td>
                                <td><?= htmlspecialchars($o['name']) ?></td>
                                <td><?= htmlspecialchars($o['phone']) ?></td>
                                <td><?= htmlspecialchars($o['address']) ?></td>
                                <td class="order-total"><?= number_format($o['total']) ?> Դ</td>
                                <td><?= htmlspecialchars($o['created_at']) ?></td>
                                <td>
                                    <a href="admin-order.php?id=<?= $o['id'] ?>" class="row-link">
                                        Տեսնել →
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