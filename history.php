<?php
require 'start.php';

// Ստուգում ենք՝ արդյոք օգտատերը մուտք է գործել (Login)
$userId = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Գնումների Պատմություն — Իմ Խանութը</title>
</head>
<body>
    <?php $pageType = 'minimal'; require 'header.php'; ?>

    <div class="cart-title" style="margin: 30px 28px 20px;">
        <h1>Գնումների Պատմություն</h1>
    </div>

    <?php if (!$userId): ?>
        <!-- ═══ ԵԹԵ ՄՈՒՏՔ ՉԻ ԵՂԵԼ (ՀՅՈՒՐ) ═══ -->
        <div class="empty-cart">
            <div class="empty-cart-icon">🔒</div>
            <h2>Մուտք գործեք համակարգ</h2>
            <p>Ձեր գնումների պատմությունը տեսնելու համար անհրաժեշտ է մուտք գործել Ձեր անձնական էջ։</p>
            <a href="user-login.php" class="btn-shop" style="margin-top: 10px;">Մուտք գործել / Գրանցվել</a>
        </div>

    <?php else: ?>
        <?php
        // Բազայից վերցնում ենք տվյալ օգտատիրոջ բոլոր պատվերները
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();
        ?>

        <?php if (empty($orders)): ?>
            <!-- ═══ ԵԹԵ ՄՈՒՏՔ Է ԵՂԵԼ, ԲԱՅՑ ՊԱՏՎԵՐ ՉԿԱՆ ═══ -->
            <div class="empty-cart">
                <div class="empty-cart-icon">📦</div>
                <h2>Դուք դեռ գնումներ չեք կատարել</h2>
                <p>Ձեր պատմությունը դատարկ է։ Ժամանակն է գտնել ինչ-որ հետաքրքիր բան մեր խանութում։</p>
                <a href="index.php" class="btn-shop" style="margin-top: 10px;">Սկսել գնումները</a>
            </div>

        <?php else: ?>
            <!-- ═══ ԵԹԵ ԿԱՆ ՊԱՏՎԵՐՆԵՐ ═══ -->
            <?php foreach ($orders as $order): ?>
                <div class="cart-summary" style="text-align: left; margin-bottom: 20px;">
                    <!-- Պատվերի գլխիկը -->
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h3 style="margin: 0; color: var(--accent);">Պատվեր՝ <?= htmlspecialchars($order['order_number']) ?></h3>
                            <p style="margin: 5px 0 0 0; font-size: 13px; color: var(--text-muted);">Ամսաթիվ՝ <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                        </div>
                        <div style="text-align: right;">
                            <p style="margin: 0; font-size: 14px; color: var(--text-muted);">Ընդհանուր գումար</p>
                            <strong style="font-size: 18px; color: var(--text-main);"><?= number_format($order['total']) ?> Դ</strong>
                        </div>
                    </div>

                    <!-- Պատվերի ապրանքները -->
                    <h4 style="margin: 0 0 10px 0; font-size: 14px; color: var(--text-muted);">Ապրանքներ՝</h4>
                    
                    <?php
                    // Այս պատվերի համար վերցնում ենք ապրանքները order_items աղյուսակից
                    $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                    $stmtItems->execute([$order['id']]);
                    $items = $stmtItems->fetchAll();
                    ?>

                    <div style="background: var(--bg-elevated); border-radius: var(--radius); padding: 10px 15px;">
                        <?php foreach ($items as $item): ?>
                            <div class="checkout-line" style="border-bottom: 1px solid var(--border-soft); font-size: 13px;">
                                <div>
                                    <span style="color: var(--text-main); font-weight: 500;"><?= htmlspecialchars($item['product_name']) ?></span>
                                    <span style="color: var(--text-muted); margin-left: 5px;">x <?= $item['quantity'] ?></span>
                                </div>
                                <div>
                                    <?php if ($item['discount'] > 0): ?>
                                        <span style="color: var(--text-dim); text-decoration: line-through; font-size: 11px; margin-right: 5px;"><?= number_format($item['price']) ?> Դ</span>
                                    <?php endif; ?>
                                    <span style="color: var(--accent); font-weight: 600;"><?= number_format(($item['price'] - $item['discount']) * $item['quantity']) ?> Դ</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="index.php" class="btn-shop">Վերադառնալ գնումներին</a>
            </div>
        <?php endif; ?>
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